@extends('layouts.app')

@section('title', 'Tiket Saya')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6">
    <i class="fas fa-ticket-alt text-purple-600"></i> Tiket Saya
</h1>

@forelse($registrations as $reg)
<div class="bg-white rounded-lg shadow p-6 mb-4 hover:shadow-lg transition">
    <div class="flex flex-col md:flex-row justify-between items-start">
        <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-800">{{ $reg->event->title }}</h3>
            <p class="text-gray-600">{{ $reg->event->event_date->format('d F Y') }}</p>
            <p class="text-gray-600">{{ $reg->event->location }}</p>
            
            <div class="mt-3 space-x-2">
                <span class="inline-block bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">
                    {{ $reg->ticketType->name }}
                </span>
                
                @if($reg->payment_status == 'paid')
                    <span class="inline-block bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-1"></i> Lunas
                    </span>
                @elseif($reg->payment_status == 'pending' && !$reg->isDeadlinePassed())
                    <span class="inline-block bg-yellow-200 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-clock mr-1"></i> Menunggu Pembayaran
                    </span>
                @elseif($reg->payment_status == 'pending' && $reg->isDeadlinePassed())
                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-hourglass-end mr-1"></i> Kadaluarsa
                    </span>
                @elseif($reg->payment_status == 'cancelled')
                    <span class="inline-block bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-ban mr-1"></i> Dibatalkan
                    </span>
                @else
                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ ucfirst($reg->payment_status) }}
                    </span>
                @endif
            </div>

            {{-- Info Sisa Waktu --}}
            @if($reg->payment_status == 'pending' && $reg->payment_deadline)
                <div class="mt-2">
                    @if($reg->isDeadlinePassed())
                        <p class="text-xs text-red-600 font-semibold">
                            <i class="fas fa-times-circle mr-1"></i> 
                            Pendaftaran kadaluarsa — Silakan daftar ulang
                        </p>
                    @else
                        <p class="text-xs text-yellow-600 font-semibold">
                            <i class="fas fa-hourglass-half mr-1 animate-pulse"></i> 
                            Sisa waktu: {{ $reg->remaining_time }}
                        </p>
                    @endif
                </div>
            @endif

            <p class="text-sm text-gray-500 mt-2">
                Total: 
                <span class="font-bold">
                    @if($reg->ticketType->price == 0)
                        <span class="text-green-600">GRATIS</span>
                    @else
                        @if($reg->isPaid())
                            <span class="text-green-600">Rp {{ number_format($reg->amount_paid, 0, ',', '.') }}</span>
                        @else
                            <span class="text-gray-600">Rp {{ number_format($reg->ticketType->price, 0, ',', '.') }}</span>
                        @endif
                    @endif
                </span>
            </p>
        </div>

        <div class="mt-4 md:mt-0 text-left md:text-right">
            <div class="bg-gray-100 rounded-lg p-3 mb-2">
                <p class="text-xs text-gray-500">No. Registrasi</p>
                <p class="font-mono font-bold text-purple-600">{{ $reg->registration_number }}</p>
            </div>

            {{-- Tombol Bayar --}}
            @if($reg->payment_status == 'pending' && !$reg->isDeadlinePassed())
                <a href="{{ route('payment.show', $reg) }}" 
                   class="inline-block bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition font-semibold">
                    <i class="fas fa-money-bill-wave mr-1"></i> Bayar Sekarang
                </a>
            @elseif($reg->payment_status == 'pending' && $reg->isDeadlinePassed())
                <span class="inline-block bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-times-circle mr-1"></i> Kadaluarsa
                </span>
            @elseif($reg->payment_status == 'paid')
                <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-check-circle mr-1"></i> Lunas
                </span>
            @elseif($reg->payment_status == 'cancelled')
                <span class="inline-block bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-ban mr-1"></i> Dibatalkan
                </span>
            @endif

            <p class="text-xs text-gray-500 mt-2">
                Daftar: {{ $reg->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</div>
@empty
<div class="bg-white rounded-lg shadow p-12 text-center">
    <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600">Belum ada tiket</h3>
    <p class="text-gray-500 mb-6">Anda belum mendaftar event apapun</p>
    <a href="{{ route('home') }}" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-calendar-alt mr-2"></i> Lihat Event
    </a>
</div>
@endforelse
@endsection