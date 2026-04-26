<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Halaman pembayaran
     */
    public function show(Registration $registration)
    {
        // Verifikasi kepemilikan
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        // Jika sudah lunas
        if ($registration->isPaid()) {
            return redirect()->route('my.tickets')
                ->with('success', 'Pembayaran sudah dikonfirmasi!');
        }

        // Jika sudah kadaluarsa
        if ($registration->isDeadlinePassed()) {
            $registration->cancel('Batas waktu pembayaran habis');
            return redirect()->route('my.tickets')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan daftar ulang.');
        }

        $registration->load(['event', 'ticketType']);

        $bankAccounts = [
            ['bank' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'PT Event Management'],
            ['bank' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'PT Event Management'],
            ['bank' => 'BRI', 'account_number' => '1122334455', 'account_name' => 'PT Event Management'],
        ];

        return view('payment.show', compact('registration', 'bankAccounts'));
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadProof(Request $request, Registration $registration)
    {
        // Verifikasi kepemilikan
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        // Cek apakah sudah dibayar
        if ($registration->isPaid()) {
            return back()->with('error', 'Pembayaran sudah dikonfirmasi!');
        }

        // Cek deadline
        if ($registration->isDeadlinePassed()) {
            $registration->cancel('Batas waktu pembayaran habis');
            return redirect()->route('my.tickets')
                ->with('error', 'Batas waktu pembayaran telah habis.');
        }

        // Validasi
        $validated = $request->validate([
            'payment_method' => 'required|string|in:BCA,Mandiri,BRI,BNI,Other',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        // Upload bukti
        $proofPath = $request->file('payment_proof')->store(
            'payment-proofs/' . date('Y/m'),
            'public'
        );

        // Update registration
        $registration->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $proofPath,
        ]);

        return redirect()->route('my.tickets')
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi panitia.');
    }
}