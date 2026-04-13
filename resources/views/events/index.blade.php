@extends('layouts.app')

@section('title', 'Daftar Event')

@section('content')
<h1 class="text-4xl font-bold text-gray-800 mb-8">Event Terbaru</h1>

<!-- Form Pencarian -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <form action="{{ route('events.index') }}" method="GET" class="flex flex-wrap gap-4">
        <input type="text" name="search" placeholder="Cari event..." value="{{ request('search') }}" 
               class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
        <select name="category" class="w-48 border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-search"></i> Cari
        </button>
    </form>
</div>

<!-- Grid Event -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($events as $event)
    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
        @if($event->poster)
            <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-purple-600 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-5xl text-white opacity-50"></i>
            </div>
        @endif
        <div class="p-4">
            <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded">{{ $event->category->name }}</span>
            <h3 class="text-xl font-bold mt-2">{{ $event->title }}</h3>
            <p class="text-gray-600 text-sm mt-2">{{ Str::limit($event->description, 100) }}</p>
            <div class="mt-4 text-sm text-gray-500">
                <p><i class="fas fa-calendar"></i> {{ $event->event_date->format('d F Y') }}</p>
                <p><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</p>
            </div>
            <a href="{{ route('events.show', $event) }}" class="block text-center bg-purple-600 text-white py-2 rounded-lg mt-4 hover:bg-purple-700 transition">
                Lihat Detail
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12">
        <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
        <p class="text-gray-500">Belum ada event tersedia</p>
    </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $events->links() }}
</div>
@endsection