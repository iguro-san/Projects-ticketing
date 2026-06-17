<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseEventController; // <-- Ganti parent class
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends BaseEventController // <-- Extends ke BaseEventController
{
    /**
     * ==========================================
     * OVERRIDE METHOD #1 - POLYMORPHISM
     * ==========================================
     * Admin melihat SEMUA event (tanpa filter panitia_id)
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getBaseQuery(Request $request)
    {
        return Event::query();
    }

    /**
     * ==========================================
     * OVERRIDE METHOD #2 - POLYMORPHISM
     * ==========================================
     * Admin view membutuhkan data tambahan: $categories dan $panitia
     * untuk dropdown filter di halaman admin.
     * 
     * @param mixed $events
     * @param Request $request
     * @return \Illuminate\View\View
     */
    protected function renderIndexView($events, Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.index', compact('events', 'categories', 'panitia'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.create', compact('categories', 'panitia'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'panitia_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

    public function show(Event $event)
    {
        $event->load(['category', 'panitia', 'ticketTypes', 'registrations']);
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $event->load(['category', 'panitia', 'ticketTypes']);
        $categories = Category::orderBy('name')->get();
        $panitia = User::where('role', 'panitia')->orderBy('name')->get();
        
        return view('admin.events.edit', compact('event', 'categories', 'panitia'));
    }

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

    public function approve(Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->with('error', 'Hanya event draft yang bisa disetujui.');
        }
        
        $event->approve(auth()->id());
        
        return back()->with('success', "Event \"{$event->title}\" telah DISETUJUI dan sekarang AKTIF!");
    }

    public function reject(Request $request, Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->with('error', 'Hanya event draft yang bisa ditolak.');
        }
        
        $event->reject($request->reason ?? 'Ditolak oleh admin');
        
        return back()->with('success', "Event \"{$event->title}\" telah DITOLAK.");
    }
}