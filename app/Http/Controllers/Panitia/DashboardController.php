<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $panitiaId = auth()->id();
        
        $stats = [
            'total_events' => Event::where('panitia_id', $panitiaId)->count(),
            'active_events' => Event::where('panitia_id', $panitiaId)
                ->where('status', 'active')
                ->where('event_date', '>=', now())
                ->count(),
            'completed_events' => Event::where('panitia_id', $panitiaId)
                ->where('status', 'completed')
                ->count(),
            'total_registrations' => Registration::whereHas('event', function($q) use ($panitiaId) {
                $q->where('panitia_id', $panitiaId);
            })->count(),
            'paid_registrations' => Registration::whereHas('event', function($q) use ($panitiaId) {
                $q->where('panitia_id', $panitiaId);
            })->where('payment_status', 'paid')->count(),
            'total_revenue' => Registration::whereHas('event', function($q) use ($panitiaId) {
                $q->where('panitia_id', $panitiaId);
            })->where('payment_status', 'paid')->sum('amount_paid'),
            'monthly_revenue' => Registration::whereHas('event', function($q) use ($panitiaId) {
                $q->where('panitia_id', $panitiaId);
            })->where('payment_status', 'paid')
              ->whereMonth('paid_at', Carbon::now()->month)
              ->whereYear('paid_at', Carbon::now()->year)
              ->sum('amount_paid'),
        ];
        
        $myEvents = Event::where('panitia_id', $panitiaId)
            ->with(['category'])
            ->withCount('registrations')
            ->latest()
            ->take(10)
            ->get();
        
        $upcomingEvents = Event::where('panitia_id', $panitiaId)
            ->where('status', 'active')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();
        
        // HANYA 3 PENDAFTAR TERBARU
        $recentRegistrations = Registration::whereHas('event', function($q) use ($panitiaId) {
                $q->where('panitia_id', $panitiaId);
            })
            ->with(['event', 'ticketType'])
            ->latest()
            ->take(3)
            ->get();
        
        return view('panitia.dashboard', compact(
            'stats', 'myEvents', 'upcomingEvents', 'recentRegistrations'
        ));
    }
}