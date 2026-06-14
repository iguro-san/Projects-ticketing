@extends('layouts.app')

@section('title', $registration->ticketType->price == 0 ? 'Tiket Gratis' : 'Pembayaran Tiket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        
        {{-- TIKET GRATIS --}}
        @if($registration->ticketType->price == 0)
        <div class="text-center py-8">
            <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-gift text-5xl text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Tiket GRATIS!</h1>
            <p class="text-gray-600 mb-6">Tiket Anda sudah otomatis aktif tanpa perlu pembayaran.</p>
            
            <div class="bg-[#760031]/10 rounded-lg p-6 max-w-md mx-auto text-left mb-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. Registrasi</span>
                        <span class="font-mono font-bold">{{ $registration->registration_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Event</span>
                        <span class="font-semibold">{{ $registration->event->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tiket</span>
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $registration->ticketType->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total</span>
                        <span class="text-green-600 font-bold text-lg">GRATIS</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="text-green-600 font-semibold"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('my.tickets') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                <i class="fas fa-ticket-alt mr-2"></i>Lihat Tiket Saya
            </a>
        </div>
        
        {{-- TIKET BERBAYAR --}}
        @else

        @if($registration->payment_proof)
        <div class="text-center py-8">
            <div class="bg-blue-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock text-5xl text-blue-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Menunggu Verifikasi</h1>
            <p class="text-gray-600 mb-6">Bukti pembayaran Anda sudah diupload. Admin akan segera melakukan verifikasi.</p>
            
            <div class="bg-gray-50 rounded-lg p-6 max-w-md mx-auto text-left mb-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. Registrasi</span>
                        <span class="font-mono font-bold">{{ $registration->registration_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Event</span>
                        <span class="font-semibold">{{ $registration->event->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jumlah</span>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('my.tickets') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                <i class="fas fa-ticket-alt mr-2"></i>Lihat Tiket Saya
            </a>
        </div>
        @else
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>Konfirmasi Pembayaran
        </h1>

        {{-- Countdown Timer --}}
        @if($registration->isPending() && !$registration->isDeadlinePassed())
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-300 rounded-lg p-4 mb-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-yellow-700">
                        <i class="fas fa-hourglass-half mr-2 animate-pulse"></i>BATAS WAKTU PEMBAYARAN
                    </p>
                    <p class="text-xs text-yellow-600 mt-1">Segera transfer sebelum waktu habis!</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-700" id="countdown"></p>
                    <p class="text-xs text-yellow-600 mt-1">Deadline: {{ $registration->payment_deadline->format('d M Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>
        @elseif($registration->isDeadlinePassed())
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="text-red-700 font-semibold"><i class="fas fa-times-circle mr-2"></i>Batas waktu habis! Silakan daftar ulang.</p>
        </div>
        @endif

        {{-- Detail Pendaftaran --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg mb-3">Detail Pendaftaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">No. Registrasi</p>
                    <p class="font-mono font-bold">{{ $registration->registration_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Event</p>
                    <p class="font-semibold">{{ $registration->event->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Tiket</p>
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">{{ $registration->ticketType->name }}</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Rekening Tujuan --}}
        <div class="bg-red-50 rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg mb-3">Rekening Tujuan</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($bankAccounts as $bank)
                <div class="bg-white rounded-lg p-4 text-center border">
                    <i class="fas fa-university text-2xl text-blue-600 mb-2"></i>
                    <p class="font-bold">{{ $bank['bank'] }}</p>
                    <p class="font-mono text-sm">{{ $bank['account_number'] }}</p>
                    <p class="text-xs text-gray-500">a.n. {{ $bank['account_name'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Form Upload Bukti Transfer --}}
        @if($registration->isPending() && !$registration->isDeadlinePassed() && !$registration->payment_proof)
        <div class="border-t pt-6">
            <h2 class="font-semibold text-lg mb-4">Upload Bukti Transfer</h2>
            <form action="{{ route('payment.upload', $registration) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 font-semibold">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                        <option value="Other">Lainnya</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 font-semibold">Bukti Transfer</label>
                    <input type="file" name="payment_proof" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                </div>
                <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Pastikan bukti transfer jelas dan sesuai dengan nominal yang tertera.
                    </p>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-upload mr-2"></i>Upload Bukti Transfer
                </button>
            </form>
        </div>
        @elseif($registration->isPaid())
        <div class="bg-green-50 border border-green-300 rounded-lg p-4 text-center">
            <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
            <p class="text-green-700 font-semibold text-lg">Pembayaran Terkonfirmasi!</p>
        </div>
        @endif
        @endif
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('my.tickets') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Tiket Saya
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Countdown Timer
    let remainingSeconds = {{ $registration->remaining_seconds ?? 0 }};
    if (remainingSeconds > 0 && !{{ $registration->payment_proof ? 'true' : 'false' }}) {
        const countdownEl = document.getElementById('countdown');
        if (countdownEl) {
            const timer = setInterval(() => {
                if (remainingSeconds <= 0) {
                    clearInterval(timer);
                    countdownEl.innerText = 'Kadaluarsa';
                    setTimeout(() => location.reload(), 3000);
                } else {
                    const hours = Math.floor(remainingSeconds / 3600);
                    const minutes = Math.floor((remainingSeconds % 3600) / 60);
                    const seconds = remainingSeconds % 60;
                    countdownEl.innerText = `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
                    remainingSeconds--;
                }
            }, 1000);
        }
    }
</script>
@endpush
@endsection