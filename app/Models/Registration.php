<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number', 'event_id', 'ticket_type_id', 
        'user_name', 'user_email', 'payment_status', 'payment_proof',
        'payment_method', 'amount_paid', 'paid_at', 'admin_notes', 'registered_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public static function generateRegistrationNumber()
    {
        return 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function markAsPaid($method = null, $notes = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'amount_paid' => $this->ticketType->price,
            'paid_at' => now(),
            'admin_notes' => $notes
        ]);
    }

    public function markAsFailed($notes = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'admin_notes' => $notes
        ]);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
}