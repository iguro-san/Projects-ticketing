@extends('layouts.app')

@section('title', 'Tiket Saya')

@section('content')
<h1 class="text-3xl font-bold text-[#141E46] mb-6">
    <i class="fas fa-ticket-alt text-[#B6771D]"></i> Tiket Saya
</h1>

@forelse($registrations as $reg)
<div class="bg-white rounded-lg shadow p-6 mb-4 hover:shadow-lg transition">
    <div class="flex flex-col md:flex-row justify-between items-start">
        <div class="flex-1">
            <h3 class="text-xl font-bold text-[#141E46]">{{ $reg->event->title }}</h3>
            <p class="text-gray-600">{{ $reg->event->event_date->translatedFormat('d F Y') }}</p>
            <p class="text-gray-600">{{ $reg->event->location }}</p>
            
            <div class="mt-3 space-x-2 flex flex-wrap gap-2">
                <span class="inline-block bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">
                    {{ $reg->ticketType->name }}
                </span>
                
                @if($reg->payment_status == 'paid')
                    <span class="inline-block bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-1"></i> Lunas
                    </span>
                @elseif($reg->payment_status == 'pending' && $reg->payment_proof)
                    <span class="inline-block bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-clock mr-1"></i> Menunggu Konfirmasi Admin
                    </span>
                @elseif($reg->payment_status == 'pending' && !$reg->isDeadlinePassed() && !$reg->payment_proof)
                    <span class="inline-block bg-yellow-200 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-clock mr-1"></i> Menunggu Pembayaran
                    </span>
                @elseif($reg->payment_status == 'pending' && $reg->isDeadlinePassed())
                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-hourglass-end mr-1"></i> Kadaluarsa
                    </span>
                @elseif($reg->payment_status == 'failed')
                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-times-circle mr-1"></i> Pembayaran Ditolak
                    </span>
                @else
                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                        <i class="fas fa-ban mr-1"></i> Dibatalkan
                    </span>
                @endif
            </div>

            @if($reg->payment_status == 'pending' && !$reg->payment_proof && $reg->payment_deadline)
                <div class="mt-2">
                    @if($reg->isDeadlinePassed())
                        <p class="text-xs text-red-600 font-semibold">
                            <i class="fas fa-times-circle mr-1"></i> 
                            Pendaftaran kadaluarsa
                        </p>
                    @else
                        <p class="text-xs text-yellow-600 font-semibold" id="timer-{{ $reg->id }}">
                            <i class="fas fa-hourglass-half mr-1 animate-pulse"></i> 
                            Sisa waktu: {{ $reg->remaining_time }}
                        </p>
                    @endif
                </div>
            @elseif($reg->payment_status == 'pending' && $reg->payment_proof)
                <div class="mt-2">
                    <p class="text-xs text-blue-600 font-semibold">
                        <i class="fas fa-check-circle mr-1"></i> 
                        Bukti pembayaran sudah diupload, menunggu verifikasi admin.
                    </p>
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
                <p class="font-mono font-bold text-[#B6771D]">{{ $reg->registration_number }}</p>
            </div>

            @if($reg->payment_status == 'pending' && !$reg->isDeadlinePassed() && !$reg->payment_proof)
                <a href="{{ route('payment.show', $reg) }}" 
                   class="inline-block bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition font-semibold">
                    <i class="fas fa-money-bill-wave mr-1"></i> Bayar Sekarang
                </a>
            @elseif($reg->payment_status == 'pending' && $reg->payment_proof)
                <span class="inline-block bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold">
                    <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                </span>
            @endif

            <p class="text-xs text-gray-500 mt-2">
                Daftar: {{ $reg->created_at->translatedFormat('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</div>

@if($reg->payment_status == 'pending' && !$reg->payment_proof && $reg->payment_deadline && !$reg->isDeadlinePassed())
<script>
(function() {
    // Pastikan remaining adalah integer
    let remaining = {{ (int) $reg->remaining_seconds }};
    const timerEl = document.getElementById('timer-{{ $reg->id }}');
    if (timerEl && remaining > 0) {
        // Pastikan integer
        remaining = Math.floor(remaining);
        
        const interval = setInterval(() => {
            if (remaining <= 0) {
                clearInterval(interval);
                timerEl.innerHTML = '<i class="fas fa-hourglass-end mr-1"></i> Kadaluarsa';
                location.reload();
            } else {
                const hours = Math.floor(remaining / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = Math.floor(remaining % 60);
                timerEl.innerHTML = `<i class="fas fa-hourglass-half mr-1 animate-pulse"></i> Sisa waktu: ${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
                remaining--;
            }
        }, 1000);
    }
})();
</script>
@endif

@empty
<div class="bg-white rounded-lg shadow p-12 text-center">
    <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600">Belum ada tiket</h3>
    <p class="text-gray-500 mb-6">Anda belum mendaftar event apapun</p>
    <a href="{{ route('home') }}" class="inline-block bg-[#141E46] text-white px-6 py-2 rounded-lg hover:bg-[#141E46]/80 transition">
        <i class="fas fa-calendar-alt mr-2"></i> Lihat Event
    </a>
</div>
@endforelse
@endsection