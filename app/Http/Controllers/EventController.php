<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    // Data simulasi event
    private function getEvents()
    {
        return [
            ['id' => 1, 'title' => 'Seminar AI 2026', 'description' => 'Belajar tentang Artificial Intelligence', 'category' => 'Seminar', 'date' => '2026-03-20', 'location' => 'Jakarta Convention Center', 'poster' => 'ai-seminar.jpg'],
            ['id' => 2, 'title' => 'Workshop Laravel', 'description' => 'Praktik langsung Laravel 12', 'category' => 'Workshop', 'date' => '2026-03-25', 'location' => 'Bandung Digital Valley', 'poster' => 'laravel-workshop.jpg'],
            ['id' => 3, 'title' => 'Informatics Fair 2026', 'description' => 'Pameran teknologi terbaru', 'category' => 'Expo', 'date' => '2026-04-01', 'location' => 'Surabaya Convention Hall', 'poster' => 'informatics-fair.jpg'],
        ];
    }

    public function index(Request $request)
    {
        $events = $this->getEvents();
        
        // Fitur pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $events = array_filter($events, function($event) use ($search) {
                return stripos($event['title'], $search) !== false || 
                       stripos($event['description'], $search) !== false;
            });
        }
        
        return view('events', compact('events'));
    }

    public function detail($id)
    {
        $events = $this->getEvents();
        $event = collect($events)->firstWhere('id', $id);
        
        // Data tiket untuk event
        $ticketTypes = [
            ['id' => 1, 'name' => 'Regular', 'price' => 50000, 'quota' => 100, 'registered' => 45],
            ['id' => 2, 'name' => 'VIP', 'price' => 150000, 'quota' => 50, 'registered' => 20],
            ['id' => 3, 'name' => 'Early Bird', 'price' => 25000, 'quota' => 30, 'registered' => 30],
        ];
        
        return view('event_detail', compact('event', 'ticketTypes', 'id'));
    }
}