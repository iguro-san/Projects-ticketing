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
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        // Jika tiket GRATIS, redirect ke my-tickets
        if ($registration->ticketType->price == 0) {
            return redirect()->route('my.tickets')
                ->with('success', 'Tiket GRATIS sudah otomatis aktif!');
        }

        // Jika sudah lunas
        if ($registration->isPaid()) {
            return redirect()->route('my.tickets')
                ->with('success', 'Pembayaran sudah dikonfirmasi!');
        }

        // Jika kadaluarsa
        if ($registration->isDeadlinePassed()) {
            $registration->cancel('Batas waktu habis');
            return redirect()->route('my.tickets')
                ->with('error', 'Batas waktu habis. Silakan daftar ulang.');
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
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        // Tiket gratis tidak perlu upload
        if ($registration->ticketType->price == 0) {
            return redirect()->route('my.tickets')
                ->with('success', 'Tiket GRATIS sudah otomatis aktif!');
        }

        if ($registration->isPaid()) {
            return back()->with('error', 'Pembayaran sudah dikonfirmasi!');
        }

        if ($registration->isDeadlinePassed()) {
            $registration->cancel('Batas waktu habis');
            return redirect()->route('my.tickets')->with('error', 'Batas waktu habis.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|in:BCA,Mandiri,BRI,BNI,Other',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $proofPath = $request->file('payment_proof')->store('payment-proofs/' . date('Y/m'), 'public');

        $registration->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $proofPath,
        ]);

        return redirect()->route('my.tickets')
            ->with('success', 'Bukti pembayaran diupload! Menunggu verifikasi panitia.');
    }
}