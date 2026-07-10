@extends('layouts.admin')

@section('title', 'Kelola Refund')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-undo-alt mr-2 text-[#760031]"></i>Kelola Pengembalian Dana
    </h1>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No Registrasi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Event</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Jumlah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Alasan</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($refunds as $reg)
                <tr>
                    <td class="px-4 py-3 font-mono text-sm">{{ $reg->registration_number }}</td>
                    <td class="px-4 py-3">{{ $reg->user_name }}</td>
                    <td class="px-4 py-3">{{ $reg->event->title }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($reg->ticketType->price ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs
                            @if($reg->refund_status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($reg->refund_status == 'processing') bg-blue-100 text-blue-700
                            @elseif($reg->refund_status == 'completed') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($reg->refund_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ Str::limit($reg->refund_reason, 50) }}</td>
                    <td class="px-4 py-3 text-center">
                        @if(in_array($reg->refund_status, ['pending', 'processing']))
                        <button onclick="showProcessModal({{ $reg->id }}, '{{ $reg->registration_number }}', {{ $reg->ticketType->price ?? 0 }})"
                                class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                            <i class="fas fa-check-circle mr-1"></i> Proses
                        </button>
                        @else
                        <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada permintaan refund</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $refunds->links() }}</div>
</div>

{{-- Modal Proses Refund --}}
<div id="processModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-2">Proses Refund</h3>
        <p class="text-sm text-gray-600 mb-3">Registrasi: <span id="processRegNumber" class="font-semibold"></span></p>
        <p class="text-sm text-gray-600 mb-3">Jumlah: <span id="processAmount" class="font-bold text-green-600"></span></p>
        <form id="processForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Tindakan <span class="text-red-500">*</span></label>
                <select name="action" id="refundAction" class="w-full border rounded-lg px-3 py-2 text-sm" required onchange="toggleRefundFields()">
                    <option value="approve">Setujui Refund</option>
                    <option value="reject">Tolak Refund</option>
                </select>
            </div>
            
            <div id="refundFields" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Bank Tujuan <span class="text-red-500">*</span></label>
                    <select name="refund_bank" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="refund_account_name" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Nama pemilik rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="refund_account_number" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Nomor rekening tujuan">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Catatan</label>
                <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm" 
                          placeholder="Catatan untuk user..."></textarea>
            </div>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hideProcessModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showProcessModal(id, regNumber, amount) {
    document.getElementById('processForm').action = '/admin/refunds/' + id + '/process';
    document.getElementById('processRegNumber').innerText = regNumber;
    document.getElementById('processAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    document.getElementById('processModal').classList.remove('hidden');
}

function hideProcessModal() {
    document.getElementById('processModal').classList.add('hidden');
}

function toggleRefundFields() {
    const action = document.getElementById('refundAction').value;
    const fields = document.getElementById('refundFields');
    if (action === 'approve') {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}
</script>
@endsection