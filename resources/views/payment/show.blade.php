@extends('layouts.app')

@section('title', 'Pembayaran Tiket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>Konfirmasi Pembayaran
        </h1>

        {{-- COUNTDOWN TIMER --}}
        @if($registration->isPending() && !$registration->isDeadlinePassed())
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-300 rounded-lg p-4 mb-6" 
             x-data="countdownTimer({{ $registration->remaining_seconds }})">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-yellow-700">
                        <i class="fas fa-hourglass-half mr-2 animate-pulse"></i>
                        BATAS WAKTU PEMBAYARAN
                    </p>
                    <p class="text-xs text-yellow-600 mt-1">Segera transfer sebelum waktu habis!</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-700" x-text="timeDisplay"></p>
                    <p class="text-xs text-yellow-600 mt-1">
                        Deadline: {{ $registration->payment_deadline->format('d M Y, H:i') }} WIB
                    </p>
                </div>
            </div>
            {{-- Progress Bar --}}
            <div class="mt-3 bg-yellow-200 rounded-full h-2">
                <div class="bg-yellow-500 rounded-full h-2 transition-all duration-1000" 
                     :style="'width: ' + progressPercent + '%'"></div>
            </div>
        </div>
        @elseif($registration->isPending() && $registration->isDeadlinePassed())
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="text-red-700 font-semibold">
                <i class="fas fa-times-circle mr-2"></i>
                Batas waktu pembayaran telah habis! Silakan daftar ulang.
            </p>
        </div>
        @endif

        {{-- Detail Pendaftaran --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg mb-3">Detail Pendaftaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">No. Registrasi</p>
                    <p class="font-mono font-bold text-lg">{{ $registration->registration_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Event</p>
                    <p class="font-semibold">{{ $registration->event->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Tiket</p>
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                        {{ $registration->ticketType->name }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-green-600">
                        Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Rekening Bank --}}
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg mb-3">Rekening Tujuan Transfer</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($bankAccounts as $bank)
                <div class="bg-white rounded-lg p-4 text-center border hover:shadow-md transition">
                    <i class="fas fa-university text-3xl text-blue-600 mb-2"></i>
                    <p class="font-bold text-lg">{{ $bank['bank'] }}</p>
                    <p class="font-mono text-sm bg-gray-100 rounded px-2 py-1 mt-1">
                        {{ $bank['account_number'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">a.n. {{ $bank['account_name'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Form Upload --}}
        @if($registration->isPending() && !$registration->isDeadlinePassed())
        <div class="border-t pt-6">
            <h2 class="font-semibold text-lg mb-4">Upload Bukti Transfer</h2>

            <form action="{{ route('payment.upload', $registration) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 font-semibold">Metode Pembayaran</label>
                    <select name="payment_method" 
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                        <option value="">Pilih Bank Tujuan</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                        <option value="Other">Lainnya</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 font-semibold">Bukti Transfer</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-500 transition">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-500 mb-2">Seret & lepas file di sini atau klik</p>
                        <input type="file" name="payment_proof" accept="image/*" 
                               class="w-full text-sm" required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB). Pastikan bukti transfer jelas terbaca.</p>
                </div>

                <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pastikan nominal transfer sesuai dengan total pembayaran. Verifikasi oleh panitia maksimal 1x24 jam.
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg">
                    <i class="fas fa-upload mr-2"></i> Upload Bukti Transfer
                </button>
            </form>
        </div>
        @elseif($registration->isPaid())
        <div class="bg-green-50 border border-green-300 rounded-lg p-4 text-center">
            <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
            <p class="text-green-700 font-semibold text-lg">Pembayaran Terkonfirmasi!</p>
            <p class="text-green-600 text-sm">Terima kasih, tiket Anda sudah aktif.</p>
        </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('my.tickets') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Tiket Saya
            </a>
        </div>
    </div>
</div>

{{-- Countdown Script --}}
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('countdownTimer', (remainingSeconds) => ({
            remaining: remainingSeconds,
            total: remainingSeconds,
            init() {
                if (this.remaining > 0) {
                    this.startCountdown();
                }
            },
            get timeDisplay() {
                const hours = Math.floor(this.remaining / 3600);
                const minutes = Math.floor((this.remaining % 3600) / 60);
                const seconds = this.remaining % 60;
                
                if (this.remaining <= 0) {
                    return 'Kadaluarsa';
                }
                
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },
            get progressPercent() {
                if (this.total === 0) return 0;
                return Math.max(0, Math.min(100, (this.remaining / this.total) * 100));
            },
            startCountdown() {
                const interval = setInterval(() => {
                    if (this.remaining > 0) {
                        this.remaining--;
                    } else {
                        clearInterval(interval);
                        // Auto-reload setelah kadaluarsa
                        setTimeout(() => location.reload(), 3000);
                    }
                }, 1000);
            }
        }));
    });
</script>
@endpush
@endsection