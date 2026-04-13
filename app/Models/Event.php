<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'description', 'event_date', 'location', 'poster', 'status'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getAvailableTicketsAttribute()
    {
        return $this->ticketTypes->sum(function ($ticket) {
            return $ticket->quota - $ticket->registered;
        });
    }
}