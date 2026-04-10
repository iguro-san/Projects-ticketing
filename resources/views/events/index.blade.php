@extends('layouts.app')

@section('title', 'Daftar Event')

@section('content')
<div class="container">
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('events.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari event..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $events = session('events', [
            ['id' => 1, 'title' => 'Seminar AI 2026', 'category_id' => 1, 'event_date' => '2026-03-20', 'location' => 'Jakarta Convention Center', 'description' => 'Belajar tentang Artificial Intelligence bersama para ahli. Cocok untuk mahasiswa dan profesional IT.'],
            ['id' => 2, 'title' => 'Workshop Laravel', 'category_id' => 2, 'event_date' => '2026-03-25', 'location' => 'Bandung Digital Valley', 'description' => 'Praktik langsung Laravel 12 dari dasar hingga mahir. Hands-on coding bersama mentor berpengalaman.'],
            ['id' => 3, 'title' => 'Informatics Fair 2026', 'category_id' => 3, 'event_date' => '2026-04-01', 'location' => 'Surabaya Convention Hall', 'description' => 'Pameran teknologi terbaru dari berbagai startup dan perusahaan IT.'],
        ]);
        
        if(request('search')) {
            $search = request('search');
            $events = array_filter($events, function($event) use ($search) {
                return stripos($event['title'], $search) !== false || stripos($event['description'], $search) !== false;
            });
        }
    @endphp

    <div class="row">
        @forelse($events as $event)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $event['title'] }}</h5>
                    <div class="mb-2">
                        <span class="badge bg-primary">
                            @php
                                $categories = [1 => 'Seminar', 2 => 'Workshop', 3 => 'Expo', 4 => 'Conference'];
                            @endphp
                            {{ $categories[$event['category_id']] ?? 'Event' }}
                        </span>
                    </div>
                    <p class="card-text text-muted">
                        <small><i class="fas fa-calendar"></i> {{ date('d M Y', strtotime($event['event_date'])) }}</small><br>
                        <small><i class="fas fa-map-marker-alt"></i> {{ $event['location'] }}</small>
                    </p>
                    <p class="card-text">{{ Str::limit($event['description'], 100) }}</p>
                    <a href="{{ route('events.detail', $event['id']) }}" class="btn btn-primary">
                        <i class="fas fa-info-circle"></i> Detail Event
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h4>Tidak ada event ditemukan</h4>
                <p class="text-muted">Coba kata kunci pencarian yang lain.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection