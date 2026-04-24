<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory; // HAPUS SoftDeletes

    protected $fillable = [
        'category_id', 'panitia_id', 'title', 'description',
        'event_date', 'location', 'poster', 'status'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function panitia()
    {
        return $this->belongsTo(User::class, 'panitia_id');
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}