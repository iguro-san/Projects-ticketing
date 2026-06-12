<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->latest()
            ->paginate(10);
        
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10',
            'target' => 'required|in:all,panitia,user',
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target' => $validated['target'],
            'created_by' => auth()->id(),
            'published_at' => now(),
            'is_active' => true,
        ]);

        // Kirim notifikasi ke target yang dipilih
        $this->sendNotifications($announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat dan notifikasi telah dikirim!');
    }

    private function sendNotifications(Announcement $announcement)
    {
        $query = User::query();
        
        if ($announcement->target === 'panitia') {
            $query->where('role', 'panitia');
        } elseif ($announcement->target === 'user') {
            $query->where('role', 'user');
        }
        // jika 'all', ambil semua user

        $users = $query->get();

        foreach ($users as $user) {
            $user->notify(
                'announcement',
                $announcement->title,
                $announcement->content,
                ['announcement_id' => $announcement->id]
            );
        }
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        
        return back()->with('success', 'Status pengumuman diubah.');
    }
}