<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $stats = [
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')->count(),
            'total_registrations' => Registration::count(),
            'total_revenue' => DB::table('registrations')
                ->join('ticket_types', 'registrations.ticket_type_id', '=', 'ticket_types.id')
                ->where('registrations.payment_status', 'paid')
                ->sum(DB::raw('ticket_types.price')),
        ];
        
        $recentRegistrations = Registration::with(['event', 'ticketType'])
            ->latest()
            ->take(10)
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recentRegistrations'));
    }
}