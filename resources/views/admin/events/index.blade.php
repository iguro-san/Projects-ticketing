@extends('layouts.app')

@section('title', 'Histori Event')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-history mr-2 text-purple-600"></i>Histori Event
            </h1>
            <p class="text-sm text-gray-500 mt-1">Semua event yang pernah dibuat oleh panitia</p>
        </div>
        <a href="{{ route('admin.events.create') }}" 
           class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm whitespace-nowrap">
            <i class="fas fa-plus mr-1"></i> Buat Event
        </a>
    </div>
    
    {{-- Filter --}}
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <form action="{{ route('admin.events.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Status --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            
            {{-- Waktu --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Waktu</label>
                <select name="time_filter" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Waktu</option>
                    <option value="upcoming" {{ request('time_filter') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="past" {{ request('time_filter') == 'past' ? 'selected' : '' }}>Telah Lewat</option>
                </select>
            </div>
            
            {{-- Kategori --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Kategori</label>
                <select name="category_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Panitia --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Panitia</label>
                <select name="panitia_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Panitia</option>
                    @foreach($panitia as $p)
                        <option value="{{ $p->id }}" {{ request('panitia_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Search & Buttons --}}
            <div class="sm:col-span-2 lg:col-span-4 flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Cari Event</label>
                    <input type="text" name="search" placeholder="Judul atau lokasi event..." value="{{ request('search') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                </div>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition whitespace-nowrap">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                <a href="{{ route('admin.events.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition whitespace-nowrap">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    {{-- Info Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-purple-50 rounded-lg p-3 text-center border border-purple-200">
            <p class="text-xs text-purple-500 font-medium">Total</p>
            <p class="text-xl font-bold text-purple-600">{{ $events->total() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-3 text-center border border-green-200">
            <p class="text-xs text-green-500 font-medium">Aktif</p>
            <p class="text-xl font-bold text-green-600">
                @php
                    $activeCount = $events->filter(fn($e) => $e->status == 'active' && $e->event_date >= now())->count();
                @endphp
                {{ $activeCount }}
            </p>
        </div>
        <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-200">
            <p class="text-xs text-blue-500 font-medium">Berakhir</p>
            <p class="text-xl font-bold text-blue-600">
                @php
                    $endedCount = $events->filter(fn($e) => $e->event_date < now())->count();
                @endphp
                {{ $endedCount }}
            </p>
        </div>
        <div class="bg-red-50 rounded-lg p-3 text-center border border-red-200">
            <p class="text-xs text-red-500 font-medium">Batal/Draft</p>
            <p class="text-xl font-bold text-red-600">
                @php
                    $cancelCount = $events->filter(fn($e) => in_array($e->status, ['cancelled', 'draft']))->count();
                @endphp
                {{ $cancelCount }}
            </p>
        </div>
    </div>
    
    {{-- Tabel Event --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Panitia</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Peserta</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 transition">
                    {{-- No --}}
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $loop->iteration + ($events->firstItem() - 1) }}
                    </td>
                    
                    {{-- Event --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            @if($event->poster)
                                <img src="{{ Storage::url($event->poster) }}" 
                                     class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-purple-500 text-sm"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $event->title }}</p>
                                <p class="text-xs text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($event->location, 30) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    
                    {{-- Kategori --}}
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                            {{ $event->category->name ?? '-' }}
                        </span>
                    </td>
                    
                    {{-- Tanggal --}}
                    <td class="px-4 py-3 text-sm">
                        <span class="{{ $event->event_date < now() ? 'text-red-600' : 'text-green-600' }} font-medium">
                            {{ $event->event_date->format('d/m/Y') }}
                        </span>
                        @if($event->event_date < now())
                            <br><span class="text-xs text-red-400">Telah lewat</span>
                        @else
                            <br><span class="text-xs text-gray-400">{{ $event->event_date->diffForHumans() }}</span>
                        @endif
                    </td>
                    
                    {{-- Panitia --}}
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-tie text-blue-500 text-xs"></i>
                            </div>
                            <span class="text-gray-700">{{ $event->panitia->name ?? 'Tidak ada' }}</span>
                        </div>
                    </td>
                    
                    {{-- Peserta --}}
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-users mr-1"></i>{{ $event->registrations_count }}
                        </span>
                    </td>
                    
                    {{-- Status --}}
                    <td class="px-4 py-3 text-center">
                        @if($event->status == 'active' && $event->event_date >= now())
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @elseif($event->status == 'completed' || ($event->status == 'active' && $event->event_date < now()))
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-clock mr-1"></i>Berakhir
                            </span>
                        @elseif($event->status == 'draft')
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-pencil-alt mr-1"></i>Draft
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-times-circle mr-1"></i>Batal
                            </span>
                        @endif
                    </td>
                    
                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <div class="flex justify-center gap-1">
                            <a href="{{ route('admin.events.edit', $event) }}" 
                               class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600 transition"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.events.ticket-types.index', $event) }}" 
                               class="bg-indigo-500 text-white px-2 py-1 rounded text-xs hover:bg-indigo-600 transition"
                               title="Tiket">
                                <i class="fas fa-ticket-alt"></i>
                            </a>
                            <a href="{{ route('admin.events.registrations.index', $event) }}" 
                               class="bg-green-500 text-white px-2 py-1 rounded text-xs hover:bg-green-600 transition"
                               title="Peserta">
                                <i class="fas fa-users"></i>
                            </a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600 transition"
                                        onclick="return confirm('Yakin hapus event ini?')"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <i class="fas fa-calendar-times text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-lg">Belum ada event</p>
                        <p class="text-gray-400 text-sm">Event yang dibuat panitia akan muncul di sini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-2">
        <p class="text-sm text-gray-500">
            Menampilkan {{ $events->firstItem() ?? 0 }} - {{ $events->lastItem() ?? 0 }} 
            dari {{ $events->total() }} event
        </p>
        {{ $events->links() }}
    </div>
</div>
@endsection