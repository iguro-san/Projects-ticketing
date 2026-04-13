@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
<p class="text-gray-600 mb-8">Selamat datang, {{ auth()->user()->name }}!</p>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Event</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_events'] }}</p>
            </div>
            <i class="fas fa-calendar-alt text-4xl text-gray-300"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Event Aktif</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_events'] }}</p>
            </div>
            <i class="fas fa-play-circle text-4xl text-gray-300"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Peserta</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_registrations'] }}</p>
            </div>
            <i class="fas fa-users text-4xl text-gray-300"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pendapatan</p>
                <p class="text-3xl font-bold text-yellow-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-money-bill-wave text-4xl text-gray-300"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Menu Cepat -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Menu Cepat</h2>
        <div class="space-y-3">
            <a href="{{ route('admin.events.index') }}" class="block w-full bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fas fa-calendar-plus"></i> Kelola Event
            </a>
            <a href="{{ route('admin.categories.index') }}" class="block w-full bg-green-500 text-white text-center py-2 rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-tags"></i> Kelola Kategori
            </a>
            <a href="{{ route('admin.events.create') }}" class="block w-full bg-purple-500 text-white text-center py-2 rounded-lg hover:bg-purple-600 transition">
                <i class="fas fa-plus-circle"></i> Buat Event Baru
            </a>
        </div>
    </div>
    
    <!-- Pendaftaran Terbaru -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Pendaftaran Terbaru</h2>
        <div class="space-y-3">
            @forelse($recentRegistrations as $reg)
            <div class="border-b pb-3">
                <p class="font-semibold">{{ $reg->user_name }}</p>
                <p class="text-sm text-gray-600">{{ $reg->event->title }}</p>
                <span class="text-xs px-2 py-1 rounded
                    @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                    @else bg-yellow-100 text-yellow-700 @endif">
                    {{ ucfirst($reg->payment_status) }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Belum ada pendaftaran</p>
            @endforelse
        </div>
    </div>
</div>
@endsection