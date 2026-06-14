@extends('layouts.app')

@section('title', 'Daftar Peserta')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-[#141E46]">Peserta: {{ $event->title }}</h1>
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registrations as $reg)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-sm">{{ $reg->registration_number }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $reg->user_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $reg->user_email }}</td>
                    <td class="px-4 py-3">{{ $reg->ticketType->name }}</td>
                    <td class="px-4 py-3">
                        @if($reg->payment_proof)
                            <a href="{{ Storage::url($reg->payment_proof) }}" target="_blank" 
                               class="text-blue-500 hover:underline text-sm">
                                <i class="fas fa-image mr-1"></i> Lihat
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                            @elseif($reg->payment_status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($reg->payment_status == 'cancelled') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($reg->payment_status) }}
                        </span>
                        @if($reg->payment_status == 'pending' && $reg->payment_proof)
                            <p class="text-xs text-blue-500 mt-1">Menunggu verifikasi admin</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada peserta terdaftar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $registrations->links() }}
    </div>
    
    <div class="mt-4">
        <a href="{{ route('panitia.events.index') }}" class="text-[#B6771D] hover:text-[#B6771D]/80">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Event
        </a>
    </div>
</div>
@endsection