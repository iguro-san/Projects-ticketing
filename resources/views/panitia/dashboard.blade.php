@extends('layouts.app')

@section('title', 'Dashboard Panitia')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Panitia</h1>
            <p class="text-gray-600 mt-2">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('panitia.events.create') }}" 
           class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-plus-circle mr-2"></i>Buat Event Baru
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Event</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_events'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
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

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pendapatan</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- My Events & Recent Registrations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- My Events -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Event Saya</h2>
                <a href="{{ route('panitia.events.index') }}" class="text-purple-600 hover:text-purple-800 text-sm">
                    Lihat Semua
                </a>
            </div>
            <div class="p-6">
                @forelse($myEvents as $event)
                    <div class="mb-4 pb-4 border-b last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $event->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ $event->event_date->format('d F Y') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $event->location }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded
                                @if($event->status == 'active') bg-green-100 text-green-700
                                @elseif($event->status == 'draft') bg-gray-100 text-gray-700
                                @elseif($event->status == 'completed') bg-blue-100 text-blue-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada event. <a href="{{ route('panitia.events.create') }}" class="text-purple-600">Buat event pertama Anda!</a></p>
                @endforelse
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">Pendaftaran Terbaru</h2>
            </div>
            <div class="p-6">
                @forelse($recentRegistrations as $reg)
                    <div class="mb-4 pb-4 border-b last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">{{ $reg->user_name }}</p>
                                <p class="text-sm text-gray-500">{{ $reg->event->title }}</p>
                                <p class="text-xs text-gray-400">{{ $reg->ticketType->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded
                                @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                                @elseif($reg->payment_status == 'pending') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($reg->payment_status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada pendaftaran</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection