<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Daftar event
     */
    public function register(Request $request, Event $event)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'user_phone' => 'nullable|string|max:15',
        ]);

        $ticketType = TicketType::findOrFail($validated['ticket_type_id']);

        // Validasi tiket milik event ini
        if ($ticketType->event_id !== $event->id) {
            return back()->with('error', 'Tiket tidak valid untuk event ini.');
        }

        // Cek ketersediaan tiket
        if (!$ticketType->isAvailable()) {
            return back()->with('error', 'Maaf, tiket sudah habis!');
        }

        // Cek apakah user sudah punya pendaftaran
        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_email', $user->email)
            ->where('payment_status', 'pending')
            ->first();

        if ($existingRegistration) {
            if ($existingRegistration->isDeadlinePassed()) {
                $existingRegistration->cancel('Dibatalkan karena membuat pendaftaran baru');
            } else {
                return redirect()->route('payment.show', $existingRegistration)
                    ->with('warning', 'Anda sudah mendaftar!');
            }
        }

        DB::beginTransaction();
        try {
            // Jika tiket GRATIS, langsung PAID
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

            // Redirect sesuai tipe tiket
            if ($ticketType->price == 0) {
                return redirect()->route('my.tickets')
                    ->with('success', 'Pendaftaran berhasil! Tiket GRATIS Anda sudah aktif.');
            }

            return redirect()->route('payment.show', $registration)
                ->with('success', 'Pendaftaran berhasil! Segera bayar sebelum batas waktu.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    /**
     * Tiket saya
     */
    public function myTickets()
    {
        $user = auth()->user();

        // Auto-cancel expired
        $this->cancelExpiredRegistrations($user->email);

        $registrations = Registration::with(['event', 'ticketType'])
            ->where('user_email', $user->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.my-tickets', compact('registrations'));
    }

    private function cancelExpiredRegistrations($email): void
    {
        Registration::where('user_email', $email)
            ->where('payment_status', 'pending')
            ->where('payment_deadline', '<', now())
            ->each(fn($reg) => $reg->cancel('Batas waktu pembayaran habis'));
    }
}