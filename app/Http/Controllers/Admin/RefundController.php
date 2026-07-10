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
            ->where('refund_status', '!=', 'none')
            ->where('refund_status', '!=', 'completed')
            ->orderBy('refund_requested_at', 'desc')
            ->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function process(Request $request, Registration $registration)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'refund_bank' => 'required_if:action,approve|in:BCA,Mandiri,BRI,BNI',
            'refund_account_name' => 'required_if:action,approve|string|max:255',
            'refund_account_number' => 'required_if:action,approve|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            $registration->update([
                'refund_status' => 'completed',
                'refund_processed_at' => now(),
                'refund_notes' => $request->notes,
                'refund_bank' => $request->refund_bank,
                'refund_account_name' => $request->refund_account_name,
                'refund_account_number' => $request->refund_account_number,
            ]);

            if ($registration->user) {
                $registration->user->notify(
                    'refund_processed',
                    'Refund Telah Diproses',
                    "Pengembalian dana untuk event '{$registration->event->title}' telah berhasil diproses. Dana akan dikirim ke rekening {$registration->refund_bank} a.n. {$registration->refund_account_name}.",
                    ['registration_id' => $registration->id]
                );
            }

            return back()->with('success', 'Refund berhasil diproses.');
        } else {
            $registration->update([
                'refund_status' => 'rejected',
                'refund_notes' => $request->notes ?? 'Refund ditolak oleh admin',
            ]);

            return back()->with('success', 'Refund ditolak.');
        }
    }
}