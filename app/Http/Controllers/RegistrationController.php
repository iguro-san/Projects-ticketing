<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $request->validate([
            'ticket_type_id' => 'required',
            'name' => 'required',
            'email' => 'required|email'
        ]);
        
        if (!session('logged_in')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu!');
        }
        
        // Generate nomor registrasi unik
        $registrationNumber = 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        $registration = [
            'id' => uniqid(),
            'event_id' => $eventId,
            'user_email' => session('user_email'),
            'user_name' => $request->name,
            'ticket_type_id' => $request->ticket_type_id,
            'registration_number' => $registrationNumber,
            'payment_status' => 'pending',
            'registered_at' => now()
        ];
        
        // Simpan ke session
        $registrations = session('registrations', []);
        $registrations[] = $registration;
        session(['registrations' => $registrations]);
        
        return redirect('/my-tickets')->with('success', 'Pendaftaran berhasil! Nomor registrasi: ' . $registrationNumber);
    }
    
    public function myTickets()
    {
        $registrations = session('registrations', []);
        $myRegistrations = array_filter($registrations, function($reg) {
            return $reg['user_email'] == session('user_email');
        });
        
        return view('my_tickets', compact('myRegistrations'));
    }
    
    public function participants($eventId)
    {
        $registrations = session('registrations', []);
        $participants = array_filter($registrations, function($reg) use ($eventId) {
            return $reg['event_id'] == $eventId;
        });
        
        return view('admin.participants', compact('participants', 'eventId'));
    }
}