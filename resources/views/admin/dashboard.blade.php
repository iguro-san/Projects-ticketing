@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-[#141E46]">Admin Dashboard</h1>
    <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}!</p>
</div>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Event --}}
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-[#B6771D]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Event</p>
                <p class="text-3xl font-bold text-[#B6771D]">{{ $stats['total_events'] }}</p>
            </div>
            <div class="bg-[#B6771D]/10 rounded-full p-3">
                <i class="fas fa-calendar-alt text-[#B6771D] text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Event Aktif --}}
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Event Aktif</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_events'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-play-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Peserta --}}
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Peserta</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_registrations'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Panitia --}}
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Panitia</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['total_panitia'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-user-tie text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Announcement Widget -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-[#141E46]">
            <i class="fas fa-bullhorn mr-2 text-[#B6771D]"></i>Pengumuman Terbaru
        </h2>
        <a href="{{ route('admin.announcements.create') }}" class="text-sm text-[#B6771D] hover:text-[#B6771D]/80">
            <i class="fas fa-plus mr-1"></i>Buat Pengumuman
        </a>
    </div>
    <div class="space-y-3">
        @php
            $latestAnnouncements = \App\Models\Announcement::with('creator')
                ->where('is_active', true)
                ->latest('published_at')
                ->take(5)
                ->get();
        @endphp
        @forelse($latestAnnouncements as $ann)
        <div class="border-l-4 border-[#B6771D] pl-4 py-2 hover:bg-gray-50 transition">
            <p class="font-semibold text-gray-800">{{ $ann->title }}</p>
            <p class="text-sm text-gray-600">{{ Str::limit($ann->content, 100) }}</p>
            <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                <span><i class="fas fa-user mr-1"></i>{{ $ann->creator->name }}</span>
                <span><i class="fas fa-clock mr-1"></i>{{ $ann->published_at->diffForHumans() }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs
                    @if($ann->target == 'all') bg-purple-100 text-purple-700
                    @elseif($ann->target == 'panitia') bg-blue-100 text-blue-700
                    @else bg-green-100 text-green-700 @endif">
                    <i class="fas {{ $ann->target == 'all' ? 'fa-users' : ($ann->target == 'panitia' ? 'fa-user-tie' : 'fa-user') }} mr-1"></i>
                    {{ $ann->target == 'all' ? 'Semua User' : ($ann->target == 'panitia' ? 'Panitia' : 'User') }}
                </span>
            </div>
        </div>
        @empty
        <div class="text-center py-6">
            <i class="fas fa-bullhorn text-4xl text-gray-300 mb-2"></i>
            <p class="text-gray-500">Belum ada pengumuman</p>
            <a href="{{ route('admin.announcements.create') }}" class="inline-block mt-2 text-sm text-[#B6771D] hover:text-[#B6771D]/80">
                Buat pengumuman pertama
            </a>
        </div>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Ilustrasi Banner --}}
    <div class="bg-gradient-to-br from-[#141E46] to-[#B6771D] rounded-xl shadow-lg p-8 text-white flex flex-col justify-center items-center text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 opacity-10">
            <i class="fas fa-ticket-alt text-9xl"></i>
        </div>
        <i class="fas fa-ticket-alt text-6xl mb-4 relative z-10"></i>
        <h2 class="text-2xl font-bold mb-2 relative z-10">EventKu</h2>
        <p class="text-lg mb-6 opacity-90 relative z-10">Sistem Manajemen Event</p>
        <div class="grid grid-cols-3 gap-3 w-full relative z-10">
            <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                <i class="fas fa-calendar-check text-2xl mb-1"></i>
                <p class="text-xs">Kelola Event</p>
            </div>
            <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                <i class="fas fa-qrcode text-2xl mb-1"></i>
                <p class="text-xs">Tiket Digital</p>
            </div>
            <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                <i class="fas fa-chart-line text-2xl mb-1"></i>
                <p class="text-xs">Laporan</p>
            </div>
        </div>
    </div>

    {{-- Pendaftaran Terbaru --}}
    <div class="bg-white rounded-xl shadow lg:col-span-2">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-[#141E46]">
                <i class="fas fa-clock mr-2 text-[#B6771D]"></i>Pendaftaran Terbaru
            </h2>
            <a href="{{ route('admin.registrations.index') }}" class="text-sm text-[#B6771D] hover:text-[#B6771D]/80 font-medium">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-6">
            @forelse($recentRegistrations as $reg)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-2 rounded transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-[#B6771D]/10 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-[#B6771D]"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $reg->user_name }}</p>
                            <p class="text-xs text-gray-500">{{ $reg->event->title ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs text-gray-400">{{ $reg->created_at->diffForHumans() }}</span>
                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                            @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                            @elseif($reg->payment_status == 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($reg->payment_status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada pendaftaran</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Upcoming Events --}}
@if(count($upcomingEvents) > 0)
<div class="mt-8">
    <h2 class="text-xl font-bold text-[#141E46] mb-4">
        <i class="fas fa-calendar-star mr-2 text-[#B6771D]"></i>Event Mendatang
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($upcomingEvents as $event)
            <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs bg-[#B6771D]/10 text-[#B6771D] px-2 py-1 rounded-full">
                            {{ $event->category->name ?? 'Event' }}
                        </span>
                        <h3 class="font-semibold text-gray-800 mt-2">{{ $event->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $event->event_date->format('d F Y') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-user-tie mr-1"></i> {{ $event->panitia->name ?? '-' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($event->status == 'active') bg-green-100 text-green-700
                            @elseif($event->status == 'draft') bg-gray-100 text-gray-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                        @if($event->suspension_status === 'pending')
                            <span class="block mt-1 px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">
                                <i class="fas fa-pause mr-1"></i>Pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection