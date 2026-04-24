<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['category', 'ticketTypes'])
            ->where('status', 'active')
            ->where('event_date', '>=', now()->format('Y-m-d')); // Perbaikan format date
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $events = $query->orderBy('event_date', 'asc')
            ->paginate(12);
            
        $categories = Category::has('events')->get();
        
        return view('home', compact('events', 'categories'));
    }

    public function show(Event $event) // Perbaikan: parameter $event bukan Event
    {
        if ($event->status !== 'active') {
            abort(404);
        }
        
        $event->load(['category', 'ticketTypes' => function($q) {
            $q->where('is_active', true);
        }, 'panitia']);
        
        $availableTickets = $event->ticketTypes->filter(function($ticket) {
            return $ticket->isAvailable();
        });
        
        return view('events.show', compact('event', 'availableTickets'));
    }
}