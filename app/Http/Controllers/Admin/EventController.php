<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $events = Event::with('category')->orderBy('event_date', 'desc')->paginate(10);
        return view('admin.events.index', compact('events'));
    }
    
    public function create()
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $validated = $request->validate([
            'title' => 'required|min:3',
            'description' => 'required',
            'event_date' => 'required|date|after:today',
            'location' => 'required',
            'category_id' => 'required|exists:categories,id',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }
        
        $validated['status'] = 'active';
        
        Event::create($validated);
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat!');
    }
    
    public function edit(Event $event)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }
    
    public function update(Request $request, Event $event)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        $validated = $request->validate([
            'title' => 'required|min:3',
            'description' => 'required',
            'event_date' => 'required|date',
            'location' => 'required',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,completed,cancelled',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        if ($request->hasFile('poster')) {
            if ($event->poster) {
                Storage::disk('public')->delete($event->poster);
            }
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }
        
        $event->update($validated);
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diupdate!');
    }
    
    public function destroy(Event $event)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        
        if ($event->registrations()->count() > 0) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah ada peserta terdaftar!');
        }
        
        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
        }
        
        $event->delete();
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}