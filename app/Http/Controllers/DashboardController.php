<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik untuk dashboard
        $stats = [
            'total_events' => 3,
            'total_participants' => 95,
            'total_revenue' => 4250000,
            'upcoming_events' => 2
        ];
        
        $recentRegistrations = [
            ['id' => 1, 'name' => 'Budi Santoso', 'event' => 'Seminar AI 2026', 'ticket' => 'Regular', 'status' => 'paid'],
            ['id' => 2, 'name' => 'Siti Aminah', 'event' => 'Workshop Laravel', 'ticket' => 'VIP', 'status' => 'pending'],
            ['id' => 3, 'name' => 'Joko Widodo', 'event' => 'Seminar AI 2026', 'ticket' => 'Early Bird', 'status' => 'paid'],
        ];
        
        return view('dashboard', compact('stats', 'recentRegistrations'));
    }
}