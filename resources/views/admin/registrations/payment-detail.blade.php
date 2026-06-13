@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Pembayaran</h1>
    
    <div class="mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">No Registrasi</p>
                    <p class="font-mono font-bold">{{ $registration->registration_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Daftar</p>
                    <p class="font-semibold">{{ $registration->registered_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nama Peserta</p>
                    <p class="font-semibold">{{ $registration->user_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold">{{ $registration->user_email }}</p>
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
                    <p class="text-sm text-gray-500">Jumlah Bayar</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($registration->ticketType->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-2 py-1 rounded text-sm
                        @if($registration->payment_status == 'paid') bg-green-100 text-green-700
                        @elseif($registration->payment_status == 'pending') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($registration->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    @if($registration->payment_proof)
    <div class="mb-6">
        <h2 class="font-semibold text-lg mb-3">Bukti Transfer</h2>
        <div class="border rounded-lg p-4 text-center">
            <img src="{{ Storage::url($registration->payment_proof) }}" alt="Bukti Transfer" class="max-w-full max-h-96 mx-auto rounded-lg">
        </div>
        <p class="text-sm text-gray-500 mt-2">Metode: {{ $registration->payment_method ?? '-' }}</p>
        @if($registration->paid_at)
            <p class="text-sm text-gray-500">Dikonfirmasi: {{ $registration->paid_at->format('d/m/Y H:i') }}</p>
        @endif
    </div>
    @endif
    
    <div class="border-t pt-6">
        <h2 class="font-semibold text-lg mb-3">Update Status</h2>
        <form action="{{ route('admin.events.registrations.update-payment', [$event, $registration]) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Status Pembayaran</label>
                <select name="payment_status" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                    <option value="pending" {{ $registration->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $registration->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ $registration->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Catatan Admin</label>
                <textarea name="admin_notes" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">{{ $registration->admin_notes }}</textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Update Status
                </button>
                <a href="{{ route('admin.events.registrations.index', $event) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection