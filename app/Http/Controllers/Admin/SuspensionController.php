<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class SuspensionController extends Controller
{
    public function pending(Event $event, Request $request)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        if ($event->status !== 'active') {
            return back()->with('error', 'Hanya event aktif yang dapat dipending.');
        }

        $event->suspend($request->reason, auth()->id());

        // Notifikasi ke panitia
        $event->panitia->notify(
            'event_pending',
            'Event Dipending',
            "Event '{$event->title}' sedang dalam peninjauan. Alasan: {$request->reason}",
            ['event_id' => $event->id]
        );

        // Notifikasi ke peserta yang sudah bayar
        $registrations = Registration::where('event_id', $event->id)
            ->where('payment_status', 'paid')
            ->get();

        foreach ($registrations as $reg) {
            if ($reg->user) {
                $reg->user->notify(
                    'event_pending',
                    'Info Event',
                    "Event '{$event->title}' sedang dalam peninjauan. Aktivitas event dihentikan sementara.",
                    ['event_id' => $event->id]
                );
            }
        }

        return back()->with('success', 'Event berhasil dipending.');
    }

    public function resolve(Event $event, $action)
    {
        if ($action === 'continue') {
            $event->continueSuspension();
            $message = "Event '{$event->title}' telah disetujui untuk dilanjutkan.";
            $type = 'event_continue';
            $title = 'Event Dilanjutkan';
        } else {
            $event->cancelSuspension();
            $message = "Event '{$event->title}' dibatalkan. Silakan ajukan refund jika sudah melakukan pembayaran.";
            $type = 'event_cancelled';
            $title = 'Event Dibatalkan';

            // Proses refund untuk semua peserta yang sudah bayar
            $registrations = Registration::where('event_id', $event->id)
                ->where('payment_status', 'paid')
                ->get();

            foreach ($registrations as $reg) {
                if ($reg->user) {
                    $reg->requestRefund('Event dibatalkan oleh admin karena masalah dengan panitia');
                }
            }
        }

        // Notifikasi ke panitia
        $event->panitia->notify($type, $title, $message, ['event_id' => $event->id]);

        return redirect()->route('admin.events.index')->with('success', $message);
    }
}