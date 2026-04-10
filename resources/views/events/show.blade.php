@extends('layouts.app')

@section('title', 'Detail Event')

@section('content')
<div class="container">
    @php
        $events = session('events', [
            ['id' => 1, 'title' => 'Seminar AI 2026', 'category_id' => 1, 'event_date' => '2026-03-20', 'location' => 'Jakarta Convention Center', 'description' => 'Belajar tentang Artificial Intelligence bersama para ahli. Cocok untuk mahasiswa dan profesional IT.'],
            ['id' => 2, 'title' => 'Workshop Laravel', 'category_id' => 2, 'event_date' => '2026-03-25', 'location' => 'Bandung Digital Valley', 'description' => 'Praktik langsung Laravel 12 dari dasar hingga mahir. Hands-on coding bersama mentor berpengalaman.'],
            ['id' => 3, 'title' => 'Informatics Fair 2026', 'category_id' => 3, 'event_date' => '2026-04-01', 'location' => 'Surabaya Convention Hall', 'description' => 'Pameran teknologi terbaru dari berbagai startup dan perusahaan IT.'],
        ]);
        $event = collect($events)->firstWhere('id', $id);
        if(!$event) {
            $event = ['id' => $id, 'title' => 'Event', 'category_id' => 1, 'event_date' => date('Y-m-d'), 'location' => '-', 'description' => '-'];
        }
        
        $ticketTypes = session('ticket_types_' . $id, [
            ['id' => 1, 'name' => 'Regular', 'price' => 50000, 'quota' => 100, 'registered' => 45],
            ['id' => 2, 'name' => 'VIP', 'price' => 150000, 'quota' => 50, 'registered' => 20],
            ['id' => 3, 'name' => 'Early Bird', 'price' => 25000, 'quota' => 30, 'registered' => 30],
        ]);
        $categories = [1 => 'Seminar', 2 => 'Workshop', 3 => 'Expo', 4 => 'Conference'];
    @endphp

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2>{{ $event['title'] }}</h2>
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $categories[$event['category_id']] ?? 'Event' }}</span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><i class="fas fa-calendar-alt"></i> <strong>Tanggal:</strong> {{ date('d F Y', strtotime($event['event_date'])) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Lokasi:</strong> {{ $event['location'] }}</p>
                        </div>
                    </div>
                    <h5>Deskripsi Event</h5>
                    <p class="text-muted">{{ $event['description'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt"></i> Pilih Tiket</h5>
                </div>
                <div class="card-body">
                    @if(session('logged_in'))
                        <form action="{{ route('register.event', $event['id']) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Jenis Tiket</label>
                                <select name="ticket_type_id" class="form-select" required>
                                    <option value="">Pilih tiket...</option>
                                    @foreach($ticketTypes as $ticket)
                                        @php $remaining = $ticket['quota'] - $ticket['registered']; @endphp
                                        <option value="{{ $ticket['id'] }}" {{ $remaining <= 0 ? 'disabled' : '' }}>
                                            {{ $ticket['name'] }} - Rp {{ number_format($ticket['price'], 0, ',', '.') }}
                                            (Sisa: {{ $remaining }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ session('user_name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ session('user_email') }}" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle"></i> Daftar Sekarang
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <p class="text-muted">Silakan login terlebih dahulu untuk mendaftar event ini.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection