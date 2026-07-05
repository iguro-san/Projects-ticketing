@extends('layouts.admin')

@section('title', 'Persetujuan Event')

@section('content')
<div class="bg-white rounded-lg shadow p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-check mr-2 text-[#760031]"></i>Persetujuan Event
            </h1>
            <p class="text-sm text-gray-500 mt-1">Setujui atau tolak event yang dibuat panitia</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <form action="{{ route('admin.events.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                <select name="status" class="w-full border rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>⏳ Draft (Butuh Persetujuan)</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>🏁 Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Ditolak/Batal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Waktu</label>
                <select name="time_filter" class="w-full border rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                    <option value="">Semua Waktu</option>
                    <option value="upcoming" {{ request('time_filter') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="past" {{ request('time_filter') == 'past' ? 'selected' : '' }}>Telah Lewat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Kategori</label>
                <select name="category_id" class="w-full border rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Panitia</label>
                <select name="panitia_id" class="w-full border rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                    <option value="">Semua Panitia</option>
                    @foreach($panitia as $p)
                        <option value="{{ $p->id }}" {{ request('panitia_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex gap-2 items-end">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Cari judul/lokasi..." value="{{ request('search') }}"
                           class="w-full border rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#760031] bg-white">
                </div>
                <button type="submit" class="bg-[#760031] text-white px-3 py-2 rounded-lg text-sm hover:bg-[#5e0025] transition"><i class="fas fa-search mr-1"></i>Cari</button>
                <a href="{{ route('admin.events.index') }}" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-400 transition"><i class="fas fa-redo mr-1"></i>Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-6">
        <div class="bg-yellow-50 rounded-lg p-2 text-center border border-yellow-200">
            <p class="text-xs text-yellow-500 font-medium">Butuh Persetujuan</p>
            <p class="text-lg font-bold text-yellow-600">{{ $events->where('status', 'draft')->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-2 text-center border border-green-200">
            <p class="text-xs text-green-500 font-medium">Disetujui</p>
            <p class="text-lg font-bold text-green-600">{{ $events->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-2 text-center border border-blue-200">
            <p class="text-xs text-blue-500 font-medium">Selesai</p>
            <p class="text-lg font-bold text-blue-600">{{ $events->where('status', 'completed')->count() }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-2 text-center border border-red-200">
            <p class="text-xs text-red-500 font-medium">Ditolak</p>
            <p class="text-lg font-bold text-red-600">{{ $events->where('status', 'cancelled')->count() }}</p>
        </div>
    </div>

    <!-- Tabel Event -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Event</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Detail</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Panitia</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 {{ $event->status === 'draft' ? 'bg-yellow-50' : '' }} {{ $event->suspension_status === 'pending' ? 'bg-orange-50' : '' }}">
                    <td class="px-3 py-2 text-xs text-gray-500">{{ $loop->iteration + ($events->firstItem() - 1) }}</td>
                    <td class="px-3 py-2">
                        <div>
                            <p class="font-semibold text-gray-800 text-xs">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($event->location, 20) }}</p>
                        </div>
                    </td>
                    <td class="px-3 py-2">
                        <button onclick="openEventDetail(this)"
                                data-id="{{ $event->id }}"
                                data-title="{{ addslashes($event->title) }}"
                                data-location="{{ addslashes($event->location) }}"
                                data-description="{{ addslashes($event->description) }}"
                                data-poster="{{ $event->poster ? Storage::url($event->poster) : '' }}"
                                data-tickets='{{ json_encode($event->ticketTypes->map(function($ticket) {
                                    return [
                                        'name' => $ticket->name,
                                        'price' => $ticket->price,
                                        'quota' => $ticket->quota,
                                        'registered' => $ticket->registered,
                                        'remaining' => $ticket->remaining_quota
                                    ];
                                })) }}'
                                class="text-xs text-[#760031] hover:text-[#760031]/80 transition flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Lihat Detail
                        </button>
                    </td>
                    <td class="px-3 py-2 whitespace-normal break-words">
                        <span class="inline-block px-2 py-0.5 bg-[#760031]/10 text-[#760031] rounded-full text-xs font-medium max-w-full">
                            {{ $event->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-xs whitespace-nowrap">
                        <span class="{{ $event->event_date < now() ? 'text-red-600' : 'text-green-600' }} font-medium">{{ $event->event_date->translatedFormat('d/m/Y') }}</span>
                        <br><span class="text-xs {{ $event->event_date < now() ? 'text-red-400' : 'text-gray-400' }}">{{ $event->event_date < now() ? 'Lewat' : $event->event_date->diffForHumans() }}</span>
                    </td>
                    <td class="px-3 py-2 text-xs whitespace-nowrap">{{ $event->panitia->name ?? '-' }}</td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        @if($event->status === 'draft')
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold"><i class="fas fa-clock mr-1"></i>Menunggu</span>
                        @elseif($event->status === 'active')
                            @if($event->suspension_status === 'pending')
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold"><i class="fas fa-pause mr-1"></i>Pending</span>
                            @else
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold"><i class="fas fa-check-circle mr-1"></i>Disetujui</span>
                            @endif
                        @elseif($event->status === 'completed')
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold"><i class="fas fa-flag mr-1"></i>Selesai</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-semibold"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        @if($event->status === 'draft')
                            <div class="flex gap-1 justify-center">
                                <form action="{{ route('admin.events.approve', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Setujui event ini?')"
                                            class="bg-green-500 text-white px-2 py-0.5 rounded text-xs hover:bg-green-600 transition font-semibold">
                                        <i class="fas fa-check mr-1"></i>Setujui
                                    </button>
                                </form>
                                <button onclick="showRejectModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                                        class="bg-red-500 text-white px-2 py-0.5 rounded text-xs hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i>Tolak
                                </button>
                            </div>
                        @elseif($event->status === 'active' && $event->suspension_status === 'normal')
                            <button onclick="showPendingModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                                    class="bg-orange-500 text-white px-2 py-0.5 rounded text-xs hover:bg-orange-600 transition">
                                <i class="fas fa-pause mr-1"></i>Pending
                            </button>
                        @elseif($event->suspension_status === 'pending')
                            <div class="flex gap-1 justify-center">
                                <form action="{{ route('admin.events.resolve', [$event, 'continue']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-2 py-0.5 rounded text-xs"
                                            onclick="return confirm('Lanjutkan event ini?')">
                                        <i class="fas fa-play mr-1"></i>Lanjutkan
                                    </button>
                                </form>
                                <form action="{{ route('admin.events.resolve', [$event, 'cancel']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white px-2 py-0.5 rounded text-xs"
                                            onclick="return confirm('Batalkan event ini? Semua peserta akan direfund.')">
                                        <i class="fas fa-times mr-1"></i>Batalkan
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada event</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
</div>

<!-- Modal Tolak Event -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-5 w-80">
        <h3 class="text-lg font-bold mb-2">Tolak Event</h3>
        <p class="text-sm text-gray-600 mb-2">Alasan penolakan: <span id="rejectTitle" class="font-semibold"></span></p>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="reason" rows="2" class="w-full border rounded-lg px-2 py-1 text-sm focus:outline-none focus:border-[#760031]" placeholder="Alasan (opsional)"></textarea>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-3 py-1 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm">Tolak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pending Event -->
<div id="pendingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-5 w-80">
        <h3 class="text-lg font-bold mb-2">Pending Event</h3>
        <p class="text-sm text-gray-600 mb-2">Event: <span id="pendingTitle" class="font-semibold"></span></p>
        <form id="pendingForm" method="POST">
            @csrf
            <textarea name="reason" rows="2" class="w-full border rounded-lg px-2 py-1 text-sm focus:outline-none focus:border-[#760031]" placeholder="Alasan penundaan event..." required></textarea>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hidePendingModal()" class="px-3 py-1 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-3 py-1 bg-orange-500 text-white rounded-lg text-sm">Pending</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Event (dengan poster & tiket) -->
<div id="eventDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-5 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-xl font-bold text-[#760031]">Detail Event</h3>
            <button onclick="closeEventDetailModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="eventDetailContent">
            <!-- Konten akan diisi oleh JavaScript -->
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="closeEventDetailModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Lightbox untuk poster -->
<div id="posterModal" class="hidden fixed inset-0 bg-black bg-opacity-90 items-center justify-center z-50" onclick="closePosterModal()">
    <div class="relative max-w-5xl max-h-screen p-4" onclick="event.stopPropagation()">
        <img id="posterModalImage" src="" alt="Poster Event" class="max-w-full max-h-screen object-contain rounded-lg shadow-2xl">
        <button onclick="closePosterModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300 transition">
            <i class="fas fa-times-circle"></i>
        </button>
    </div>
</div>

<script>
    
    // REJECT MODAL
    
    function showRejectModal(id, title) {
        document.getElementById('rejectForm').action = '/admin/events/' + id + '/reject';
        document.getElementById('rejectTitle').innerText = title;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    
    // PENDING MODAL
    
    function showPendingModal(id, title) {
        document.getElementById('pendingForm').action = '/admin/events/' + id + '/pending';
        document.getElementById('pendingTitle').innerText = title;
        document.getElementById('pendingModal').classList.remove('hidden');
    }
    function hidePendingModal() {
        document.getElementById('pendingModal').classList.add('hidden');
    }

    
    // DETAIL EVENT MODAL (dengan Tiket)
    
    function openEventDetail(button) {
        // Ambil data dari atribut tombol
        const title = button.dataset.title || 'Tanpa Judul';
        const location = button.dataset.location || 'Tidak diketahui';
        const description = button.dataset.description || 'Tidak ada deskripsi';
        const poster = button.dataset.poster || '';
        let tickets = [];

        try {
            tickets = JSON.parse(button.dataset.tickets || '[]');
        } catch (e) {
            tickets = [];
        }

        const contentDiv = document.getElementById('eventDetailContent');

        // Buat HTML poster
        let posterHtml = '';
        if (poster) {
            posterHtml = `
                <div class="mb-3">
                    <h4 class="font-semibold text-gray-800 mb-1">Poster Event</h4>
                    <img src="${poster}" alt="${title}" class="max-w-full max-h-48 rounded-lg shadow cursor-pointer mx-auto" onclick="openPosterModal('${poster}')">
                </div>
            `;
        } else {
            posterHtml = `
                <div class="mb-3 bg-gray-100 rounded-lg p-3 text-center">
                    <i class="fas fa-image text-3xl text-gray-400"></i>
                    <p class="text-gray-500 text-xs mt-1">Tidak ada poster</p>
                </div>
            `;
        }

        // Buat HTML tabel tiket
        let ticketHtml = '';
        if (tickets.length > 0) {
            let rows = '';
            tickets.forEach(function(ticket) {
                const remaining = ticket.quota - ticket.registered;
                const statusClass = remaining > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                rows += `
                    <tr class="border-b">
                        <td class="px-3 py-2 font-semibold">${escapeHtml(ticket.name)}</td>
                        <td class="px-3 py-2">${ticket.price == 0 ? '<span class="text-green-600 font-bold">GRATIS</span>' : 'Rp ' + Number(ticket.price).toLocaleString('id-ID')}</td>
                        <td class="px-3 py-2 text-center">${ticket.quota}</td>
                        <td class="px-3 py-2 text-center">${ticket.registered}</td>
                        <td class="px-3 py-2 text-center"><span class="px-2 py-1 rounded text-xs ${statusClass}">${remaining}</span></td>
                    </tr>
                `;
            });
            ticketHtml = `
                <div class="mt-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Jenis Tiket</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nama Tiket</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Harga</th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Kuota</th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                ${rows}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        } else {
            ticketHtml = `
                <div class="mt-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-ticket-alt mr-1"></i> Belum ada tiket untuk event ini.
                </div>
            `;
        }

        // Gabungkan semua konten
        contentDiv.innerHTML = `
            <div class="mb-2">
                <h4 class="font-semibold text-gray-800">Judul Event</h4>
                <p class="text-gray-700 text-sm">${escapeHtml(title)}</p>
            </div>
            <div class="mb-2">
                <h4 class="font-semibold text-gray-800">Lokasi</h4>
                <p class="text-gray-700 text-sm">${escapeHtml(location)}</p>
            </div>
            ${posterHtml}
            <div class="mb-2">
                <h4 class="font-semibold text-gray-800">Deskripsi</h4>
                <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">${escapeHtml(description).replace(/\n/g, '<br>')}</div>
            </div>
            ${ticketHtml}
        `;

        document.getElementById('eventDetailModal').classList.remove('hidden');
    }

    function closeEventDetailModal() {
        document.getElementById('eventDetailModal').classList.add('hidden');
    }

    
    // POSTER LIGHTBOX
    
    function openPosterModal(imageUrl) {
        const modal = document.getElementById('posterModal');
        const modalImg = document.getElementById('posterModalImage');
        modalImg.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closePosterModal() {
        const modal = document.getElementById('posterModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    
    // UTILITY: ESCAPE HTML
    
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    
    // KEYBOARD SHORTCUT
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePosterModal();
            hideRejectModal();
            hidePendingModal();
            closeEventDetailModal();
        }
    });
</script>
@endsection