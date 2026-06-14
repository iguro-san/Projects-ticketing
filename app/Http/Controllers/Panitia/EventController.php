<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::where('panitia_id', auth()->id())
            ->with(['category'])->withCount('registrations');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('location', 'like', "%{$s}%"));
        }
        $events = $query->orderBy('event_date', 'desc')->paginate(10);
        return view('panitia.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
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
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'ticket_names' => 'required|array|min:1',
            'ticket_names.*' => 'required|string|min:1',
            'ticket_prices' => 'required|array|min:1',
            'ticket_prices.*' => 'required|numeric|min:0',
            'ticket_quotas' => 'required|array|min:1',
            'ticket_quotas.*' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters/' . date('Y/m'), 'public');
        }

        $validated['panitia_id'] = auth()->id();
        $validated['status'] = 'draft';

        DB::beginTransaction();
        try {
            $event = Event::create($validated);
            foreach ($validated['ticket_names'] as $i => $name) {
                TicketType::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'price' => $validated['ticket_prices'][$i],
                    'quota' => $validated['ticket_quotas'][$i],
                    'registered' => 0,
                ]);
            }
            DB::commit();
            return redirect()->route('panitia.events.index')->with('success', 'Event dibuat! Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);
        $event->load('ticketTypes');
        $categories = Category::orderBy('name')->get();
        return view('panitia.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'ticket_names' => 'nullable|array',
            'ticket_names.*' => 'nullable|string|min:1',
            'ticket_prices' => 'nullable|array',
            'ticket_prices.*' => 'nullable|numeric|min:0',
            'ticket_quotas' => 'nullable|array',
            'ticket_quotas.*' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster) Storage::disk('public')->delete($event->poster);
            $validated['poster'] = $request->file('poster')->store('posters/' . date('Y/m'), 'public');
        }

        $event->update($validated);

        if ($request->has('ticket_names') && is_array($request->ticket_names)) {
            foreach ($request->ticket_names as $i => $name) {
                if (!empty($name) && isset($request->ticket_prices[$i]) && isset($request->ticket_quotas[$i])) {
                    TicketType::create([
                        'event_id' => $event->id,
                        'name' => $name,
                        'price' => $request->ticket_prices[$i],
                        'quota' => $request->ticket_quotas[$i],
                        'registered' => 0,
                    ]);
                }
            }
        }

        return redirect()->route('panitia.events.index')->with('success', 'Event diupdate!');
    }

    public function registrations(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);
        
        // Auto-cancel expired
        Registration::where('event_id', $event->id)
            ->where('payment_status', 'pending')
            ->where('payment_deadline', '<', now())
            ->each(fn($r) => $r->cancel('Batas waktu habis'));

        $registrations = $event->registrations()->with(['ticketType', 'user'])
            ->orderBy('created_at', 'desc')->paginate(20);
        return view('panitia.events.registrations', compact('event', 'registrations'));
    }

    public function exportRegistrations(Event $event)
    {
        if ($event->panitia_id !== auth()->id()) abort(403);
        $registrations = $event->registrations()->with('ticketType')->get();
        $filename = 'peserta_' . str_replace(' ', '_', $event->title) . '_' . date('Ymd') . '.csv';
        return response()->streamDownload(function() use ($registrations) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No', 'Nama', 'Email', 'Tiket', 'Harga', 'Status', 'Tanggal']);
            foreach ($registrations as $r) {
                fputcsv($out, [$r->registration_number, $r->user_name, $r->user_email, $r->ticketType->name, number_format($r->ticketType->price,0,',','.'), $r->payment_status, $r->registered_at->format('d/m/Y H:i')]);
            }
            fclose($out);
        }, $filename);
    }
}