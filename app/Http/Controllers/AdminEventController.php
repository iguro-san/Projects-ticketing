<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    // Data simulasi
    private $events = [];
    
    public function __construct()
    {
        $this->events = session('events', [
            ['id' => 1, 'category_id' => 1, 'title' => 'Seminar AI 2026', 'description' => 'Belajar tentang Artificial Intelligence', 'event_date' => '2026-03-20', 'location' => 'Jakarta Convention Center', 'poster' => null],
            ['id' => 2, 'category_id' => 2, 'title' => 'Workshop Laravel', 'description' => 'Praktik langsung Laravel 12', 'event_date' => '2026-03-25', 'location' => 'Bandung Digital Valley', 'poster' => null],
        ]);
    }
    
    public function index()
    {
        $events = $this->events;
        return view('admin.events.index', compact('events'));
    }
    
    public function create()
    {
        $categories = $this->getCategories();
        return view('admin.events.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3',
            'description' => 'required',
            'event_date' => 'required|date',
            'location' => 'required',
            'category_id' => 'required'
        ]);
        
        $newId = count($this->events) + 1;
        $newEvent = [
            'id' => $newId,
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'category_id' => $request->category_id,
            'poster' => null
        ];
        
        $this->events[] = $newEvent;
        session(['events' => $this->events]);
        
        return redirect('/admin/events')->with('success', 'Event berhasil dibuat!');
    }
    
    public function edit($id)
    {
        $event = collect($this->events)->firstWhere('id', $id);
        $categories = $this->getCategories();
        return view('admin.events.edit', compact('event', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        // Update logic
        return redirect('/admin/events')->with('success', 'Event berhasil diupdate!');
    }
    
    public function destroy($id)
    {
        // Delete logic
        return redirect('/admin/events')->with('success', 'Event berhasil dihapus!');
    }
    
    private function getCategories()
    {
        return [
            ['id' => 1, 'name' => 'Seminar'],
            ['id' => 2, 'name' => 'Workshop'],
            ['id' => 3, 'name' => 'Expo'],
            ['id' => 4, 'name' => 'Conference'],
        ];
    }
}