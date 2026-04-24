<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Payment;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    // HAPUS constructor

    public function index(Request $request)
    {
        $query = Registration::with(['event', 'user', 'ticketType']);
        
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }
        
        $registrations = $query->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $events = Event::all();
        
        return view('admin.registrations.index', compact('registrations', 'events'));
    }

    public function show(Registration $registration)
    {
        $registration->load(['event', 'user', 'ticketType', 'payments.verifier']);
        
        return view('admin.registrations.show', compact('registration'));
    }

    public function verifyPayment(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $latestPayment = $registration->payments()->latest()->first();
        
        if (!$latestPayment) {
            return back()->with('error', 'Tidak ada data pembayaran.');
        }

        if ($validated['action'] === 'verify') {
            $latestPayment->verify(auth()->id());
            $registration->markAsPaid(
                $latestPayment->method,
                $validated['notes'] ?? 'Pembayaran diverifikasi oleh admin'
            );
            
            $message = 'Pembayaran berhasil diverifikasi.';
        } else {
            $latestPayment->reject(auth()->id(), $validated['notes'] ?? null);
            $registration->markAsFailed($validated['notes'] ?? 'Pembayaran ditolak oleh admin');
            
            $message = 'Pembayaran ditolak.';
        }

        return redirect()->route('admin.registrations.index')
            ->with('success', $message);
    }

    public function export()
    {
        $registrations = Registration::with(['event', 'user', 'ticketType'])
            ->where('payment_status', 'paid')
            ->get();

        $filename = 'registrations_' . date('Y-m-d_His') . '.csv';
        
        return response()->streamDownload(function() use ($registrations) {
            $output = fopen('php://output', 'w');
            
            fputcsv($output, [
                'No Registrasi', 'Event', 'Tanggal Event', 'Peserta', 
                'Email', 'Tiket', 'Harga', 'Status', 'Tanggal Bayar'
            ]);
            
            foreach ($registrations as $reg) {
                fputcsv($output, [
                    $reg->registration_number,
                    $reg->event->title,
                    $reg->event->event_date->format('d/m/Y'),
                    $reg->user_name,
                    $reg->user_email,
                    $reg->ticketType->name,
                    number_format($reg->amount_paid, 0, ',', '.'),
                    $reg->payment_status,
                    $reg->paid_at ? $reg->paid_at->format('d/m/Y H:i') : '-'
                ]);
            }
            
            fclose($output);
        }, $filename);
    }
}