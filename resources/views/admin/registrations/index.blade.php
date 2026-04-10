@extends('layouts.app')

@section('title', 'Daftar Peserta')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-users"></i> Daftar Peserta: {{ $event['title'] }}</h4>
            <a href="{{ route('admin.events.registrations.export', $event['id']) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
        <div class="card-body">
            @php
                $registrations = session('registrations', []);
                $eventRegistrations = array_filter($registrations, function($reg) use ($event) {
                    return isset($reg['event_id']) && $reg['event_id'] == $event['id'];
                });
            @endphp
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Registrasi</th>
                            <th>Nama Peserta</th>
                            <th>Email</th>
                            <th>Jenis Tiket</th>
                            <th>Status</th>
                            <th>Tgl Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventRegistrations as $index => $reg)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $reg['registration_number'] }}</strong></td>
                            <td>{{ $reg['user_name'] }}</td>
                            <td>{{ $reg['user_email'] }}</td>
                            <td>{{ $reg['ticket_type_name'] ?? 'Regular' }}</td>
                            <td>
                                <span class="badge bg-{{ ($reg['payment_status'] ?? 'pending') == 'paid' ? 'success' : 'warning' }}">
                                    {{ $reg['payment_status'] ?? 'pending' }}
                                </span>
                            </td>
                            <td>{{ isset($reg['registered_at']) ? date('d/m/Y H:i', strtotime($reg['registered_at'])) : date('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada peserta terdaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection