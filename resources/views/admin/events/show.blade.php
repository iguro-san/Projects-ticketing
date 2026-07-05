@extends('layouts.admin')

@section('title', 'Detail Event: ' . $event->title)

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm">{{ $event->category->name }}</span>
                <h1 class="text-3xl font-bold text-gray-800 mt-3">{{ $event->title }}</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-gray-600">
                    <p><i class="fas fa-calendar-alt w-6 text-purple-600"></i> {{ $event->event_date->translatedFormat('l, d F Y') }}</p>
                    <p><i class="fas fa-map-marker-alt w-6 text-purple-600"></i> {{ $event->location }}</p>
                    <p><i class="fas fa-user-tie w-6 text-purple-600"></i> Panitia: {{ $event->panitia->name ?? '-' }}</p>
                    <p><i class="fas fa-ticket-alt w-6 text-purple-600"></i> Total Peserta: {{ $event->registrations_count ?? $event->registrations->count() }}</p>
                </div>
            </div>
            <div>
                @if($event->status === 'draft')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">Draft</span>
                @elseif($event->status === 'active')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Aktif</span>
                @elseif($event->status === 'completed')
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">Selesai</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">Dibatalkan</span>
                @endif
                
                @if($event->suspension_status === 'pending')
                    <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold ml-2">
                        <i class="fas fa-pause mr-1"></i>Pending
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Deskripsi</h2>
        <p class="text-gray-600">{{ $event->description }}</p>
    </div>

    {{-- Poster --}}
    @if($event->poster)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Poster Event</h2>
        <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" 
             class="max-w-full max-h-96 rounded-lg mx-auto cursor-pointer hover:opacity-90 transition"
             onclick="openPosterModal('{{ Storage::url($event->poster) }}')">
    </div>
    @endif

    {{-- Tiket --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Jenis Tiket</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Nama Tiket</th>
                        <th class="px-4 py-2 text-left">Harga</th>
                        <th class="px-4 py-2 text-center">Kuota</th>
                        <th class="px-4 py-2 text-center">Terdaftar</th>
                        <th class="px-4 py-2 text-center">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->ticketTypes as $ticket)
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold">{{ $ticket->name }}</td>
                        <td class="px-4 py-2">
                            @if($ticket->price == 0)
                                <span class="text-green-600 font-bold">GRATIS</span>
                            @else
                                Rp {{ number_format($ticket->price, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">{{ $ticket->quota }}</td>
                        <td class="px-4 py-2 text-center">{{ $ticket->registered }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs {{ $ticket->remaining_quota > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $ticket->remaining_quota }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex gap-3">
        <a href="{{ route('admin.events.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        
        @if($event->status === 'draft')
            <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">
                    <i class="fas fa-check mr-2"></i>Setujui Event
                </button>
            </form>
            <button onclick="showRejectModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                    class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-times mr-2"></i>Tolak Event
            </button>
        @endif
        
        @if($event->status === 'active' && $event->suspension_status === 'normal')
            <button onclick="showPendingModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                    class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                <i class="fas fa-pause mr-2"></i>Pending Event
            </button>
        @endif
        
        @if($event->suspension_status === 'pending')
            <form action="{{ route('admin.events.resolve', [$event, 'continue']) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition"
                        onclick="return confirm('Lanjutkan event ini?')">
                    <i class="fas fa-play mr-2"></i>Lanjutkan Event
                </button>
            </form>
            <form action="{{ route('admin.events.resolve', [$event, 'cancel']) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition"
                        onclick="return confirm('Batalkan event ini? Semua peserta akan direfund.')">
                    <i class="fas fa-ban mr-2"></i>Batalkan Event
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Modal Tolak Event --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Tolak Event</h3>
        <p class="text-sm text-gray-600 mb-3">Event: <span id="rejectTitle" class="font-semibold"></span></p>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Alasan penolakan (opsional)"></textarea>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm">Tolak</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pending Event --}}
<div id="pendingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Pending Event</h3>
        <p class="text-sm text-gray-600 mb-3">Event: <span id="pendingTitle" class="font-semibold"></span></p>
        <form id="pendingForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Alasan penundaan event..." required></textarea>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hidePendingModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm">Pending</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Lightbox untuk poster --}}
<div id="posterModal" class="hidden fixed inset-0 bg-black bg-opacity-90 items-center justify-center z-50" onclick="closePosterModal()">
    <div class="relative max-w-5xl max-h-screen p-4" onclick="event.stopPropagation()">
        <img id="posterModalImage" src="" alt="Poster Event" class="max-w-full max-h-screen object-contain rounded-lg shadow-2xl">
        <button onclick="closePosterModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300 transition">
            <i class="fas fa-times-circle"></i>
        </button>
    </div>
</div>

<script>
    function showRejectModal(id, title) {
        document.getElementById('rejectForm').action = '/admin/events/' + id + '/reject';
        document.getElementById('rejectTitle').innerText = title;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
    function showPendingModal(id, title) {
        document.getElementById('pendingForm').action = '/admin/events/' + id + '/pending';
        document.getElementById('pendingTitle').innerText = title;
        document.getElementById('pendingModal').classList.remove('hidden');
    }
    function hidePendingModal() {
        document.getElementById('pendingModal').classList.add('hidden');
    }
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
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePosterModal();
            hideRejectModal();
            hidePendingModal();
        }
    });
</script>
@endsection