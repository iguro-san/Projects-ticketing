@extends('layouts.app')

@section('title', 'Event Management System')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-12 mb-12 text-white">
    <div class="max-w-3xl">
        <h1 class="text-5xl font-bold mb-4">Temukan Event Terbaik!</h1>
        <p class="text-xl mb-8 opacity-90">Daftar dan ikuti berbagai event menarik dari seminar, konser, workshop, dan masih banyak lagi.</p>
        <div class="flex gap-4">
            @guest
                <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Daftar Sekarang
                </a>
            @else
                <a href="{{ route('my.tickets') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Tiket Saya
                </a>
            @endguest
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <form action="{{ route('home') }}" method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Cari event..." 
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600">
            </div>
        </div>
        <select name="category" class="px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 min-w-[200px]">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-search mr-2"></i> Cari
        </button>
    </form>
</div>

<!-- Events Grid -->
@if($events->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                @if($event->poster)
                    <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" 
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-5xl text-white opacity-50"></i>
                    </div>
                @endif
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                            {{ $event->category->name }}
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            {{ $event->event_date->diffForHumans() }}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $event->title }}</h3>
                    
                    <div class="space-y-2 text-gray-600 mb-4">
                        <p class="text-sm">
                            <i class="fas fa-calendar-alt w-5 text-purple-600"></i>
                            {{ $event->event_date->format('d F Y') }}
                        </p>
                        <p class="text-sm">
                            <i class="fas fa-map-marker-alt w-5 text-purple-600"></i>
                            {{ $event->location }}
                        </p>
                        <p class="text-sm">
                            <i class="fas fa-ticket-alt w-5 text-purple-600"></i>
                            {{ $event->available_tickets }} tiket tersedia
                        </p>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            @if($event->ticketTypes->min('price') == 0)
                                <span class="text-green-600 font-bold">GRATIS</span>
                            @else
                                <span class="text-gray-800 font-bold">
                                    Mulai Rp {{ number_format($event->ticketTypes->min('price'), 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('events.show', $event) }}" 
                           class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $events->links() }}
    </div>
@else
    <div class="text-center py-16">
        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada event ditemukan</h3>
        <p class="text-gray-500">Coba ubah filter pencarian atau cek kembali nanti.</p>
    </div>
@endif
@endsection