<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = Registration::with(['event', 'user', 'ticketType'])
            ->where('refund_status', '!=', Registration::REFUND_NONE)
            ->where('refund_status', '!=', Registration::REFUND_COMPLETED)
            ->orderBy('refund_requested_at', 'desc')
            ->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function process(Registration $registration, Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        $registration->processRefund($request->action, $request->notes);

        $title = $request->action === 'approve' ? 'Refund Disetujui' : 'Refund Ditolak';
        $message = $request->action === 'approve' 
            ? "Pengembalian dana untuk event '{$registration->event->title}' telah disetujui. Dana akan dikirim dalam 3-5 hari kerja."
            : "Pengembalian dana untuk event '{$registration->event->title}' ditolak. Alasan: " . ($request->notes ?? 'Tidak ada alasan');

        if ($registration->user) {
            $registration->user->notify('refund_processed', $title, $message, ['registration_id' => $registration->id]);
        }

        return back()->with('success', "Refund berhasil diproses.");
    }
}