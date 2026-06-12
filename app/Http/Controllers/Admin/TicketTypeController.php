<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function index(Event $event)
    {
        $ticketTypes = TicketType::where('event_id', $event->id)->get();
        return view('admin.ticket-types.index', compact('event', 'ticketTypes'));
    }
    
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1'
        ]);
        
        $validated['event_id'] = $event->id;
        $validated['registered'] = 0;
        
        TicketType::create($validated);
        
        return redirect()->route('admin.events.ticket-types.index', $event)
            ->with('success', 'Jenis tiket berhasil ditambahkan!');
    }
    
    public function update(Request $request, Event $event, TicketType $ticketType)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:' . $ticketType->registered
        ]);
        
        $ticketType->update($validated);
        
        return redirect()->route('admin.events.ticket-types.index', $event)
            ->with('success', 'Jenis tiket berhasil diupdate!');
    }
    
    public function destroy(Event $event, TicketType $ticketType)
    {
        if ($ticketType->registrations()->count() > 0) {
            return back()->with('error', 'Tiket tidak dapat dihapus karena sudah ada pendaftar!');
        }
        
        $ticketType->delete();
        
        return redirect()->route('admin.events.ticket-types.index', $event)
            ->with('success', 'Jenis tiket berhasil dihapus!');
    }
}