@extends('layouts.app')

@section('title', 'Daftar Peserta')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Peserta: {{ $event->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-calendar-alt mr-1"></i> {{ $event->event_date->format('d F Y') }}
                <span class="mx-2">|</span>
                <i class="fas fa-map-marker-alt mr-1"></i> {{ $event->location }}
            </p>
        </div>
        <a href="{{ route('panitia.events.registrations.export', $event) }}" 
           class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-file-excel mr-1"></i> Export
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No Registrasi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tiket</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Bukti</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registrations as $reg)
                <tr>
                    <td class="px-4 py-3 font-mono text-sm">{{ $reg->registration_number }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $reg->user_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $reg->user_email }}</td>
                    <td class="px-4 py-3">{{ $reg->ticketType->name }}</td>
                    <td class="px-4 py-3">
                        @if($reg->payment_proof)
                            <a href="{{ Storage::url($reg->payment_proof) }}" target="_blank" 
                               class="text-blue-500 hover:underline text-sm">
                                <i class="fas fa-image"></i> Lihat
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                            @elseif($reg->payment_status == 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($reg->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($reg->payment_proof && $reg->payment_status == 'pending')
                            <div class="flex gap-1 justify-center">
                                <form action="{{ route('panitia.events.registrations.confirm', [$event, $reg]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="confirm">
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 transition"
                                            onclick="return confirm('Konfirmasi pembayaran ini?')">
                                        <i class="fas fa-check mr-1"></i> Konfirmasi
                                    </button>
                                </form>
                                <form action="{{ route('panitia.events.registrations.confirm', [$event, $reg]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition"
                                            onclick="return confirm('Tolak pembayaran ini?')">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        @elseif($reg->payment_status == 'paid')
                            <span class="text-green-600 text-xs">
                                <i class="fas fa-check-circle"></i> Terkonfirmasi
                            </span>
                        @elseif($reg->payment_status == 'failed')
                            <span class="text-red-600 text-xs">
                                <i class="fas fa-times-circle"></i> Ditolak
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">Menunggu bukti bayar</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada peserta terdaftar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $registrations->links() }}
    </div>
    
    <div class="mt-4">
        <a href="{{ route('panitia.events.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Event
        </a>
    </div>
</div>
@endsection