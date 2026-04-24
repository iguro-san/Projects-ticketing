<?php

namespace App\Http\Controllers;

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
        
        // Validate ticket belongs to event
        if ($ticketType->event_id !== $event->id) {
            return back()->with('error', 'Tiket tidak valid untuk event ini.');
        }
        
        // Check ticket availability
        if (!$ticketType->isAvailable()) {
            return back()->with('error', 'Maaf, tiket sudah habis!');
        }
        
        // Check if already registered - GUNAKAN user_email (bukan user_id)
        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_email', $user->email)  // GANTI: pakai email
            ->where('payment_status', '!=', 'failed')
            ->first();
            
        if ($existingRegistration) {
            return back()->with('error', 'Anda sudah terdaftar di event ini.');
        }

        DB::beginTransaction();
        
        try {
            // Create registration - SESUAIKAN dengan kolom yang ada
            $registration = Registration::create([
                'registration_number' => Registration::generateRegistrationNumber(),
                'event_id' => $event->id,
                'ticket_type_id' => $ticketType->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'payment_status' => 'pending',
                'registered_at' => now()
            ]);
            
            // Increment registered count
            $ticketType->increment('registered');
            
            DB::commit();
            
            return redirect()->route('payment.show', $registration)
                ->with('success', 'Pendaftaran berhasil! Silakan lakukan pembayaran.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function myTickets()
    {
        $user = auth()->user();
        
        // GUNAKAN user_email (bukan user_id)
        $registrations = Registration::with(['event', 'ticketType'])
            ->where('user_email', $user->email)  // GANTI: pakai email
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.my-tickets', compact('registrations'));
    }
}