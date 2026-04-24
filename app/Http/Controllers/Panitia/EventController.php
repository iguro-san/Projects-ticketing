<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::where('panitia_id', auth()->id())
            ->with(['category'])
            ->withCount('registrations');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $events = $query->orderBy('event_date', 'desc')
            ->paginate(10);
        
        return view('panitia.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('panitia.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')
                ->store('posters/' . date('Y/m'), 'public');
        }

        $validated['panitia_id'] = auth()->id();
        $validated['status'] = 'draft';

        Event::create($validated);

        return redirect()->route('panitia.events.index')
            ->with('success', 'Event berhasil dibuat dan menunggu persetujuan admin.');
    }

    public function edit(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke event ini.');
        }

        $categories = Category::all();
        return view('panitia.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->panitia_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster) {
                Storage::disk('public')->delete($event->poster);
            }
            $validated['poster'] = $request->file('poster')
                ->store('posters/' . date('Y/m'), 'public');
        }

        $event->update($validated);

        return redirect()->route('panitia.events.index')
            ->with('success', 'Event berhasil diupdate.');
    }

    /**
     * Melihat daftar peserta event
     */
    public function registrations(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) {
            abort(403);
        }

        $registrations = $event->registrations()
            ->with(['ticketType', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('panitia.events.registrations', compact('event', 'registrations'));
    }

    /**
     * Melihat detail pembayaran peserta
     */
    public function paymentDetail(Event $event, Registration $registration)
    {
        if ($event->panitia_id !== auth()->id()) {
            abort(403);
        }

        $registration->load(['ticketType', 'user']);
        
        return view('panitia.events.payment-detail', compact('event', 'registration'));
    }

    /**
     * Konfirmasi pembayaran oleh panitia
     */
    public function confirmPayment(Request $request, Event $event, Registration $registration)
    {
        if ($event->panitia_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:confirm,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['action'] === 'confirm') {
            $registration->markAsPaid(
                $registration->payment_method ?? 'Manual',
                $validated['notes'] ?? 'Dikonfirmasi oleh panitia'
            );
            $message = 'Pembayaran berhasil dikonfirmasi.';
        } else {
            $registration->markAsFailed($validated['notes'] ?? 'Ditolak oleh panitia');
            $message = 'Pembayaran ditolak.';
        }

        return redirect()->route('panitia.events.registrations', $event)
            ->with('success', $message);
    }

    /**
     * Export data peserta
     */
    public function exportRegistrations(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) {
            abort(403);
        }

        $registrations = $event->registrations()
            ->with('ticketType')
            ->get();

        $filename = 'peserta_' . str_replace(' ', '_', $event->title) . '_' . date('Y-m-d') . '.csv';
        
        return response()->streamDownload(function() use ($registrations) {
            $output = fopen('php://output', 'w');
            
            fputcsv($output, [
                'No Registrasi', 'Nama', 'Email', 'Tiket', 
                'Harga', 'Status Pembayaran', 'Tanggal Daftar'
            ]);
            
            foreach ($registrations as $reg) {
                fputcsv($output, [
                    $reg->registration_number,
                    $reg->user_name,
                    $reg->user_email,
                    $reg->ticketType->name,
                    number_format($reg->ticketType->price, 0, ',', '.'),
                    $reg->payment_status,
                    $reg->registered_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($output);
        }, $filename);
    }
}