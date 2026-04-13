@extends('layouts.app')

@section('title', 'Pembayaran Tiket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Konfirmasi Pembayaran</h1>
        
        <!-- Informasi Registrasi -->
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
                    <p class="font-semibold">{{ $registration->ticketType->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Status Pembayaran -->
        <div class="mb-6">
            @if($registration->isPaid())
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle"></i> Pembayaran sudah dikonfirmasi. Terima kasih!
                </div>
            @elseif($registration->payment_status === 'pending')
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    <i class="fas fa-clock"></i> Menunggu konfirmasi pembayaran.
                </div>
            @elseif($registration->payment_status === 'failed')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-times-circle"></i> Pembayaran ditolak. Silakan hubungi admin.
                </div>
            @endif
        </div>
        
        <!-- Informasi Bank -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg mb-3">Rekening Tujuan Transfer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($bankAccounts as $bank)
                <div class="bg-white rounded-lg p-3 text-center">
                    <i class="fas fa-university text-2xl text-blue-600 mb-2"></i>
                    <p class="font-bold">{{ $bank['bank'] }}</p>
                    <p class="font-mono text-sm">{{ $bank['account_number'] }}</p>
                    <p class="text-xs text-gray-500">a.n. {{ $bank['account_name'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Form Upload Bukti Transfer -->
        @if(!$registration->isPaid() && $registration->payment_status !== 'failed')
        <div class="border-t pt-6">
            <h2 class="font-semibold text-lg mb-4">Upload Bukti Transfer</h2>
            
            <form action="{{ route('payment.upload', $registration) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                        <option value="">Pilih Bank Tujuan</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                        <option value="Other">Lainnya</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Bukti Transfer</label>
                    <input type="file" name="payment_proof" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                </div>
                
                <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle"></i> 
                        Pastikan bukti transfer jelas dan sesuai dengan nominal yang tertera.
                    </p>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-upload"></i> Upload Bukti Transfer
                </button>
            </form>
        </div>
        @endif
        
        <div class="mt-6">
            <a href="{{ route('my.tickets') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Tiket Saya
            </a>
        </div>
    </div>
</div>
@endsection