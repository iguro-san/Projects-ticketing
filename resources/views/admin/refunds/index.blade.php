@extends('layouts.admin')

@section('title', 'Kelola Refund')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-undo-alt mr-2 text-[#760031]"></i>Pengembalian Dana
        </h1>
        <div class="flex gap-2 items-center">
            <form action="{{ route('admin.refunds.process-all') }}" method="POST" id="processAllForm">
                @csrf
                <input type="hidden" name="ids" id="selectedIds" value="">
                <button type="button" onclick="processAllRefunds()" 
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition text-sm">
                    <i class="fas fa-check-double mr-1"></i> Proses Semua
                </button>
            </form>
            <span class="text-sm text-gray-500">
                Total: <span class="font-bold">{{ $refunds->total() }}</span> refund pending
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Peserta</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Event</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Bank</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No. Rekening</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nominal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($refunds as $reg)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        @if($reg->refund_status == 'pending')
                            <input type="checkbox" class="refund-checkbox" value="{{ $reg->id }}" 
                                   onchange="updateSelectedCount()">
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $reg->user_name }}</div>
                        <div class="text-xs text-gray-500">{{ $reg->user_email }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ Str::limit($reg->event->title, 30) }}</div>
                        <div class="text-xs text-gray-500">{{ $reg->registration_number }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium">{{ $reg->refund_bank ?? '-' }}</span>
                        @if(empty($reg->refund_bank))
                            <span class="text-xs text-red-500 block">Data bank tidak lengkap!</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-sm">
                        {{ $reg->refund_account_number ?? '-' }}
                    </td>
                    <td class="px-4 py-3 font-semibold text-green-600">
                        Rp {{ number_format($reg->refund_amount ?? $reg->amount_paid ?? $reg->ticketType->price ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($reg->refund_status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($reg->refund_status == 'processing') bg-blue-100 text-blue-700
                            @elseif($reg->refund_status == 'completed') bg-green-100 text-green-700
                            @elseif($reg->refund_status == 'rejected') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            @if($reg->refund_status == 'pending')
                                <i class="fas fa-clock mr-1"></i> Menunggu
                            @elseif($reg->refund_status == 'processing')
                                <i class="fas fa-spinner fa-spin mr-1"></i> Diproses
                            @elseif($reg->refund_status == 'completed')
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            @elseif($reg->refund_status == 'rejected')
                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                            @else
                                {{ ucfirst($reg->refund_status) }}
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex gap-1 justify-center flex-wrap">
                            @if(in_array($reg->refund_status, ['pending', 'processing']))
                                <form action="{{ route('admin.refunds.process', $reg) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 transition"
                                            onclick="return confirm('Proses refund untuk {{ $reg->user_name }} sebesar Rp {{ number_format($reg->refund_amount ?? $reg->amount_paid ?? $reg->ticketType->price ?? 0, 0, ',', '.') }}?')">
                                        <i class="fas fa-check mr-1"></i> Proses
                                    </button>
                                </form>
                                <button onclick="showRejectModal('{{ $reg->id }}', '{{ $reg->user_name }}')" 
                                        class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-3 block"></i>
                        <p class="text-lg font-medium">Tidak ada refund pending</p>
                        <p class="text-sm">Semua refund sudah diproses atau tidak ada permintaan refund.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-500">
            Menampilkan {{ $refunds->firstItem() ?? 0 }} - {{ $refunds->lastItem() ?? 0 }} dari {{ $refunds->total() }} data
        </div>
        <div>{{ $refunds->links() }}</div>
    </div>
</div>

{{-- Modal Tolak Refund --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Tolak Refund</h3>
        <p class="text-sm text-gray-600 mb-4">Yakin ingin menolak refund untuk <span id="rejectUserName" class="font-semibold"></span>?</p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Alasan Penolakan</label>
                <textarea name="notes" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" 
                          placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm">Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
// ==========================================
// CHECKBOX FUNCTIONS
// ==========================================
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.refund-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.refund-checkbox:checked').length;
    const total = document.querySelectorAll('.refund-checkbox').length;
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.checked = checked === total && total > 0;
    }
    // Update button text
    const btn = document.querySelector('button[onclick="processAllRefunds()"]');
    if (btn) {
        btn.innerHTML = `<i class="fas fa-check-double mr-1"></i> Proses ${checked} Refund`;
    }
}

function processAllRefunds() {
    const checked = document.querySelectorAll('.refund-checkbox:checked');
    if (checked.length === 0) {
        alert('Pilih minimal 1 refund untuk diproses massal.');
        return;
    }
    
    const ids = Array.from(checked).map(cb => cb.value);
    document.getElementById('selectedIds').value = JSON.stringify(ids);
    
    if (confirm(`Proses ${ids.length} refund sekaligus? Pastikan Anda sudah mentransfer ke rekening masing-masing peserta.`)) {
        document.getElementById('processAllForm').submit();
    }
}

// ==========================================
// REJECT MODAL
// ==========================================
function showRejectModal(id, userName) {
    document.getElementById('rejectUserName').textContent = userName;
    document.getElementById('rejectForm').action = '/admin/refunds/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal on click outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>

<style>
.refund-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
}
</style>
@endsection