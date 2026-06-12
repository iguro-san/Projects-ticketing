<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Menampilkan histori semua event
     */
    public function index(Request $request)
    {
        $query = Event::with(['category', 'panitia'])
            ->withCount('registrations');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('panitia_id')) {
            $query->where('panitia_id', $request->panitia_id);
        }
        
        if ($request->filled('time_filter')) {
            if ($request->time_filter === 'upcoming') {
                $query->whereDate('event_date', '>=', now());
            } elseif ($request->time_filter === 'past') {
                $query->whereDate('event_date', '<', now());
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $events = $query->orderBy('event_date', 'desc')
            ->paginate(15)
            ->appends($request->query());
        
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.index', compact('events', 'categories', 'panitia'));
    }

    /**
     * Tampilkan detail event
     */
    public function show(Event $event)
    {
        $event->load(['category', 'panitia', 'ticketTypes', 'registrations']);
        return view('admin.events.show', compact('event'));
    }

    /**
     * APPROVE EVENT - Setujui event dari draft menjadi active
     */
    public function approve(Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->with('error', 'Hanya event dengan status Draft yang bisa disetujui.');
        }

        $event->approve(auth()->id());

        return back()->with('success', "Event \"{$event->title}\" telah DISETUJUI dan sekarang AKTIF!");
    }

    /**
     * REJECT EVENT - Tolak event dari draft menjadi cancelled
     */
    public function reject(Request $request, Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->with('error', 'Hanya event dengan status Draft yang bisa ditolak.');
        }

        $event->reject($request->reason ?? 'Ditolak oleh admin');

        return back()->with('success', "Event \"{$event->title}\" telah DITOLAK.");
    }
}