<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id', 'amount', 'method',
        'proof_file', 'status', 'notes',
        'verified_at', 'verified_by'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Status Checkers
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // Actions
    public function verify($adminId): void
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $adminId,
            'verified_at' => now()
        ]);
    }

    public function reject($adminId, $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $adminId,
            'notes' => $notes
        ]);
    }
}