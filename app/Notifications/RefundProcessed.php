<?php

namespace App\Notifications;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RefundProcessed extends Notification
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
            'title' => 'Refund Berhasil',
            'message' => "Pengembalian dana untuk event '{$this->registration->event->title}' telah berhasil diproses. Dana dikirim ke rekening {$this->registration->refund_bank} a.n. {$this->registration->refund_account_name}.",
            'registration_id' => $this->registration->id,
            'event_id' => $this->registration->event_id,
            'type' => 'refund_processed',
        ];
    }
}