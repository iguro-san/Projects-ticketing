<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function show(Registration $registration)
    {
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        if ($registration->ticketType->price == 0) {
            return redirect()->route('my.tickets')->with('success', 'Tiket GRATIS sudah otomatis aktif!');
        }

        if ($registration->isPaid()) {
            return redirect()->route('my.tickets')->with('success', 'Pembayaran sudah dikonfirmasi!');
        }

        $registration->load(['event', 'ticketType']);

        $bankAccounts = [
            ['bank' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'PT Event Management'],
            ['bank' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'PT Event Management'],
            ['bank' => 'BRI', 'account_number' => '1122334455', 'account_name' => 'PT Event Management'],
            ['bank' => 'BNI', 'account_number' => '5566778899', 'account_name' => 'PT Event Management'],
        ];

        return view('payment.show', compact('registration', 'bankAccounts'));
    }

    public function uploadProof(Request $request, Registration $registration)
    {
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        if ($registration->ticketType->price == 0) {
            return redirect()->route('my.tickets')->with('success', 'Tiket GRATIS sudah otomatis aktif!');
        }

        if ($registration->payment_proof) {
            return back()->with('error', 'Anda sudah upload bukti pembayaran.');
        }

        if ($registration->isPaid()) {
            return back()->with('error', 'Pembayaran sudah dikonfirmasi!');
        }

        if ($registration->isDeadlinePassed()) {
            $registration->cancel('Batas waktu habis');
            return redirect()->route('my.tickets')->with('error', 'Batas waktu habis. Silakan daftar ulang.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|in:BCA,Mandiri,BRI,BNI',
            'sender_name' => 'required|string|max:255',
            'sender_account' => 'required|string|max:50',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $proofPath = $request->file('payment_proof')->store('payment-proofs/' . date('Y/m'), 'public');

        $registration->update([
            'payment_method' => $validated['payment_method'],
            'sender_name' => $validated['sender_name'],
            'sender_account' => $validated['sender_account'],
            'payment_proof' => $proofPath,
        ]);

        return redirect()->route('my.tickets')
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }
}