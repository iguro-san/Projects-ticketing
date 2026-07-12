<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseEventController;
use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
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
     * BATALKAN EVENT - OTOMATIS BUAT REFUND
     * ==========================================
     * Semua peserta berbayar otomatis dibuatkan refund pending
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

        // ==========================================
        // DEKLARASIKAN VARIABLE DI LUAR CLOSURE
        // ==========================================
        $refundCount = 0;

        DB::transaction(function () use ($event, $request, &$refundCount) {
            // Update status event
            $event->update([
                'status' => 'cancelled',
                'suspension_status' => 'cancelled',
            ]);

            // ==========================================
            // OTOMATIS: BUAT REFUND UNTUK SEMUA PESERTA BAYAR
            // ==========================================
            $registrations = Registration::where('event_id', $event->id)
                ->where('payment_status', 'paid')
                ->get();

            foreach ($registrations as $reg) {
                // Cek apakah tiket berbayar
                $ticketType = $reg->ticketType;
                if ($ticketType && $ticketType->price > 0) {
                    // ==========================================
                    // LANGSUNG BUAT REFUND STATUS = PENDING
                    // Data bank diambil dari data pembayaran
                    // ==========================================
                    $reg->update([
                        'refund_status' => 'pending',
                        'refund_requested_at' => now(),
                        'refund_reason' => 'Event dibatalkan oleh admin' . ($request->reason ? ': ' . $request->reason : ''),
                        'refund_amount' => $reg->amount_paid ?? $ticketType->price,
                        'refund_bank' => $reg->payment_method ?? null,
                        'refund_account_name' => $reg->sender_name ?? null,
                        'refund_account_number' => $reg->sender_account ?? null,
                    ]);

                    $refundCount++;

                    // Kirim notifikasi refund ke user
                    if ($reg->user) {
                        $reg->user->notify(
                            'refund_initiated',
                            'Refund Otomatis Dibuat',
                            "Event '{$event->title}' dibatalkan. Refund sebesar Rp " . number_format($reg->refund_amount, 0, ',', '.') . " sedang diproses.",
                            ['registration_id' => $reg->id]
                        );
                    }
                } else {
                    // Untuk tiket gratis, tidak ada refund
                    $reg->update([
                        'refund_status' => 'completed',
                        'refund_notes' => 'Tiket gratis - tidak ada pengembalian dana',
                    ]);
                }
            }
        });

        return redirect()->route('admin.events.index')
            ->with('success', "Event berhasil dibatalkan. {$refundCount} refund otomatis dibuat untuk peserta berbayar. Silakan proses refund di halaman Refund.");
    }
}