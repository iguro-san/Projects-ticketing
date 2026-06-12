<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function request(Registration $registration, Request $request)
    {
        if ($registration->user_email !== auth()->user()->email) {
            abort(403);
        }

        if (!$registration->canRequestRefund()) {
            return back()->with('error', 'Tidak dapat meminta refund untuk registrasi ini.');
        }

        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ]);

        $registration->requestRefund($request->reason);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(
                'refund_request',
                'Permintaan Refund Baru',
                "User {$registration->user_name} meminta refund untuk event '{$registration->event->title}'",
                ['registration_id' => $registration->id]
            );
        }

        return back()->with('success', 'Permintaan refund telah dikirim. Mohon tunggu konfirmasi admin.');
    }
}