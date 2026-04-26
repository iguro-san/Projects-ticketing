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
        'admin_notes', 'registered_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    // ========== RELASI ==========
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
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ========== STATUS CHECKERS ==========
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->payment_status === 'expired';
    }

    public function isCancelled(): bool
    {
        return $this->payment_status === 'cancelled';
    }

    /**
     * Cek apakah pembayaran sudah melewati batas waktu
     */
    public function isDeadlinePassed(): bool
    {
        if (!$this->payment_deadline) {
            return false;
        }
        return Carbon::now()->greaterThan($this->payment_deadline);
    }

    /**
     * Batas waktu default (5 menit dari pendaftaran)
     */
    public static function getDefaultDeadline(): Carbon
    {
        return Carbon::now()->addMinutes(5);
    }

    /**
     * Sisa waktu pembayaran dalam format readable
     */
    public function getRemainingTimeAttribute(): ?string
    {
        if (!$this->payment_deadline || $this->isPaid()) {
            return null;
        }

        if ($this->isDeadlinePassed()) {
            return 'Kadaluarsa';
        }

        return Carbon::now()->diffForHumans($this->payment_deadline, [
            'parts' => 2,
            'short' => false,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
        ]);
    }

    /**
     * Sisa waktu dalam detik (untuk countdown JS)
     */
    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->payment_deadline || $this->isPaid()) {
            return 0;
        }

        $remaining = Carbon::now()->diffInSeconds($this->payment_deadline, false);
        return max(0, $remaining);
    }

    // ========== ACTIONS ==========
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

    /**
     * Batalkan pendaftaran (hangus/kadaluarsa)
     */
    public function cancel($reason = null): void
    {
        $this->update([
            'payment_status' => 'cancelled',
            'cancelled_at' => now(),
            'admin_notes' => $reason ?? 'Batas waktu pembayaran habis'
        ]);

        // Kembalikan kuota tiket
        $this->ticketType->decrement('registered');
    }

    // ========== STATIC ==========
    public static function generateRegistrationNumber(): string
    {
        return 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}