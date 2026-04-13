<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Event $event)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $registrations = Registration::with('ticketType')
            ->where('event_id', $event->id)
            ->orderBy('registered_at', 'desc')
            ->get();
            
        return view('admin.registrations.index', compact('event', 'registrations'));
    }
    
    public function updatePayment(Request $request, Event $event, Registration $registration)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
            'admin_notes' => 'nullable|string'
        ]);
        
        if ($validated['payment_status'] === 'paid' && !$registration->isPaid()) {
            $registration->markAsPaid($registration->payment_method, $validated['admin_notes'] ?? null);
        } elseif ($validated['payment_status'] === 'failed') {
            $registration->markAsFailed($validated['admin_notes'] ?? null);
        } else {
            $registration->update([
                'payment_status' => $validated['payment_status'],
                'admin_notes' => $validated['admin_notes'] ?? null
            ]);
        }
        
        return redirect()->route('admin.events.registrations.index', $event)
            ->with('success', 'Status pembayaran berhasil diupdate!');
    }
    
    public function export(Event $event)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $registrations = Registration::with('ticketType')
            ->where('event_id', $event->id)
            ->get();
        
        $filename = 'peserta_' . str_replace(' ', '_', $event->title) . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");
        
        fputcsv($output, [
            'No Registrasi', 'Nama Peserta', 'Email', 'Jenis Tiket', 'Harga', 'Status Pembayaran', 'Tanggal Daftar'
        ]);
        
        foreach ($registrations as $reg) {
            fputcsv($output, [
                $reg->registration_number,
                $reg->user_name,
                $reg->user_email,
                $reg->ticketType->name,
                number_format($reg->ticketType->price, 0, ',', '.'),
                $reg->payment_status,
                $reg->registered_at->format('d/m/Y H:i')
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // Lihat detail pembayaran
    public function showPayment(Event $event, Registration $registration)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        return view('admin.registrations.payment-detail', compact('event', 'registration'));
    }
}