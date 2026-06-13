@extends('layouts.admin')

@section('title', 'Kelola Event')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>Kelola Event
        </h1>
    </div>

    {{-- Filter --}}
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <form action="{{ route('admin.events.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" placeholder="Cari event..." value="{{ request('search') }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600">
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Event</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Panitia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Tanggal</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Suspensi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 {{ $event->suspension_status === 'pending' ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-3">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400">{{ $event->location }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $event->panitia->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $event->event_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($event->status === 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span>
                        @elseif($event->status === 'draft')
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">Draft</span>
                        @elseif($event->status === 'completed')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Selesai</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Batal</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($event->suspension_status === 'pending')
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">Normal</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($event->status === 'active' && $event->suspension_status === 'normal')
                            <button onclick="showPendingModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                                    class="bg-orange-500 text-white px-3 py-1 rounded text-xs hover:bg-orange-600 transition">
                                <i class="fas fa-pause mr-1"></i>Pending
                            </button>
                        @elseif($event->suspension_status === 'pending')
                            <div class="flex gap-1 justify-center">
                                <form action="{{ route('admin.events.resolve', [$event, 'continue']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded text-xs"
                                            onclick="return confirm('Lanjutkan event ini?')">
                                        <i class="fas fa-play mr-1"></i>Lanjutkan
                                    </button>
                                </form>
                                <form action="{{ route('admin.events.resolve', [$event, 'cancel']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs"
                                            onclick="return confirm('Batalkan event ini? Semua peserta akan direfund.')">
                                        <i class="fas fa-times mr-1"></i>Batalkan
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada event</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">{{ $events->links() }}</div>
</div>

{{-- Modal Pending --}}
<div id="pendingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Pending Event</h3>
        <p class="text-sm text-gray-600 mb-3">Event: <span id="pendingTitle" class="font-semibold"></span></p>
        <form id="pendingForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" 
                      placeholder="Alasan penundaan event..." required></textarea>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hidePendingModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm">Pending</button>
            </div>
        </form>
    </div>
</div>

<script>
function showPendingModal(id, title) {
    document.getElementById('pendingForm').action = '/admin/events/' + id + '/pending';
    document.getElementById('pendingTitle').innerText = title;
    document.getElementById('pendingModal').classList.remove('hidden');
}
function hidePendingModal() {
    document.getElementById('pendingModal').classList.add('hidden');
}
</script>
@endsection