@extends('layouts.app')

@section('title', isset($event) ? 'Daftar Peserta: ' . $event->title : 'Daftar Registrasi')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(isset($event))
                Daftar Peserta: {{ $event->title }}
            @else
                Daftar Registrasi
            @endif
        </h1>
        
        @if(isset($event))
            <a href="{{ route('admin.events.registrations.export', $event) }}" 
               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-file-excel mr-1"></i> Export
            </a>
        @else
            <a href="{{ route('admin.registrations.export') }}" 
               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-file-excel mr-1"></i> Export
            </a>
        @endif
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No Registrasi</th>
                    @if(!isset($event))
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Event</th>
                    @endif
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tiket</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Harga</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Bukti Bayar</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registrations as $reg)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono text-xs">{{ $reg->registration_number }}</td>
                    
                    @if(!isset($event))
                        <td class="px-4 py-3 text-sm">
                            <span class="font-medium">{{ $reg->event->title ?? '-' }}</span>
                            <br>
                            <span class="text-xs text-gray-400">{{ $reg->event->event_date->format('d/m/Y') ?? '' }}</span>
                        </td>
                    @endif
                    
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <div class="bg-purple-100 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-purple-600 text-xs"></i>
                            </div>
                            <span class="font-semibold text-sm">{{ $reg->user_name }}</span>
                        </div>
                    </td>
                    
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $reg->user_email }}</td>
                    
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                            {{ $reg->ticketType->name ?? '-' }}
                        </span>
                    </td>
                    
                    <td class="px-4 py-3 text-sm font-medium">
                        @if($reg->amount_paid)
                            Rp {{ number_format($reg->amount_paid, 0, ',', '.') }}
                        @else
                            Rp {{ number_format($reg->ticketType->price ?? 0, 0, ',', '.') }}
                        @endif
                    </td>
                    
                    <td class="px-4 py-3">
                        @if($reg->payment_proof)
                            <a href="{{ Storage::url($reg->payment_proof) }}" target="_blank" 
                               class="inline-flex items-center text-blue-500 hover:text-blue-700 text-sm">
                                <i class="fas fa-image mr-1"></i> Lihat
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    
                    <td class="px-4 py-3">
                        @if($reg->payment_status == 'paid')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle mr-1"></i> Lunas
                            </span>
                            @if($reg->paid_at)
                                <p class="text-xs text-gray-400 mt-1">{{ $reg->paid_at->format('d/m/Y') }}</p>
                            @endif
                        @elseif($reg->payment_status == 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @elseif($reg->payment_status == 'failed')
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-times-circle mr-1"></i> Gagal
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                                {{ ucfirst($reg->payment_status) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ isset($event) ? '8' : '9' }}" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">Belum ada peserta terdaftar</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Footer Info --}}
    <div class="mt-6 flex flex-wrap justify-between items-center text-sm text-gray-500">
        <div>
            Total: <span class="font-bold text-gray-700">{{ $registrations->total() }}</span> peserta
        </div>
        <div>
            {{ $registrations->links() }}
        </div>
    </div>
</div>
@endsection