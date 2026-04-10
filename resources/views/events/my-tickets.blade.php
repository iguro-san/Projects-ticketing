@extends('layouts.app')

@section('title', 'Tiket Saya')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fas fa-ticket-alt"></i> Tiket Saya</h4>
        </div>
        <div class="card-body">
            @php
                $registrations = session('registrations', []);
                $myRegistrations = array_filter($registrations, function($reg) {
                    return isset($reg['user_email']) && $reg['user_email'] == session('user_email');
                });
            @endphp

            @if(empty($myRegistrations))
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                    <h5>Belum ada tiket</h5>
                    <p class="text-muted">Anda belum mendaftar event apapun.</p>
                    <a href="{{ route('events.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Lihat Event
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($myRegistrations as $reg)
                    <div class="col-md-6 mb-4">
                        <div class="card border-{{ ($reg['payment_status'] ?? 'pending') == 'paid' ? 'success' : 'warning' }} shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title">{{ $reg['event_title'] ?? 'Event' }}</h5>
                                        <p class="text-muted mb-1">
                                            <small><i class="fas fa-calendar"></i> {{ $reg['event_date'] ?? '-' }}</small>
                                        </p>
                                        <p class="text-muted mb-1">
                                            <small><i class="fas fa-ticket"></i> {{ $reg['ticket_type_name'] ?? 'Regular' }}</small>
                                        </p>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ ($reg['payment_status'] ?? 'pending') == 'paid' ? 'success' : 'warning' }} fs-6">
                                            {{ ($reg['payment_status'] ?? 'pending') == 'paid' ? 'Paid' : 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-1">
                                            <strong>No. Registrasi:</strong><br>
                                            <code class="fs-6">{{ $reg['registration_number'] }}</code>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('{{ $reg['registration_number'] }}')">
                                        <i class="fas fa-copy"></i> Copy No Registrasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    alert('Nomor registrasi telah disalin!');
}
</script>
@endsection