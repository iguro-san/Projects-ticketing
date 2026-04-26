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

        // Cek apakah user sudah punya pendaftaran PENDING di event ini
        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_email', $user->email)
            ->where('payment_status', 'pending')
            ->first();

        if ($existingRegistration) {
            // Jika deadline sudah lewat, batalkan yang lama
            if ($existingRegistration->isDeadlinePassed()) {
                $existingRegistration->cancel('Dibatalkan karena membuat pendaftaran baru');
            } else {
                return redirect()->route('payment.show', $existingRegistration)
                    ->with('warning', 'Anda sudah mendaftar! Selesaikan pembayaran sebelum batas waktu.');
            }
        }

        DB::beginTransaction();

        try {
            $registration = Registration::create([
                'registration_number' => Registration::generateRegistrationNumber(),
                'event_id' => $event->id,
                'ticket_type_id' => $ticketType->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $validated['user_phone'] ?? $user->phone,
                'payment_status' => 'pending',
                'payment_deadline' => Registration::getDefaultDeadline(), // 24 jam
                'registered_at' => now()
            ]);

            $ticketType->increment('registered');

            DB::commit();

            return redirect()->route('payment.show', $registration)
                ->with('success', 'Pendaftaran berhasil! Segera bayar sebelum batas waktu 24 jam.');

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

        // Cek & batalkan pendaftaran yang sudah kadaluarsa
        $this->cancelExpiredRegistrations($user->email);

        $registrations = Registration::with(['event', 'ticketType'])
            ->where('user_email', $user->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.my-tickets', compact('registrations'));
    }

    /**
     * Batalkan pendaftaran kadaluarsa milik user tertentu
     */
    private function cancelExpiredRegistrations($email): void
    {
        $expired = Registration::where('user_email', $email)
            ->where('payment_status', 'pending')
            ->where('payment_deadline', '<', now())
            ->get();

        foreach ($expired as $reg) {
            $reg->cancel('Batas waktu pembayaran 24 jam telah habis');
        }
    }
}