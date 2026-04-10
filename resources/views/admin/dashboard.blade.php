@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2><i class="fas fa-chart-line"></i> Welcome back, {{ session('user_name') }}!</h2>
                    <p class="mb-0">Selamat datang di dashboard admin event management system.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Events</h6>
                            <h2 class="mb-0">{{ session('total_events', 3) }}</h2>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Peserta</h6>
                            <h2 class="mb-0">{{ session('total_participants', 0) }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Pendapatan</h6>
                            <h2 class="mb-0">Rp {{ number_format(session('total_revenue', 0), 0, ',', '.') }}</h2>
                        </div>
                        <i class="fas fa-money-bill fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Upcoming Events</h6>
                            <h2 class="mb-0">2</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Menu Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-plus"></i> Kelola Event
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-tags"></i> Kelola Kategori
                        </a>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus-circle"></i> Buat Event Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Pendaftaran Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Event</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $registrations = session('registrations', []);
                                    $recentRegs = array_slice(array_reverse($registrations), 0, 5);
                                @endphp
                                @forelse($recentRegs as $reg)
                                <tr>
                                    <td>{{ $reg['user_name'] ?? '-' }}</td>
                                    <td>{{ $reg['event_title'] ?? 'Event' }}</td>
                                    <td>
                                        <span class="badge bg-{{ ($reg['payment_status'] ?? 'pending') == 'paid' ? 'success' : 'warning' }}">
                                            {{ $reg['payment_status'] ?? 'pending' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada pendaftaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection