@extends('layouts.app')

@section('title', 'Kelola Event')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-calendar-alt"></i> Daftar Event</h4>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Event Baru
            </a>
        </div>
        <div class="card-body">
            @php
                $events = session('events', [
                    ['id' => 1, 'title' => 'Seminar AI 2026', 'category_id' => 1, 'event_date' => '2026-03-20', 'location' => 'Jakarta Convention Center', 'description' => 'Belajar AI'],
                    ['id' => 2, 'title' => 'Workshop Laravel', 'category_id' => 2, 'event_date' => '2026-03-25', 'location' => 'Bandung Digital Valley', 'description' => 'Praktik Laravel'],
                    ['id' => 3, 'title' => 'Informatics Fair', 'category_id' => 3, 'event_date' => '2026-04-01', 'location' => 'Surabaya Convention Hall', 'description' => 'Pameran teknologi'],
                ]);
                $categories = [1 => 'Seminar', 2 => 'Workshop', 3 => 'Expo', 4 => 'Conference'];
            @endphp

            @if(empty($events))
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada event. Silakan buat event baru.</p>
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Buat Event</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Judul Event</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $event['id'] }}</td>
                                <td><strong>{{ $event['title'] }}</strong></td>
                                <td>{{ $categories[$event['category_id']] ?? 'General' }}</td>
                                <td>{{ date('d M Y', strtotime($event['event_date'])) }}</td>
                                <td>{{ $event['location'] }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.events.ticket-types.index', $event['id']) }}" class="btn btn-sm btn-info" title="Kelola Tiket">
                                            <i class="fas fa-ticket-alt"></i>
                                        </a>
                                        <a href="{{ route('admin.events.registrations.index', $event['id']) }}" class="btn btn-sm btn-success" title="Lihat Peserta">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event['id']) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus event ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection