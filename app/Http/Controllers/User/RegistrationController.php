<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function register(Request $request, Event $event)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'user_phone' => 'nullable|string|max:15',
        ]);

        $ticketType = TicketType::findOrFail($validated['ticket_type_id']);

        // Validasi tiket
        if ($ticketType->event_id !== $event->id) {
            return back()->with('error', 'Tiket tidak valid untuk event ini.');
        }

        if (!$ticketType->isAvailable()) {
            return back()->with('error', 'Maaf, tiket sudah habis!');
        }

        // ==========================================
        // 1. HAPUS REGISTRASI PENDING YANG KADALUARSA
        // ==========================================
        Registration::where('event_id', $event->id)
            ->where('user_email', $user->email)
            ->where('payment_status', 'pending')
            ->where('payment_deadline', '<', now())
            ->each(fn($reg) => $reg->cancel('Batas waktu pembayaran habis'));

        // ==========================================
        // 2. CEK APAKAH SUDAH TERDAFTAR (PENDING ATAU PAID)
        // ==========================================
        $existing = Registration::where('event_id', $event->id)
            ->where('user_email', $user->email)
            ->whereIn('payment_status', ['pending', 'paid'])
            ->first();

        if ($existing) {
            if ($existing->payment_status == 'pending' && !$existing->isDeadlinePassed()) {
                return redirect()->route('payment.show', $existing)
                    ->with('warning', 'Anda sudah mendaftar! Silakan selesaikan pembayaran.');
            }
            return back()->with('error', 'Anda sudah terdaftar di event ini dan tidak dapat mendaftar ulang.');
        }

        // ==========================================
        // 3. CEK JUMLAH PENOLAKAN (FAILED) - MAKS 2 KALI
        // ==========================================
        $failedCount = Registration::where('event_id', $event->id)
            ->where('user_email', $user->email)
            ->where('payment_status', 'failed')
            ->count();

        if ($failedCount >= 2) {
            return back()->with('error', 'Anda sudah 2 kali gagal bayar untuk event ini dan tidak diperbolehkan mendaftar lagi.');
        }

        // ==========================================
        // 4. BUAT REGISTRASI BARU
        // ==========================================
        DB::beginTransaction();
        try {
            $paymentStatus = $ticketType->price == 0 ? 'paid' : 'pending';
            $paymentDeadline = $ticketType->price == 0 ? null : Registration::getDefaultDeadline();

            $registration = Registration::create([
                'registration_number' => Registration::generateRegistrationNumber(),
                'event_id' => $event->id,
                'ticket_type_id' => $ticketType->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $validated['user_phone'] ?? $user->phone,
                'payment_status' => $paymentStatus,
                'payment_deadline' => $paymentDeadline,
                'amount_paid' => $ticketType->price == 0 ? 0 : null,
                'paid_at' => $ticketType->price == 0 ? now() : null,
                'payment_method' => $ticketType->price == 0 ? 'Gratis' : null,
                'admin_notes' => $ticketType->price == 0 ? 'Tiket gratis - otomatis terkonfirmasi' : null,
                'registered_at' => now()
            ]);

            $ticketType->increment('registered');

            DB::commit();

            if ($ticketType->price == 0) {
                return redirect()->route('my.tickets')->with('success', 'Pendaftaran berhasil! Tiket GRATIS Anda sudah aktif.');
            }

            return redirect()->route('payment.show', $registration)->with('success', 'Pendaftaran berhasil! Segera bayar sebelum batas waktu.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    public function myTickets()
    {
        $user = auth()->user();

        Registration::where('user_email', $user->email)
            ->where('payment_status', 'pending')
            ->where('payment_deadline', '<', now())
            ->each(fn($reg) => $reg->cancel('Batas waktu pembayaran habis'));

        $registrations = Registration::with(['event', 'ticketType'])
            ->where('user_email', $user->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.my-tickets', compact('registrations'));
    }
}