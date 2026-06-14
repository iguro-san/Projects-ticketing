@extends('layouts.app')

@section('title', 'Dashboard Panitia')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#141E46]">Dashboard Panitia</h1>
        <p class="text-gray-600 mt-2">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Event</p>
                    <p class="text-3xl font-bold text-[#B6771D]">{{ $stats['total_events'] }}</p>
                </div>
                <div class="bg-[#B6771D]/10 rounded-full p-3">
                    <i class="fas fa-calendar-alt text-[#B6771D] text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Event Aktif</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_events'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-play-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Peserta</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_registrations'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Registrations (HANYA 3 TERBARU) --}}
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-[#141E46]">
                <i class="fas fa-clock mr-2 text-[#B6771D]"></i>3 Pendaftar Terbaru
            </h2>
        </div>
        <div class="p-6">
            @forelse($recentRegistrations as $reg)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $reg->user_name }}</p>
                        <p class="text-sm text-gray-500">{{ $reg->event->title }}</p>
                        <p class="text-xs text-gray-400">{{ $reg->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                        @elseif($reg->payment_status == 'pending') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($reg->payment_status) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada pendaftaran</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- My Events --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-[#141E46]">Event Saya</h2>
            <a href="{{ route('panitia.events.create') }}" 
               class="bg-[#760031] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#760031]/80 transition">
                <i class="fas fa-plus mr-1"></i> Buat Event
            </a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($myEvents as $event)
                    <div class="border rounded-lg p-4 hover:shadow transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $event->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ $event->event_date->format('d F Y') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $event->registrations_count }} peserta
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded
                                    @if($event->status == 'active') bg-green-100 text-green-700
                                    @elseif($event->status == 'draft') bg-gray-100 text-gray-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                                <div class="mt-2">
                                    <a href="{{ route('panitia.events.edit', $event) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 text-sm mr-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8">
                        <p class="text-gray-500">Belum ada event. <a href="{{ route('panitia.events.create') }}" class="text-[#B6771D]">Buat event pertama!</a></p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection