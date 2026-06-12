<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'target', 'created_by', 'is_active', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTargetLabelAttribute()
    {
        return match($this->target) {
            'all' => 'Semua User',
            'panitia' => 'Panitia Saja',
            'user' => 'User Saja',
            default => 'Semua User'
        };
    }
}