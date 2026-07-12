<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    /**
     * ==========================================
     * DAFTAR REFUND YANG PERLU DIPROSES
     * ==========================================
     * Menampilkan semua refund dengan status pending atau processing
     */
    public function index()
    {
        $refunds = Registration::with(['event', 'user', 'ticketType'])
            ->where('refund_status', 'pending')
            ->orWhere('refund_status', 'processing')
            ->orderBy('refund_requested_at', 'desc')
            ->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * ==========================================
     * PROSES REFUND - TANPA INPUT DATA BANK
     * ==========================================
     * Admin cukup klik tombol "Proses Refund"
     * Data bank sudah ada dari data pembayaran
     */
    public function process(Request $request, Registration $registration)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Cek apakah refund masih pending
        if (!in_array($registration->refund_status, ['pending', 'processing'])) {
            return back()->with('error', 'Refund tidak dapat diproses karena statusnya ' . $registration->refund_status);
        }

        // Validasi data bank sudah ada
        if (empty($registration->refund_bank) || empty($registration->refund_account_number)) {
            return back()->with('error', 'Data rekening penerima tidak lengkap. Pastikan peserta mengisi data bank saat pembayaran.');
        }

        // Ambil nominal refund
        $refundAmount = $registration->refund_amount ?? $registration->amount_paid ?? $registration->ticketType->price ?? 0;

        // Proses refund
        $registration->update([
            'refund_status' => 'completed',
            'refund_processed_at' => now(),
            'refund_notes' => $request->notes ?? 'Refund berhasil diproses oleh admin',
        ]);

        // Kirim notifikasi ke user
        if ($registration->user) {
            $registration->user->notify(
                'refund_processed',
                'Refund Telah Diproses',
                "Pengembalian dana untuk event '{$registration->event->title}' telah berhasil diproses. Dana sebesar Rp " . number_format($refundAmount, 0, ',', '.') . " akan dikirim ke rekening {$registration->refund_bank} a.n. {$registration->refund_account_name}",
                ['registration_id' => $registration->id]
            );
        }

        return back()->with('success', 'Refund berhasil diproses untuk ' . $registration->user_name);
    }

    /**
     * ==========================================
     * PROSES REFUND MASSAL
     * ==========================================
     * Admin bisa proses semua refund sekaligus
     */
    public function processAll(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Parse IDs dari JSON
        $ids = json_decode($request->ids, true);
        
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Tidak ada refund yang dipilih.');
        }

        // Ambil semua refund pending yang dipilih
        $registrations = Registration::whereIn('id', $ids)
            ->where('refund_status', 'pending')
            ->get();

        if ($registrations->isEmpty()) {
            return back()->with('error', 'Tidak ada refund pending yang dipilih.');
        }

        $processed = 0;
        $skipped = 0;

        foreach ($registrations as $reg) {
            // Skip jika data bank tidak lengkap
            if (empty($reg->refund_bank) || empty($reg->refund_account_number)) {
                $skipped++;
                continue;
            }

            $refundAmount = $reg->refund_amount ?? $reg->amount_paid ?? $reg->ticketType->price ?? 0;

            $reg->update([
                'refund_status' => 'completed',
                'refund_processed_at' => now(),
                'refund_notes' => 'Refund massal diproses oleh admin',
            ]);

            // Kirim notifikasi
            if ($reg->user) {
                $reg->user->notify(
                    'refund_processed',
                    'Refund Telah Diproses',
                    "Pengembalian dana untuk event '{$reg->event->title}' telah berhasil diproses. Dana sebesar Rp " . number_format($refundAmount, 0, ',', '.') . " akan dikirim ke rekening {$reg->refund_bank} a.n. {$reg->refund_account_name}",
                    ['registration_id' => $reg->id]
                );
            }

            $processed++;
        }

        $message = "{$processed} refund berhasil diproses.";
        if ($skipped > 0) {
            $message .= " {$skipped} refund dilewati karena data bank tidak lengkap.";
        }

        return back()->with('success', $message);
    }

    /**
     * ==========================================
     * TOLAK REFUND
     * ==========================================
     */
    public function reject(Request $request, Registration $registration)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $registration->update([
            'refund_status' => 'rejected',
            'refund_notes' => $request->notes ?? 'Refund ditolak oleh admin',
        ]);

        if ($registration->user) {
            $registration->user->notify(
                'refund_rejected',
                'Refund Ditolak',
                "Pengembalian dana untuk event '{$registration->event->title}' ditolak. Alasan: " . ($request->notes ?? 'Tidak ada alasan'),
                ['registration_id' => $registration->id]
            );
        }

        return back()->with('success', 'Refund ditolak.');
    }

    /**
     * ==========================================
     * DETAIL REFUND
     * ==========================================
     */
    public function show(Registration $registration)
    {
        $registration->load(['event', 'user', 'ticketType']);
        return view('admin.refunds.show', compact('registration'));
    }
}