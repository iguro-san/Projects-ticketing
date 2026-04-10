@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-home"></i> Selamat Datang</h4>
                </div>
                <div class="card-body text-center">
                    @if(session('logged_in'))
                        <h3>Halo, {{ session('user_name') }}!</h3>
                        <p class="text-muted">Selamat datang di Event Management System.</p>
                        <div class="mt-4">
                            <a href="{{ route('events.index') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-alt"></i> Lihat Event
                            </a>
                            @if(session('user_role') == 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-success">
                                    <i class="fas fa-chart-line"></i> Dashboard Admin
                                </a>
                            @endif
                        </div>
                    @else
                        <h3>Event Management System</h3>
                        <p class="text-muted">Sistem manajemen event dan tiket online</p>
                        <div class="mt-4">
                            <a href="{{ route('events.index') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-alt"></i> Lihat Event
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-secondary">
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