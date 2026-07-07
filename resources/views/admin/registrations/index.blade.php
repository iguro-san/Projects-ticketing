@extends('layouts.admin')

@section('title', isset($event) ? 'Daftar Peserta: ' . $event->title : 'Daftar Registrasi')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(isset($event))
                <i class="fas fa-users mr-2 text-[#760031]"></i>Peserta: {{ $event->title }}
            @else
                <i class="fas fa-list mr-2 text-[#760031]"></i>Daftar Registrasi
            @endif
        </h1>
        <div>
            @if(isset($event))
                <a href="{{ route('admin.events.registrations.export', $event) }}" 
                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition inline-flex items-center text-sm">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            @else
                <a href="{{ route('admin.registrations.export') }}" 
                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition inline-flex items-center text-sm">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            @endif
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form action="{{ isset($event) ? route('admin.events.registrations', $event) : route('admin.registrations.index') }}" 
              method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Cari</label>
                <input type="text" name="search" placeholder="Nama / Email / No Registrasi" 
                       value="{{ request('search') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#760031]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Status Pembayaran</label>
                <select name="payment_status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>✅ Lunas</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>❌ Gagal</option>
                </select>
            </div>
            @if(!isset($event))
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Event</label>
                <select name="event_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                    <option value="">Semua Event</option>
                    @foreach($events as $e)
                        <option value="{{ $e->id }}" {{ request('event_id') == $e->id ? 'selected' : '' }}>{{ $e->title }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-[#760031] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#5e0025] transition">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                <a href="{{ isset($event) ? route('admin.events.registrations', $event) : route('admin.registrations.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm table-auto border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">#</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">No. Registrasi</th>
                    @if(!isset($event))
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Event</th>
                    @endif
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Email</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Status</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($registrations as $reg)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-3 py-2 text-gray-400 text-xs text-center">
                        {{ ($registrations->currentPage() - 1) * $registrations->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-3 py-2 font-mono text-xs font-semibold text-[#760031] whitespace-nowrap">
                        {{ $reg->registration_number }}
                    </td>
                    
                    @if(!isset($event))
                        <td class="px-3 py-2 max-w-[120px] truncate" title="{{ $reg->event->title ?? '-' }}">
                            <span class="font-medium text-gray-800">{{ $reg->event->title ?? '-' }}</span>
                            <br>
                            <span class="text-xs text-gray-400">{{ optional($reg->event->event_date)->translatedFormat('d/m/Y') ?? '' }}</span>
                        </td>
                    @endif
                    
                    <td class="px-3 py-2 max-w-[150px] truncate text-gray-600" title="{{ $reg->user_email }}">
                        {{ $reg->user_email }}
                    </td>
                    
                    <td class="px-3 py-2">
                        @if($reg->payment_status == 'paid')
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                <i class="fas fa-check-circle mr-1"></i> Lunas
                            </span>
                        @elseif($reg->payment_status == 'pending')
                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @elseif($reg->payment_status == 'failed')
                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                <i class="fas fa-times-circle mr-1"></i> Gagal
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                {{ ucfirst($reg->payment_status) }}
                            </span>
                        @endif
                        @if($reg->paid_at)
                            <p class="text-xs text-gray-400 mt-1">{{ $reg->paid_at->translatedFormat('d/m/Y') }}</p>
                        @endif
                    </td>
                    
                    <td class="px-3 py-2 text-center">
                        <button onclick="openDetailModal(this)"
                                data-id="{{ $reg->id }}"
                                data-registration="{{ $reg->registration_number }}"
                                data-name="{{ addslashes($reg->user_name) }}"
                                data-email="{{ $reg->user_email }}"
                                data-event="{{ addslashes($reg->event->title ?? '-') }}"
                                data-ticket="{{ addslashes($reg->ticketType->name ?? '-') }}"
                                data-price="{{ $reg->amount_paid ? number_format($reg->amount_paid, 0, ',', '.') : number_format($reg->ticketType->price ?? 0, 0, ',', '.') }}"
                                data-proof="{{ $reg->payment_proof ? Storage::url($reg->payment_proof) : '' }}"
                                data-status="{{ $reg->payment_status }}"
                                class="text-xs bg-[#760031] text-white px-3 py-1 rounded hover:bg-[#5a0024] transition">
                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ isset($event) ? '6' : '7' }}" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">Belum ada peserta terdaftar</p>
                            <p class="text-sm text-gray-400 mt-1">Belum ada pendaftaran untuk event ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Footer & Pagination --}}
    <div class="mt-6 flex flex-wrap justify-between items-center gap-4 text-sm text-gray-500">
        <div>
            Menampilkan <span class="font-medium text-gray-700">{{ $registrations->firstItem() ?? 0 }}</span> 
            - <span class="font-medium text-gray-700">{{ $registrations->lastItem() ?? 0 }}</span> 
            dari <span class="font-medium text-gray-700">{{ $registrations->total() }}</span> data
        </div>
        <div>
            {{ $registrations->onEachSide(1)->links() }}
        </div>
    </div>
</div>

{{-- Modal Detail Registrasi (sama seperti sebelumnya) --}}
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#760031]">Detail Registrasi</h3>
            <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="detailContent" class="space-y-3"></div>
        <div class="mt-4 flex justify-end">
            <button onclick="closeDetailModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Lightbox untuk Bukti Transfer --}}
<div id="proofModal" class="hidden fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50" onclick="closeProofModal()">
    <div class="relative max-w-5xl max-h-screen p-4" onclick="event.stopPropagation()">
        <img id="proofImage" src="" alt="Bukti Transfer" class="max-w-full max-h-screen object-contain rounded-lg shadow-2xl">
        <button onclick="closeProofModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300 transition">
            <i class="fas fa-times-circle"></i>
        </button>
    </div>
</div>

<script>
    function openDetailModal(button) {
        const registration = button.dataset.registration;
        const name = button.dataset.name;
        const email = button.dataset.email;
        const event = button.dataset.event;
        const ticket = button.dataset.ticket;
        const price = button.dataset.price;
        const proof = button.dataset.proof;
        const status = button.dataset.status;

        const statusMap = {
            'paid': '<span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold"><i class="fas fa-check-circle mr-1"></i> Lunas</span>',
            'pending': '<span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold"><i class="fas fa-clock mr-1"></i> Pending</span>',
            'failed': '<span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold"><i class="fas fa-times-circle mr-1"></i> Gagal</span>',
            'cancelled': '<span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold"><i class="fas fa-ban mr-1"></i> Dibatalkan</span>',
        };
        const statusBadge = statusMap[status] || `<span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">${status}</span>`;

        let proofHtml = '';
        if (proof && proof !== '') {
            proofHtml = `
                <div>
                    <p class="font-semibold text-gray-700">Bukti Transfer</p>
                    <img src="${proof}" alt="Bukti Transfer" class="max-w-full max-h-60 rounded-lg shadow cursor-pointer mx-auto mt-1" onclick="openProofModal('${proof}')">
                </div>
            `;
        } else {
            proofHtml = `
                <div>
                    <p class="font-semibold text-gray-700">Bukti Transfer</p>
                    <p class="text-gray-500 text-sm">Tidak ada bukti transfer</p>
                </div>
            `;
        }

        document.getElementById('detailContent').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <p class="text-sm text-gray-500">No. Registrasi</p>
                    <p class="font-mono font-bold text-[#760031]">${escapeHtml(registration)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    ${statusBadge}
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nama</p>
                    <p class="font-semibold">${escapeHtml(name)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="text-gray-700">${escapeHtml(email)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Event</p>
                    <p class="font-semibold">${escapeHtml(event)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tiket</p>
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">${escapeHtml(ticket)}</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Harga</p>
                    <p class="font-bold text-green-600">Rp ${escapeHtml(price)}</p>
                </div>
            </div>
            <div class="mt-3">
                ${proofHtml}
            </div>
        `;

        document.getElementById('detailModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openProofModal(imageUrl) {
        document.getElementById('proofImage').src = imageUrl;
        document.getElementById('proofModal').classList.remove('hidden');
        document.getElementById('proofModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.add('hidden');
        document.getElementById('proofModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
            closeProofModal();
        }
    });
</script>
@endsection