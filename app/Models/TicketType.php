<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'price', 'quota', 
        'registered', 'description', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // Accessors
    public function getRemainingQuotaAttribute()
    {
        return $this->quota - $this->registered;
    }

    // Checkers
    public function isAvailable(): bool
    {
        return $this->is_active && $this->remaining_quota > 0;
    }

    public function isSoldOut(): bool
    {
        return $this->registered >= $this->quota;
    }
}