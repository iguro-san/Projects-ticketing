<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'panitia_id', 'approved_by', 'approved_at',
        'title', 'description', 'event_date', 'location', 'poster', 'status',
        'suspension_status', 'suspension_reason', 'suspended_at', 'suspended_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    // Constants
    const SUSPENSION_NORMAL = 'normal';
    const SUSPENSION_PENDING = 'pending';
    const SUSPENSION_CANCELLED = 'cancelled';

    // Relationships
    public function category() 
    { 
        return $this->belongsTo(Category::class); 
    }
    
    public function panitia() 
    { 
        return $this->belongsTo(User::class, 'panitia_id'); 
    }
    
    public function approver() 
    { 
        return $this->belongsTo(User::class, 'approved_by'); 
    }
    
    public function suspender() 
    { 
        return $this->belongsTo(User::class, 'suspended_by'); 
    }
    
    public function ticketTypes() 
    { 
        return $this->hasMany(TicketType::class); 
    }
    
    public function registrations() 
    { 
        return $this->hasMany(Registration::class); 
    }

    // Accessors
    public function getAvailableTicketsAttribute()
    {
        return $this->ticketTypes->sum(fn($t) => $t->quota - $t->registered);
    }

    public function getMinPriceAttribute()
    {
        return $this->ticketTypes->min('price');
    }

    public function getIsFreeAttribute()
    {
        return $this->ticketTypes->max('price') == 0;
    }

    // Status Checkers
    public function isApproved(): bool 
    { 
        return $this->status === 'active' && $this->approved_by !== null; 
    }
    
    public function isDraft(): bool 
    { 
        return $this->status === 'draft'; 
    }
    
    public function isSuspended(): bool 
    { 
        return $this->suspension_status !== self::SUSPENSION_NORMAL; 
    }
    
    public function isPendingSuspension(): bool 
    { 
        return $this->suspension_status === self::SUSPENSION_PENDING; 
    }
    
    public function canRegister(): bool 
    { 
        return $this->status === 'active' && $this->suspension_status === self::SUSPENSION_NORMAL; 
    }

    // Actions
    public function approve($adminId): void
    {
        $this->update([
            'status' => 'active',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
    }

    public function reject($reason = null): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function suspend($reason, $adminId): void
    {
        $this->update([
            'suspension_status' => self::SUSPENSION_PENDING,
            'suspension_reason' => $reason,
            'suspended_at' => now(),
            'suspended_by' => $adminId
        ]);
    }

    public function continueSuspension(): void
    {
        $this->update([
            'suspension_status' => self::SUSPENSION_NORMAL,
            'suspension_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null
        ]);
    }

    public function cancelSuspension(): void
    {
        $this->update([
            'status' => 'cancelled',
            'suspension_status' => self::SUSPENSION_CANCELLED
        ]);
    }
}