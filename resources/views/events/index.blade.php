@extends('layouts.app')

@section('title', 'Daftar Event')

@section('content')
<h1 class="text-4xl font-bold text-[#141E46] mb-8">Event Terbaru</h1>

<div class="bg-white rounded-lg shadow p-6 mb-8">
    <form action="{{ route('events.index') }}" method="GET" class="flex flex-wrap gap-4">
        <input type="text" name="search" placeholder="Cari event..." value="{{ request('search') }}" 
               class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:border-[#B6771D]">
        <select name="category" class="w-48 border rounded-lg px-3 py-2 focus:outline-none focus:border-[#B6771D]">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-[#B6771D] text-white px-6 py-2 rounded-lg hover:bg-[#B6771D]/80 transition">
            <i class="fas fa-search"></i> Cari
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($events as $event)
        <x-event-card :event="$event" />
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