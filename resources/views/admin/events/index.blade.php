@extends('layouts.app')

@section('title', 'Persetujuan Event')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-check mr-2 text-purple-600"></i>Persetujuan Event
            </h1>
            <p class="text-sm text-gray-500 mt-1">Setujui atau tolak event yang dibuat panitia</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <form action="{{ route('admin.events.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>⏳ Draft (Butuh Persetujuan)</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>🏁 Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Ditolak/Batal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Waktu</label>
                <select name="time_filter" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Waktu</option>
                    <option value="upcoming" {{ request('time_filter') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="past" {{ request('time_filter') == 'past' ? 'selected' : '' }}>Telah Lewat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Kategori</label>
                <select name="category_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Panitia</label>
                <select name="panitia_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                    <option value="">Semua Panitia</option>
                    @foreach($panitia as $p)
                        <option value="{{ $p->id }}" {{ request('panitia_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex gap-2 items-end">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Cari judul/lokasi..." value="{{ request('search') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-600 bg-white">
                </div>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition"><i class="fas fa-search mr-1"></i>Filter</button>
                <a href="{{ route('admin.events.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition"><i class="fas fa-redo mr-1"></i>Reset</a>
            </div>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-yellow-50 rounded-lg p-3 text-center border border-yellow-200">
            <p class="text-xs text-yellow-500 font-medium">Butuh Persetujuan</p>
            <p class="text-xl font-bold text-yellow-600">{{ $events->where('status', 'draft')->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-3 text-center border border-green-200">
            <p class="text-xs text-green-500 font-medium">Disetujui</p>
            <p class="text-xl font-bold text-green-600">{{ $events->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-200">
            <p class="text-xs text-blue-500 font-medium">Selesai</p>
            <p class="text-xl font-bold text-blue-600">{{ $events->where('status', 'completed')->count() }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-3 text-center border border-red-200">
            <p class="text-xs text-red-500 font-medium">Ditolak</p>
            <p class="text-xl font-bold text-red-600">{{ $events->where('status', 'cancelled')->count() }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Event</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Panitia</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 {{ $event->status === 'draft' ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration + ($events->firstItem() - 1) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            @if($event->poster)
                                <img src="{{ Storage::url($event->poster) }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-calendar-alt text-purple-500 text-sm"></i></div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $event->title }}</p>
                                <p class="text-xs text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($event->location, 25) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3"><span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">{{ $event->category->name ?? '-' }}</span></td>
                    <td class="px-4 py-3 text-sm">
                        <span class="{{ $event->event_date < now() ? 'text-red-600' : 'text-green-600' }} font-medium">{{ $event->event_date->format('d/m/Y') }}</span>
                        <br><span class="text-xs {{ $event->event_date < now() ? 'text-red-400' : 'text-gray-400' }}">{{ $event->event_date < now() ? 'Lewat' : $event->event_date->diffForHumans() }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $event->panitia->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($event->status === 'draft')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold"><i class="fas fa-clock mr-1"></i>Menunggu</span>
                        @elseif($event->status === 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold"><i class="fas fa-check-circle mr-1"></i>Disetujui</span>
                        @elseif($event->status === 'completed')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold"><i class="fas fa-flag mr-1"></i>Selesai</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($event->status === 'draft')
                            <div class="flex gap-1 justify-center">
                                <form action="{{ route('admin.events.approve', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Setujui event ini?')"
                                            class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 transition font-semibold">
                                        <i class="fas fa-check mr-1"></i>Setujui
                                    </button>
                                </form>
                                <button onclick="showRejectModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                                        class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i>Tolak
                                </button>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Belum ada event</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
</div>

{{-- Modal Tolak --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Tolak Event</h3>
        <p class="text-sm text-gray-600 mb-3">Alasan penolakan: <span id="rejectTitle" class="font-semibold"></span></p>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Alasan (opsional)"></textarea>
            <div class="flex gap-2 mt-3 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm">Tolak</button>
            </div>
        </form>
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
</script>
@endsection