<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->markAsRead();
        
        return back();
    }

    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }
}