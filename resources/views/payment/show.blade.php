@extends('layouts.app')

@section('title', 'Pembayaran Tiket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Konfirmasi Pembayaran</h1>
        
        @if($registration->ticketType->price == 0)
            <div class="text-center py-8">
                <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-5xl text-green-600"></i>
                </div>
                <h2 class="text-xl font-bold mb-2">Tiket GRATIS!</h2>
                <p class="text-gray-600 mb-4">Tiket Anda sudah aktif tanpa perlu pembayaran.</p>
                <a href="{{ route('my.tickets') }}" class="inline-block bg-[#760031] text-white px-6 py-2 rounded-lg">
                    Lihat Tiket Saya
                </a>
            </div>
        @else
            @if($registration->payment_proof)
                <div class="text-center py-8">
                    <div class="bg-blue-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-4xl text-blue-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Menunggu Verifikasi</h2>
                    <p class="text-gray-600 mb-4">Bukti pembayaran sudah diupload. Admin akan segera melakukan verifikasi.</p>
                    <div class="bg-gray-50 rounded-lg p-4 max-w-md mx-auto text-left">
                        <div class="space-y-2">
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
                                <span class="text-xl font-bold text-green-600">Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Menunggu Verifikasi</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('my.tickets') }}" class="inline-block bg-[#760031] text-white px-6 py-2 rounded-lg">
                            Kembali ke Tiket Saya
                        </a>
                    </div>
                </div>
            @else
                @php
                    $isPending = $registration->isPending();
                    $isDeadlinePassed = $registration->isDeadlinePassed();
                    $remainingSeconds = (int) $registration->remaining_seconds;
                    $hasDeadline = $registration->payment_deadline !== null;
                @endphp

                {{-- TIMER --}}
                @if($isPending && !$isDeadlinePassed && $remainingSeconds > 0)
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-300 rounded-lg p-4 mb-6" id="timerContainer">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-yellow-700">
                                <i class="fas fa-hourglass-half mr-2 animate-pulse"></i>BATAS WAKTU PEMBAYARAN
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-yellow-700 font-mono" id="countdownTimer">
                                {{-- Akan diisi JavaScript --}}
                            </p>
                            <p class="text-xs text-yellow-600 mt-1">
                                Deadline: {{ $registration->payment_deadline ? $registration->payment_deadline->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($isDeadlinePassed)
                <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
                    <p class="text-red-700 font-semibold"><i class="fas fa-times-circle mr-2"></i>Batas waktu habis! Silakan daftar ulang.</p>
                </div>
                @endif

                {{-- DETAIL PENDAFTARAN --}}
                <div class="bg-[#760031]/5 rounded-lg p-4 mb-6">
                    <h2 class="font-semibold text-[#760031] text-lg mb-3">Detail Pendaftaran</h2>
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
                            <span class="px-2 py-1 bg-[#760031]/20 text-[#760031] rounded-full text-xs font-medium">{{ $registration->ticketType->name }}</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-2xl font-bold text-[#760031]">Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- REKENING TUJUAN --}}
                <div class="bg-[#760031]/5 rounded-lg p-4 mb-6">
                    <h2 class="font-semibold text-[#760031] text-lg mb-3">Rekening Tujuan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @foreach($bankAccounts as $bank)
                        <div class="bg-white rounded-lg p-4 text-center border-2 hover:border-[#760031] transition cursor-pointer" onclick="selectBank('{{ $bank['bank'] }}')">
                            <i class="fas fa-university text-2xl text-[#760031] mb-2"></i>
                            <p class="font-bold">{{ $bank['bank'] }}</p>
                            <p class="font-mono text-sm">{{ $bank['account_number'] }}</p>
                            <p class="text-xs text-gray-500">a.n. {{ $bank['account_name'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- FORM UPLOAD --}}
                @if($isPending && !$isDeadlinePassed && !$registration->payment_proof)
                <div class="border-t pt-6">
                    <h2 class="font-semibold text-lg mb-4">Upload Bukti Transfer</h2>
                    <form action="{{ route('payment.upload', $registration) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-semibold">Bank Tujuan <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="bankSelect" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
                                <option value="">Pilih Bank Tujuan</option>
                                <option value="BCA">BCA</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="BRI">BRI</option>
                                <option value="BNI">BNI</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-semibold">Nama Pengirim <span class="text-red-500">*</span></label>
                            <input type="text" name="sender_name" value="{{ old('sender_name', auth()->user()->name) }}" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                                   placeholder="Masukkan nama pemilik rekening pengirim" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-semibold">Nomor Rekening Pengirim <span class="text-red-500">*</span></label>
                            <input type="text" name="sender_account" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                                   placeholder="Masukkan nomor rekening pengirim" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-semibold">Bukti Transfer <span class="text-red-500">*</span></label>
                            <input type="file" name="payment_proof" accept="image/*" id="proofInput" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                            <div id="fileSizeAlert" class="hidden mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i> Ukuran file melebihi 2MB. Silakan pilih file yang lebih kecil.
                            </div>
                            @error('payment_proof')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-info-circle mr-1"></i> 
                                Pastikan bukti transfer jelas dan sesuai dengan nominal yang tertera.
                            </p>
                        </div>
                        
                        <button type="submit" class="w-full bg-[#760031] text-white px-6 py-3 rounded-lg hover:bg-[#5a0024] transition font-semibold">
                            <i class="fas fa-upload mr-2"></i>Upload Bukti Transfer
                        </button>
                    </form>
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

<script>
function selectBank(bank) {
    document.getElementById('bankSelect').value = bank;
    document.getElementById('bankSelect').style.borderColor = '#760031';
}

document.getElementById('proofInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const alertDiv = document.getElementById('fileSizeAlert');
    const maxSize = 2 * 1024 * 1024;
    
    if (file) {
        if (file.size > maxSize) {
            alertDiv.classList.remove('hidden');
            this.value = '';
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alertDiv.classList.add('hidden');
        }
    } else {
        alertDiv.classList.add('hidden');
    }
});

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('proofInput');
    const alertDiv = document.getElementById('fileSizeAlert');
    const maxSize = 2 * 1024 * 1024;
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.size > maxSize) {
            e.preventDefault();
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Ukuran file melebihi 2MB! Silakan pilih file yang lebih kecil.');
            return false;
        }
    }
});

// ==========================================
// TIMER COUNTDOWN - FINAL
// ==========================================
@php
    $remainingSeconds = (int) $registration->remaining_seconds;
    $showTimer = !$registration->payment_proof && $registration->isPending() && !$registration->isDeadlinePassed() && $remainingSeconds > 0;
@endphp

@if($showTimer)
<script>
    (function() {
        console.log('🚀 Timer dimulai!');
        console.log('⏱️ Remaining seconds:', {{ $remainingSeconds }});
        
        let remainingSeconds = {{ $remainingSeconds }};
        const timerElement = document.getElementById('countdownTimer');
        
        if (!timerElement) {
            console.error('❌ Element countdownTimer tidak ditemukan!');
            return;
        }
        
        // Format waktu ke HH:MM:SS
        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);
            return String(hours).padStart(2, '0') + ':' + 
                   String(minutes).padStart(2, '0') + ':' + 
                   String(secs).padStart(2, '0');
        }
        
        // Tampilkan waktu awal
        timerElement.textContent = formatTime(remainingSeconds);
        console.log('🕐 Waktu awal:', formatTime(remainingSeconds));
        
        // Jalankan timer setiap detik
        const interval = setInterval(function() {
            remainingSeconds--;
            
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                timerElement.textContent = '⏰ Kadaluarsa';
                timerElement.classList.add('text-red-600');
                console.log('⏰ Timer selesai!');
                
                // Reload halaman setelah 3 detik
                setTimeout(function() {
                    location.reload();
                }, 3000);
            } else {
                timerElement.textContent = formatTime(remainingSeconds);
            }
        }, 1000);
        
        console.log('✅ Timer berjalan!');
    })();
</script>
@else
<script>
    console.log('❌ Timer TIDAK dijalankan karena:');
    console.log('  - payment_proof:', {{ $registration->payment_proof ? 'true' : 'false' }});
    console.log('  - isPending:', {{ $registration->isPending() ? 'true' : 'false' }});
    console.log('  - isDeadlinePassed:', {{ $registration->isDeadlinePassed() ? 'true' : 'false' }});
    console.log('  - remainingSeconds:', {{ $remainingSeconds }});
    console.log('  - showTimer:', {{ $showTimer ? 'true' : 'false' }});
</script>
@endif
</script>
@endsection