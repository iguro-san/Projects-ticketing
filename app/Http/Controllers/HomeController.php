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
            ->where('event_date', '>=', now()->format('Y-m-d'));
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $events = $query->orderBy('event_date', 'asc')->paginate(12);
        $categories = Category::has('events')->get();
        
        return view('home', compact('events', 'categories'));
    }
}