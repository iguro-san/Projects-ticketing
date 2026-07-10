<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventCancelled extends Notification
{
    use Queueable;

    protected $event;
    protected $registration;

    public function __construct(Event $event, Registration $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $isPaid = $this->registration->ticketType && $this->registration->ticketType->price > 0;
        $message = "Event '{$this->event->title}' telah dibatalkan oleh admin.";

        if ($this->registration->payment_status === 'paid' && $isPaid) {
            $message .= " Dana akan dikembalikan dalam waktu maksimal 1 bulan. Mohon tunggu informasi lebih lanjut.";
        } else {
            $message .= " Karena tiket gratis, tidak ada pengembalian dana.";
        }

        return [
            'title' => 'Event Dibatalkan',
            'message' => $message,
            'event_id' => $this->event->id,
            'registration_id' => $this->registration->id,
            'type' => 'event_cancelled',
        ];
    }
}