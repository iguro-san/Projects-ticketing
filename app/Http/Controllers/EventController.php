<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category')
            ->where('status', 'active')
            ->where('event_date', '>=', now()->subDay());
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        $events = $query->orderBy('event_date', 'asc')->paginate(9);
        $categories = \App\Models\Category::all();
        
        return view('events.index', compact('events', 'categories'));
    }
    
    public function show(Event $event)
    {
        if ($event->status !== 'active') {
            abort(404);
        }
        
        $ticketTypes = $event->ticketTypes()->get();
        
        return view('events.show', compact('event', 'ticketTypes'));
    }
    
    public function register(Request $request, Event $event)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }
        
        $validated = $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'name' => 'required|min:3',
            'email' => 'required|email',
        ]);
        
        $ticketType = TicketType::findOrFail($validated['ticket_type_id']);
        
        if ($ticketType->registered >= $ticketType->quota) {
            return back()->with('error', 'Maaf, kuota tiket ini sudah habis!');
        }
        
        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_email', $validated['email'])
            ->first();
            
        if ($existingRegistration) {
            return back()->with('error', 'Email ini sudah terdaftar untuk event ini!');
        }
        
        DB::beginTransaction();
        
        try {
            $registration = Registration::create([
                'registration_number' => Registration::generateRegistrationNumber(),
                'event_id' => $event->id,
                'ticket_type_id' => $ticketType->id,
                'user_name' => $validated['name'],
                'user_email' => $validated['email'],
                'payment_status' => 'pending',
                'registered_at' => now()
            ]);
            
            $ticketType->increment('registered');
            
            DB::commit();
            
            return redirect()->route('my.tickets')
                ->with('success', "Pendaftaran berhasil! Nomor registrasi: {$registration->registration_number}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }
    
    public function myTickets()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $registrations = Registration::with(['event', 'ticketType'])
            ->where('user_email', auth()->user()->email)
            ->orderBy('registered_at', 'desc')
            ->get();
            
        return view('events.my-tickets', compact('registrations'));
    }

    // Menampilkan halaman pembayaran
    public function showPayment(Registration $registration)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Pastikan user hanya bisa melihat pembayaran miliknya sendiri
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }
        
        $bankAccounts = [
            ['bank' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'PT Event Management'],
            ['bank' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'PT Event Management'],
            ['bank' => 'BRI', 'account_number' => '1122334455', 'account_name' => 'PT Event Management'],
            ['bank' => 'BNI', 'account_number' => '5544332211', 'account_name' => 'PT Event Management'],
        ];
        
        return view('events.payment', compact('registration', 'bankAccounts'));
    }

    // Upload bukti pembayaran
    public function uploadPayment(Request $request, Registration $registration)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }
        
        if ($registration->isPaid()) {
            return back()->with('error', 'Pembayaran sudah dikonfirmasi!');
        }
        
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'payment_method' => 'required|string',
        ]);
        
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            $registration->update([
                'payment_proof' => $proofPath,
                'payment_method' => $request->payment_method,
            ]);
        }
        
        return redirect()->route('my.tickets')->with('success', 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi admin.');
    }
}