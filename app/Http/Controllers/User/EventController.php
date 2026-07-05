<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Registration;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category')
            ->where('status', 'active')
            ->where('event_date', '>=', now()->subDay());
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        $events = $query->orderBy('event_date', 'asc')->paginate(9);
        $categories = Category::all();
        
        return view('events.index', compact('events', 'categories'));
    }
    
    public function show(Event $event)
    {
        if ($event->status !== 'active') {
            abort(404);
        }
        
        $ticketTypes = $event->ticketTypes()->get();

        // ==========================================
        // LOGIKA REGISTRASI USER UNTUK EVENT INI
        // ==========================================
        $pendingReg = null;
        $paidReg = null;
        $failedCount = 0;
        $remainingAttempts = 0;
        $canRegister = false;

        if (auth()->check()) {
            $user = auth()->user();
            $userRegistrations = Registration::where('event_id', $event->id)
                ->where('user_email', $user->email)
                ->get();

            $pendingReg = $userRegistrations->where('payment_status', 'pending')->first();
            $paidReg    = $userRegistrations->where('payment_status', 'paid')->first();
            $failedCount = $userRegistrations->where('payment_status', 'failed')->count();
            $remainingAttempts = max(0, 2 - $failedCount);
            $canRegister = $event->canRegister() && !$pendingReg && !$paidReg && $remainingAttempts > 0;
        }

        return view('events.show', compact(
            'event',
            'ticketTypes',
            'pendingReg',
            'paidReg',
            'failedCount',
            'remainingAttempts',
            'canRegister'
        ));
    }
}