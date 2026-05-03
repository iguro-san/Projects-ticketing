<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'panitia_id', 'approved_by', 'approved_at',
        'title', 'description', 'event_date', 'location', 'poster', 'status'
    ];

    protected $casts = [
        'event_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function panitia() { return $this->belongsTo(User::class, 'panitia_id'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function ticketTypes() { return $this->hasMany(TicketType::class); }
    public function registrations() { return $this->hasMany(Registration::class); }

    public function getAvailableTicketsAttribute()
    {
        return $this->ticketTypes->sum(fn($t) => $t->quota - $t->registered);
    }

    public function isApproved(): bool { return $this->status === 'active' && $this->approved_by !== null; }
    public function isDraft(): bool { return $this->status === 'draft'; }

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
        $this->update([
            'status' => 'cancelled',
        ]);
    }
}