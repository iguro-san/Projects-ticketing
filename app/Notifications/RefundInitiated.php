<?php

namespace App\Notifications;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RefundInitiated extends Notification
{
    use Queueable;

    protected $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Pengembalian Dana Diproses',
            'message' => "Pengembalian dana untuk event '{$this->registration->event->title}' sedang diproses. Dana akan dikembalikan dalam waktu maksimal 1 bulan ke rekening Anda. Mohon periksa data rekening Anda.",
            'registration_id' => $this->registration->id,
            'event_id' => $this->registration->event_id,
            'type' => 'refund_initiated',
        ];
    }
}