<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. STATISTIK
        // ==========================================
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_panitia' => User::where('role', 'panitia')->count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')
                ->where('event_date', '>=', now())
                ->count(),
            'total_registrations' => Registration::count(),
            'pending_payments' => Registration::where('payment_status', 'pending')->count(),
            'paid_registrations' => Registration::where('payment_status', 'paid')->count(),
            'total_revenue' => Registration::where('payment_status', 'paid')
                ->sum('amount_paid'),
            'monthly_revenue' => Registration::where('payment_status', 'paid')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount_paid'),
            'today_registrations' => Registration::whereDate('created_at', today())->count(),
        ];

        // ==========================================
        // 2. PENDAFTARAN TERBARU
        // ==========================================
        $recentRegistrations = Registration::with(['event', 'user', 'ticketType'])
            ->latest()
            ->take(10)
            ->get();

        // ==========================================
        // 3. EVENT MENDATANG
        // ==========================================
        $upcomingEvents = Event::with(['category', 'panitia'])
            ->where('status', 'active')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        // ==========================================
        // 4. PENGUMUMAN TERBARU
        // ==========================================
        $latestAnnouncements = Announcement::with('creator')
            ->where('is_active', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        // ==========================================
        // 5. DATA CHART (opsional, jika diperlukan di view)
        // ==========================================
        $monthlyRevenue = Registration::where('payment_status', 'paid')
            ->whereYear('paid_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $registrationStats = Registration::select('payment_status', DB::raw('count(*) as total'))
            ->groupBy('payment_status')
            ->get()
            ->pluck('total', 'payment_status')
            ->toArray();

        // ==========================================
        // 6. KIRIM KE VIEW
        // ==========================================
        return view('admin.dashboard', compact(
            'stats',
            'recentRegistrations',
            'upcomingEvents',
            'latestAnnouncements',
            'monthlyRevenue',
            'registrationStats'
        ));
    }
}