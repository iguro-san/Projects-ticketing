<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseEventController;
use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Notifications\EventCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends BaseEventController
{
    /**
     * ==========================================
     * OVERRIDE METHOD #1 - POLYMORPHISM
     * ==========================================
     * Admin melihat SEMUA event (tanpa filter panitia_id)
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getUserQuery(Request $request)
    {
        return Event::query();
    }

    /**
     * ==========================================
     * OVERRIDE METHOD #2 - POLYMORPHISM
     * ==========================================
     * Admin view membutuhkan data tambahan: $categories dan $panitia
     * untuk dropdown filter di halaman admin.
     * 
     * @param mixed $events
     * @param Request $request
     * @return \Illuminate\View\View
     */
    protected function renderIndexView($events, Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.index', compact('events', 'categories', 'panitia'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.create', compact('categories', 'panitia'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'panitia_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,active,cancelled',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')
                ->store('posters/' . date('Y/m'), 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event)
    {
        $event->load(['category', 'panitia', 'ticketTypes', 'registrations']);
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $event->load(['category', 'panitia', 'ticketTypes']);
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.edit', compact('event', 'categories', 'panitia'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'panitia_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster) {
                Storage::disk('public')->delete($event->poster);
            }
            $validated['poster'] = $request->file('poster')
                ->store('posters/' . date('Y/m'), 'public');
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diupdate.');
    }

    public function destroy(Event $event)
    {
        if ($event->registrations()->count() > 0) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah ada peserta terdaftar.');
        }

        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
        }

        $event->ticketTypes()->delete();
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    public function approve(Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->with('error', 'Hanya event draft yang bisa disetujui.');
        }
        
        $event->approve(auth()->id());
        
        return back()->with('success', "Event \"{$event->title}\" telah DISETUJUI dan sekarang AKTIF!");
    }

    public function reject(Request $request, Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->with('error', 'Hanya event draft yang bisa ditolak.');
        }
        
        $event->reject($request->reason ?? 'Ditolak oleh admin');
        
        return back()->with('success', "Event \"{$event->title}\" telah DITOLAK.");
    }

    /**
     * ==========================================
     * BATALKAN EVENT - HANYA ADMIN
     * ==========================================
     * Semua peserta berbayar akan mendapatkan refund dalam 1 bulan
     */
    public function cancelEvent(Request $request, Event $event)
    {
        // Hanya admin yang bisa membatalkan event
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Cek status event
        if ($event->status === 'cancelled') {
            return back()->with('error', 'Event sudah dibatalkan.');
        }

        // Cek apakah event sudah lewat
        if ($event->event_date < now()) {
            return back()->with('error', 'Event yang sudah lewat tidak dapat dibatalkan.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($event, $request) {
            // Update status event
            $event->update([
                'status' => 'cancelled',
                'suspension_status' => 'cancelled',
            ]);

            // Proses refund untuk semua pendaftaran berbayar
            $registrations = $event->registrations()
                ->where('payment_status', 'paid')
                ->get();

            foreach ($registrations as $reg) {
                // Cek apakah tiket berbayar
                $ticketType = $reg->ticketType;
                if ($ticketType && $ticketType->price > 0) {
                    $reg->update([
                        'refund_status' => 'processing',
                        'refund_reason' => 'Event dibatalkan oleh admin' . ($request->reason ? ': ' . $request->reason : ''),
                        'refund_requested_at' => now(),
                    ]);

                    // Kirim notifikasi refund ke user
                    $user = $reg->user;
                    if ($user) {
                        $user->notify(new \App\Notifications\RefundInitiated($reg));
                        $user->notify(new EventCancelled($event, $reg));
                    }
                } else {
                    // Untuk tiket gratis, tidak ada refund
                    $reg->update([
                        'refund_status' => 'completed',
                        'refund_notes' => 'Tiket gratis - tidak ada pengembalian dana',
                    ]);
                    
                    // Tetap kirim notifikasi event dibatalkan
                    $user = $reg->user;
                    if ($user) {
                        $user->notify(new EventCancelled($event, $reg));
                    }
                }
            }
        });

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibatalkan. Peserta berbayar akan mendapat refund dalam waktu 1 bulan.');
    }

    /**
     * ==========================================
     * PROSES REFUND - HANYA ADMIN
     * ==========================================
     */
    public function processRefund(Request $request, Registration $registration)
    {
        // Hanya admin yang bisa memproses refund
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'refund_bank' => 'required_if:action,approve|in:BCA,Mandiri,BRI,BNI',
            'refund_account_name' => 'required_if:action,approve|string|max:255',
            'refund_account_number' => 'required_if:action,approve|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            $registration->update([
                'refund_status' => 'completed',
                'refund_processed_at' => now(),
                'refund_notes' => $request->notes,
                'refund_bank' => $request->refund_bank,
                'refund_account_name' => $request->refund_account_name,
                'refund_account_number' => $request->refund_account_number,
            ]);

            // Kirim notifikasi refund selesai
            if ($registration->user) {
                $registration->user->notify(new \App\Notifications\RefundProcessed($registration));
            }

            return back()->with('success', 'Refund berhasil diproses dan dikirim ke rekening peserta.');
        } else {
            $registration->update([
                'refund_status' => 'rejected',
                'refund_notes' => $request->notes ?? 'Refund ditolak oleh admin',
            ]);

            return back()->with('success', 'Refund ditolak.');
        }
    }
}