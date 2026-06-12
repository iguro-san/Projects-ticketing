<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number', 'event_id', 'ticket_type_id',
        'user_id', 'user_name', 'user_email', 'user_phone',
        'payment_status', 'payment_deadline', 'payment_method', 
        'payment_proof', 'amount_paid', 'paid_at', 'cancelled_at',
        'admin_notes', 'registered_at',
        'refund_status', 'refund_reason', 'refund_requested_at',
        'refund_processed_at', 'refund_amount', 'refund_notes'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'cancelled_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    // Refund Constants
    const REFUND_NONE = 'none';
    const REFUND_PENDING = 'pending';
    const REFUND_PROCESSING = 'processing';
    const REFUND_COMPLETED = 'completed';
    const REFUND_FAILED = 'failed';

    // Relationships
    public function event() { return $this->belongsTo(Event::class); }
    public function ticketType() { return $this->belongsTo(TicketType::class); }
    public function user() { return $this->belongsTo(User::class, 'user_email', 'email'); }
    public function payments() { return $this->hasMany(Payment::class); }

    // Status Checkers
    public function isPaid(): bool { return $this->payment_status === 'paid'; }
    public function isPending(): bool { return $this->payment_status === 'pending'; }
    public function isFailed(): bool { return $this->payment_status === 'failed'; }
    public function isExpired(): bool { return $this->payment_status === 'expired'; }
    public function isCancelled(): bool { return $this->payment_status === 'cancelled'; }

    // Refund Checkers
    public function canRequestRefund(): bool
    {
        return $this->payment_status === 'paid' && 
               $this->refund_status === self::REFUND_NONE &&
               !$this->event->event_date->isPast();
    }

    public function isRefundPending(): bool
    {
        return in_array($this->refund_status, [self::REFUND_PENDING, self::REFUND_PROCESSING]);
    }

    // Deadline Methods
    public function isDeadlinePassed(): bool
    {
        if (!$this->payment_deadline) return false;
        return Carbon::now()->greaterThan($this->payment_deadline);
    }

    public static function getDefaultDeadline(): Carbon
    {
        return Carbon::now()->addMinutes(5);
    }

    public function getRemainingTimeAttribute(): ?string
    {
        if (!$this->payment_deadline || $this->isPaid()) return null;
        if ($this->isDeadlinePassed()) return 'Kadaluarsa';
        return Carbon::now()->diffForHumans($this->payment_deadline, ['parts' => 2, 'short' => false]);
    }

    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->payment_deadline || $this->isPaid()) return 0;
        return max(0, Carbon::now()->diffInSeconds($this->payment_deadline, false));
    }

    // Payment Actions
    public function markAsPaid($method = null, $notes = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $method ?? $this->payment_method,
            'amount_paid' => $this->ticketType->price,
            'paid_at' => now(),
            'admin_notes' => $notes
        ]);
    }

    public function markAsFailed($notes = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'admin_notes' => $notes
        ]);
    }

    public function cancel($reason = null): void
    {
        $this->update([
            'payment_status' => 'cancelled',
            'cancelled_at' => now(),
            'admin_notes' => $reason ?? 'Batas waktu pembayaran habis'
        ]);
        $this->ticketType->decrement('registered');
    }

    // Refund Actions
    public function requestRefund($reason): void
    {
        $this->update([
            'refund_status' => self::REFUND_PENDING,
            'refund_reason' => $reason,
            'refund_requested_at' => now()
        ]);
    }

    public function processRefund($action, $notes = null): void
    {
        if ($action === 'approve') {
            $this->update([
                'refund_status' => self::REFUND_COMPLETED,
                'refund_amount' => $this->amount_paid ?? $this->ticketType->price,
                'refund_notes' => $notes,
                'refund_processed_at' => now(),
                'payment_status' => 'refunded'
            ]);
        } else {
            $this->update([
                'refund_status' => self::REFUND_FAILED,
                'refund_notes' => $notes,
                'refund_processed_at' => now()
            ]);
        }
    }

    // Static Methods
    public static function generateRegistrationNumber(): string
    {
        return 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}