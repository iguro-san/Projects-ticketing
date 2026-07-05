<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class PaymentConfirmationController extends Controller
{
    public function index()
    {
        $pendingPayments = Registration::with(['event', 'user', 'ticketType'])
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_proof')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.payments.index', compact('pendingPayments'));
    }

    public function confirm(Registration $registration, Request $request)
    {
        $request->validate([
            'status' => 'required|in:paid,failed',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($request->status === 'paid') {
            $registration->markAsPaid($registration->payment_method, $request->notes);
            
            if ($registration->user) {
                $registration->user->notify(
                    'payment_confirmed',
                    'Pembayaran Dikonfirmasi',
                    "Pembayaran untuk event '{$registration->event->title}' telah dikonfirmasi.",
                    ['registration_id' => $registration->id]
                );
            }
            
            $message = 'Pembayaran berhasil dikonfirmasi.';
        } else {
            // ==========================================
            // KEMBALIKAN KUOTA TIKET SAAT DITOLAK
            // ==========================================
            if ($registration->ticketType) {
                $registration->ticketType->decrement('registered');
            }

            $registration->markAsFailed($request->notes);
            
            if ($registration->user) {
                $registration->user->notify(
                    'payment_rejected',
                    'Pembayaran Ditolak',
                    "Pembayaran untuk event '{$registration->event->title}' ditolak. Alasan: " . ($request->notes ?? 'Tidak ada alasan'),
                    ['registration_id' => $registration->id]
                );
            }
            
            $message = 'Pembayaran ditolak dan kuota tiket dikembalikan.';
        }

        return redirect()->route('admin.payments.index')->with('success', $message);
    }
}