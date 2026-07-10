<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\BaseEventController;
use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends BaseEventController
{
    /**
     * ==========================================
     * OVERRIDE METHOD #1 - POLYMORPHISM
     * ==========================================
     * Panitia hanya melihat event MILIKNYA SENDIRI (filter panitia_id)
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getUserQuery(Request $request)
    {
        return Event::where('panitia_id', auth()->id());
    }

    /**
     * ==========================================
     * OVERRIDE METHOD #2 - POLYMORPHISM
     * ==========================================
     * Panitia view hanya butuh $events (tanpa $categories & $panitia)
     * 
     * @param mixed $events
     * @param Request $request
     * @return \Illuminate\View\View
     */
    protected function renderIndexView($events, Request $request)
    {
        return view('panitia.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('panitia.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'ticket_names' => 'required|array|min:1',
            'ticket_names.*' => 'required|string|min:1',
            'ticket_prices' => 'required|array|min:1',
            'ticket_prices.*' => 'required|numeric|min:0',
            'ticket_quotas' => 'required|array|min:1',
            'ticket_quotas.*' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters/' . date('Y/m'), 'public');
        }

        $validated['panitia_id'] = auth()->id();
        $validated['status'] = 'draft';

        DB::beginTransaction();
        try {
            $event = Event::create($validated);
            foreach ($validated['ticket_names'] as $i => $name) {
                TicketType::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'price' => $validated['ticket_prices'][$i],
                    'quota' => $validated['ticket_quotas'][$i],
                    'registered' => 0,
                ]);
            }
            DB::commit();
            return redirect()->route('panitia.events.index')->with('success', 'Event berhasil dibuat, menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);
        $categories = Category::orderBy('name')->get();
        return view('panitia.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster) Storage::disk('public')->delete($event->poster);
            $validated['poster'] = $request->file('poster')->store('posters/' . date('Y/m'), 'public');
        }

        $event->update($validated);
        return redirect()->route('panitia.events.index')->with('success', 'Event berhasil diupdate.');
    }

    public function registrations(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);

        $registrations = $event->registrations()
            ->with(['ticketType', 'user', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('panitia.events.registrations', compact('event', 'registrations'));
    }

    public function confirmPayment(Request $request, Event $event, Registration $registration)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'action' => 'required|in:confirm,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        $payment = $registration->payments()->latest()->first();
        if (!$payment) {
            return back()->with('error', 'Tidak ada bukti pembayaran.');
        }

        if ($validated['action'] === 'confirm') {
            $payment->verify(auth()->id());
            $msg = 'Pembayaran dikonfirmasi!';
        } else {
            $payment->reject(auth()->id(), $validated['notes'] ?? 'Ditolak panitia');
            $msg = 'Pembayaran ditolak.';
        }

        return back()->with('success', $msg);
    }

    public function exportRegistrations(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);

        $registrations = $event->registrations()->with(['ticketType', 'user', 'payments'])->get();
        $filename = 'peserta_' . str_replace(' ', '_', $event->title) . '_' . date('Ymd') . '.csv';

        return response()->streamDownload(function() use ($registrations) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Registrasi', 'Nama', 'Email', 'Tiket', 'Harga', 'Status', 'Tanggal Daftar']);
            foreach ($registrations as $r) {
                $status = $r->payments->contains(fn($p) => $p->status === 'verified') ? 'Lunas' : 'Belum Lunas';
                fputcsv($out, [
                    $r->registration_number,
                    $r->user->name,
                    $r->user->email,
                    $r->ticketType->name,
                    number_format($r->ticketType->price, 0, ',', '.'),
                    $status,
                    $r->registered_at->format('d/m/Y H:i')
                ]);
            }
            fclose($out);
        }, $filename);
    }
}