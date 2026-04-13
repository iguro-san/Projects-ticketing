<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'name', 'price', 'quota', 'registered'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getRemainingQuotaAttribute()
    {
        return $this->quota - $this->registered;
    }

    public function isAvailable()
    {
        return $this->remaining_quota > 0;
    }
}