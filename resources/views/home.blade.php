@extends('layouts.app')

@section('title', 'Event Management System')

@section('content')

<!-- Hero Section with Background Image -->
<div class="relative rounded-2xl mb-12 overflow-hidden min-h-[400px] flex items-center">
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('{{ asset('images/bg-hero2.png') }}');">
    </div>
    <div class="absolute inset-0 bg-black/40"></div>
    `
    <!-- Konten Hero -->
    <div class="relative z-10 container mx-auto px-4 md:px-12">
        <div class="max-w-3xl">
            <h1 class="text-white text-4xl md:text-5xl font-bold mb-4">Temukan Event Terbaik!</h1>
            <p class="text-white text-lg md:text-xl mb-8">Daftar dan ikuti berbagai event menarik dari seminar, konser, workshop, dan masih banyak lagi.</p>
            <div class="flex gap-4">
                @guest
                    <a href="{{ route('register') }}" class="bg-[#DAA016] text-white px-6 md:px-8 py-3 rounded-lg font-semibold hover:bg-[#B6771D]/80 transition shadow-md">
                        Daftar Sekarang
                    </a>
                @else
                    <a href="{{ route('my.tickets') }}" class="bg-[#DAA016] text-white px-6 md:px-8 py-3 rounded-lg font-semibold hover:bg-[#B6771D]/80 transition shadow-md">
                        Tiket Saya
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <form action="{{ route('home') }}" method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Cari event..." value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-[#760031]">
            </div>
        </div>
        <select name="category" class="px-4 py-3 border rounded-lg focus:outline-none focus:border-[#760031] min-w-[200px]">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-[#760031] text-white px-6 py-3 rounded-lg hover:bg-[#5a0024] transition">
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
                    <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-r from-[#760031]-500 to-blue-500 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-5xl text-white opacity-50"></i>
                    </div>
                @endif
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 bg-[#760031]/30 text-[#760031] rounded-full text-xs font-semibold">
                            {{ $event->category->name }}
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            {{ $event->event_date->diffForHumans() }}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $event->title }}</h3>
                    
                    <div class="space-y-2 text-gray-600 mb-4">
                        <p class="text-sm"><i class="fas fa-calendar-alt w-5 text-[#760031]"></i> {{ $event->event_date->translatedFormat('d F Y') }}</p>
                        <p class="text-sm"><i class="fas fa-map-marker-alt w-5 text-[#760031]"></i> {{ $event->location }}</p>
                        <p class="text-sm"><i class="fas fa-ticket-alt w-5 text-[#760031]"></i> {{ $event->available_tickets }} tiket tersedia</p>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                        @if($event->is_free)
                            <span class="text-green-600 font-bold text-lg">🎉 GRATIS</span>
                        @elseif($event->min_price == 0)
                            <span class="text-green-600 font-bold">Mulai GRATIS</span>
                        @else
                            <span class="text-gray-800 font-bold">Mulai Rp {{ number_format($event->min_price, 0, ',', '.') }}</span>
                        @endif
                        </div>
                        <a href="{{ route('events.show', $event) }}" class="bg-[#760031] text-white px-4 py-2 rounded-lg hover:bg-[#5a0024] transition text-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">{{ $events->links() }}</div>
@else
    <div class="text-center py-16">
        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada event ditemukan</h3>
        <p class="text-gray-500">Coba ubah filter pencarian atau cek kembali nanti.</p>
    </div>
@endif

@endsection