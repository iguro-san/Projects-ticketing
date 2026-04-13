@extends('layouts.app')

@section('title', 'Daftar Peserta')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Peserta: {{ $event->title }}</h1>
        <a href="{{ route('admin.events.registrations.export', $event) }}" 
           class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-file-excel"></i> Export ke Excel
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
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registrations as $reg)
                <tr>
                    <td class="px-4 py-3 font-mono text-sm">{{ $reg->registration_number }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $reg->user_name }}</td>
                    <td class="px-4 py-3">{{ $reg->user_email }}</td>
                    <td class="px-4 py-3">{{ $reg->ticketType->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs
                            @if($reg->payment_status == 'paid') bg-green-100 text-green-700
                            @elseif($reg->payment_status == 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($reg->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.events.registrations.update-payment', [$event, $reg]) }}" method="POST" class="flex gap-2">
                            @csrf @method('PUT')
                            <select name="payment_status" class="border rounded px-2 py-1 text-sm focus:outline-none focus:border-purple-600">
                                <option value="pending" {{ $reg->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $reg->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $reg->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                                Update
                            </button>
                        </form>
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
</div>
@endsection