<?php
// app/Models/Notification.php - Update

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'data', 'is_read', 'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    // Helper untuk cek tipe notifikasi
    public function isAnnouncement()
    {
        return $this->type === 'announcement';
    }

    public function isPaymentRelated()
    {
        return in_array($this->type, ['payment_confirmed', 'payment_rejected', 'refund_processed']);
    }

    public function isEventRelated()
    {
        return in_array($this->type, ['event_pending', 'event_continue', 'event_cancelled']);
    }
}