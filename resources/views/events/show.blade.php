@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="bg-white rounded-lg shadow p-8 mb-8">
    <div class="mb-4">
        <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm">{{ $event->category->name }}</span>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $event->title }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-gray-600">
        <p><i class="fas fa-calendar-alt text-purple-600 w-6"></i> {{ $event->event_date->format('l, d F Y') }}</p>
        <p><i class="fas fa-map-marker-alt text-purple-600 w-6"></i> {{ $event->location }}</p>
    </div>
    <div class="mb-6">
        <h3 class="font-bold text-lg mb-2">Deskripsi Event</h3>
        <p class="text-gray-600">{{ $event->description }}</p>
    </div>
</div>

<h2 class="text-2xl font-bold text-gray-800 mb-4">Pilih Tiket</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($ticketTypes as $ticket)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800">{{ $ticket->name }}</h3>
        <p class="text-3xl font-bold text-purple-600 mt-2">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
        <p class="text-gray-500 text-sm mt-2">Sisa kuota: {{ $ticket->remaining_quota }}</p>
        
        @if($ticket->isAvailable())
            <form action="{{ route('events.register', $event) }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="ticket_type_id" value="{{ $ticket->id }}">
                <input type="hidden" name="name" value="{{ auth()->user()->name ?? '' }}">
                <input type="hidden" name="email" value="{{ auth()->user()->email ?? '' }}">
                <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                    Daftar Sekarang
                </button>
            </form>
        @else
            <button class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed" disabled>
                Tiket Habis
            </button>
        @endif
    </div>
    @endforeach
</div>

@if(!auth()->check())
<div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
    <p class="text-yellow-700">Silakan <a href="{{ route('login') }}" class="font-bold underline">login</a> terlebih dahulu untuk mendaftar event.</p>
</div>
@endif
@endsection