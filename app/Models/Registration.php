<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'event_id',
        'ticket_type_id',
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'payment_status',
        'payment_method',
        'sender_name',
        'sender_account',
        'payment_proof',
        'payment_deadline',
        'amount_paid',
        'paid_at',
        'admin_notes',
        'refund_status',
        'refund_reason',
        'refund_requested_at',
        'refund_processed_at',
        'refund_notes',
        'refund_bank',
        'refund_account_name',
        'refund_account_number',
        'registered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'registered_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Static Methods
    public static function generateRegistrationNumber()
    {
        $prefix = 'REG-' . date('Ymd');
        $last = self::where('registration_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$last) {
            $number = 1;
        } else {
            $number = intval(substr($last->registration_number, -4)) + 1;
        }
        
        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public static function getDefaultDeadline()
    {
        return Carbon::now()->addMinutes(5);
    }

    // Status Checkers
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }

    public function isCancelled()
    {
        return $this->payment_status === 'cancelled';
    }

    public function isDeadlinePassed()
    {
        if (!$this->payment_deadline) {
            return false;
        }
        return Carbon::now()->gt($this->payment_deadline);
    }

    // Refund Methods
    public function canRequestRefund()
    {
        return $this->isPaid() 
            && $this->refund_status === 'none'
            && $this->ticketType 
            && $this->ticketType->price > 0;
    }

    public function requestRefund($reason)
    {
        $this->update([
            'refund_status' => 'pending',
            'refund_reason' => $reason,
            'refund_requested_at' => now(),
        ]);
    }

    public function markAsPaid($method, $notes = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'amount_paid' => $this->ticketType->price ?? 0,
            'paid_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function markAsFailed($notes = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'admin_notes' => $notes,
        ]);
    }

    public function cancel($reason = null)
    {
        // Kurangi registered di ticket_type saat pembatalan
        if ($this->ticketType && $this->payment_status !== 'cancelled') {
            $this->ticketType->decrement('registered');
        }
        
        $this->update([
            'payment_status' => 'cancelled',
            'cancelled_at' => now(),
            'admin_notes' => $reason ?? 'Dibatalkan sistem',
        ]);
    }

    // Accessors - Pastikan return integer
    public function getRemainingSecondsAttribute()
    {
        if (!$this->payment_deadline) {
            return 0;
        }
        return (int) max(0, Carbon::now()->diffInSeconds($this->payment_deadline));
    }

    public function getRemainingTimeAttribute()
    {
        $seconds = $this->remaining_seconds;
        if ($seconds <= 0) {
            return 'Kadaluarsa';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}