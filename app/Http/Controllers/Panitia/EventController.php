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

    /**
     * ==========================================
     * UPDATE EVENT + TAMBAH TIKET BARU
     * ==========================================
     */
    public function update(Request $request, Event $event)
    {
        // Cek kepemilikan event
        if ($event->panitia_id !== auth()->id()) {
            abort(403);
        }

        // ==========================================
        // VALIDASI
        // ==========================================
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Validasi tiket baru (opsional - bisa kosong)
            'ticket_names' => 'nullable|array',
            'ticket_names.*' => 'nullable|string|min:1',
            'ticket_prices' => 'nullable|array',
            'ticket_prices.*' => 'nullable|numeric|min:0',
            'ticket_quotas' => 'nullable|array',
            'ticket_quotas.*' => 'nullable|integer|min:1',
        ]);

        // ==========================================
        // UPDATE POSTER JIKA ADA
        // ==========================================
        if ($request->hasFile('poster')) {
            if ($event->poster) {
                Storage::disk('public')->delete($event->poster);
            }
            $validated['poster'] = $request->file('poster')->store('posters/' . date('Y/m'), 'public');
        }

        // ==========================================
        // UPDATE EVENT
        // ==========================================
        $event->update($validated);

        // ==========================================
        // TAMBAH TIKET BARU JIKA ADA
        // ==========================================
        $ticketAdded = 0;
        $ticketErrors = [];

        // CEK APAKAH ADA TIKET YANG DIKIRIM
        if ($request->has('ticket_names') && is_array($request->ticket_names)) {
            // Filter tiket yang memiliki nama (tidak kosong)
            $ticketData = [];
            foreach ($request->ticket_names as $i => $name) {
                $name = trim($name);
                if (!empty($name)) {
                    // Pastikan index price dan quota ada
                    $price = isset($request->ticket_prices[$i]) ? (float) $request->ticket_prices[$i] : 0;
                    $quota = isset($request->ticket_quotas[$i]) ? (int) $request->ticket_quotas[$i] : 1;
                    
                    $ticketData[] = [
                        'name' => $name,
                        'price' => $price,
                        'quota' => $quota,
                    ];
                }
            }

            // Jika ada data tiket, simpan
            if (!empty($ticketData)) {
                DB::beginTransaction();
                try {
                    foreach ($ticketData as $data) {
                        TicketType::create([
                            'event_id' => $event->id,
                            'name' => $data['name'],
                            'price' => $data['price'],
                            'quota' => $data['quota'],
                            'registered' => 0,
                        ]);
                        $ticketAdded++;
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Gagal menambah tiket: ' . $e->getMessage())->withInput();
                }
            }
        }

        // ==========================================
        // RESPONSE
        // ==========================================
        $message = 'Event berhasil diupdate.';
        if ($ticketAdded > 0) {
            $message .= ' ' . $ticketAdded . ' tiket baru berhasil ditambahkan.';
        } else {
            $message .= ' Tidak ada tiket baru yang ditambahkan.';
        }

        return redirect()->route('panitia.events.index')->with('success', $message);
    }

    public function destroy(Event $event)
    {
        abort(403, 'Panitia tidak diizinkan menghapus event.');
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

        $registrations = $event->registrations()
            ->with(['ticketType', 'user', 'payments'])
            ->get();

        $filename = 'peserta_' . str_replace(' ', '_', $event->title) . '_' . date('Ymd') . '.csv';

        return response()->streamDownload(function() use ($registrations) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Registrasi', 'Nama', 'Email', 'Tiket', 'Harga', 'Status', 'Tanggal Daftar']);
            foreach ($registrations as $r) {
                fputcsv($out, [
                    $r->registration_number,
                    $r->user->name ?? '-',
                    $r->user->email ?? '-',
                    $r->ticketType->name,
                    number_format($r->ticketType->price, 0, ',', '.'),
                    $r->payment_status_label,
                    $r->registered_at->format('d/m/Y H:i')
                ]);
            }
            fclose($out);
        }, $filename);
    }

    /**
     * ==========================================
     * HAPUS TIKET (jika belum ada pendaftar)
     * ==========================================
     */
    public function destroyTicket(Event $event, TicketType $ticketType)
    {
        // Cek apakah panitia adalah pemilik event
        if ($event->panitia_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus tiket ini.');
        }
        
        // Cek apakah tiket sudah ada pendaftar
        $registrationCount = $ticketType->registrations()->count();
        if ($registrationCount > 0) {
            return back()->with('error', 'Tiket "' . $ticketType->name . '" tidak dapat dihapus karena sudah ada ' . $registrationCount . ' peserta terdaftar.');
        }
        
        // Hapus tiket
        $ticketType->delete();
        
        return back()->with('success', 'Tiket "' . $ticketType->name . '" berhasil dihapus.');
    }
}