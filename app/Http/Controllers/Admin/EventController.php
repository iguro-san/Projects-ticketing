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
     * Form buat event baru
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.create', compact('categories', 'panitia'));
    }

    /**
     * Simpan event baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'panitia_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,active,cancelled',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')
                ->store('posters/' . date('Y/m'), 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat.');
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
     * Form edit event
     */
    public function edit(Event $event)
    {
        $event->load(['category', 'panitia', 'ticketTypes']);
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.edit', compact('event', 'categories', 'panitia'));
    }

    /**
     * Update event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'panitia_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster) {
                Storage::disk('public')->delete($event->poster);
            }
            $validated['poster'] = $request->file('poster')
                ->store('posters/' . date('Y/m'), 'public');
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diupdate.');
    }

    /**
     * Hapus event
     */
    public function destroy(Event $event)
    {
        if ($event->registrations()->count() > 0) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah ada peserta terdaftar.');
        }

        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
        }

        $event->ticketTypes()->delete();
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
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