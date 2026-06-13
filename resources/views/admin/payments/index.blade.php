@extends('layouts.admin')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-credit-card mr-2 text-purple-600"></i>Konfirmasi Pembayaran
    </h1>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No Registrasi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Event</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Jumlah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Bukti Bayar</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pendingPayments as $reg)
                <tr>
                    <td class="px-4 py-3 font-mono text-sm">{{ $reg->registration_number }}</td>
                    <td class="px-4 py-3">{{ $reg->user_name }}</td>
                    <td class="px-4 py-3">{{ $reg->event->title }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($reg->ticketType->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ Storage::url($reg->payment_proof) }}" target="_blank" 
                           class="text-blue-500 hover:underline text-sm">
                            <i class="fas fa-image mr-1"></i> Lihat
                        </a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex gap-1 justify-center">
                            <form action="{{ route('admin.payments.confirm', $reg) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="paid">
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs"
                                        onclick="return confirm('Konfirmasi pembayaran ini?')">
                                    <i class="fas fa-check mr-1"></i> Konfirmasi
                                </button>
                            </form>
                            <form action="{{ route('admin.payments.confirm', $reg) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="failed">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs"
                                        onclick="return confirm('Tolak pembayaran ini?')">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada pembayaran yang menunggu konfirmasi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pendingPayments->links() }}</div>
</div>
@endsection