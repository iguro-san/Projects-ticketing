@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-edit mr-2 text-purple-600"></i>Edit Event
    </h1>
    
    <form action="{{ route('panitia.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        {{-- Judul --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Event <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Deskripsi <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>{{ old('description', $event->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Tanggal & Lokasi --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        
        {{-- Poster --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Poster Baru (opsional)</label>
            <input type="file" name="poster" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
            @if($event->poster)
                <p class="text-xs text-gray-500 mt-1">Poster saat ini: {{ basename($event->poster) }}</p>
            @endif
        </div>

        {{-- TIKET YANG SUDAH ADA (READ ONLY) --}}
        @if($event->ticketTypes->count() > 0)
        <div class="border-t pt-6 mt-6">
            <h3 class="font-bold text-lg mb-3">
                <i class="fas fa-ticket-alt mr-2 text-purple-500"></i>Tiket Saat Ini
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Nama Tiket</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Harga</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500">Kuota</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500">Terdaftar</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500">Sisa</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($event->ticketTypes as $ticket)
                        <tr>
                            <td class="px-3 py-2 font-semibold">{{ $ticket->name }}</td>
                            <td class="px-3 py-2 font-semibold">
                                @if($ticket->price == 0)
                                    <span class="text-green-600 font-bold">GRATIS</span>
                                @else
                                    Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">{{ $ticket->quota }}</td>
                            <td class="px-3 py-2 text-center">{{ $ticket->registered }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded text-xs {{ $ticket->remaining_quota > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $ticket->remaining_quota }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($ticket->isSoldOut())
                                    <span class="text-xs text-red-500 font-semibold">Habis</span>
                                @else
                                    <span class="text-xs text-green-500 font-semibold">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                <i class="fas fa-info-circle mr-1"></i>Tiket yang sudah ada tidak dapat diedit. Anda hanya bisa menambah tiket baru.
            </p>
        </div>
        @endif

        {{-- TAMBAH TIKET BARU --}}
        <div class="border-t pt-6 mt-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-lg">
                    <i class="fas fa-plus-circle mr-2 text-green-500"></i>Tambah Tiket Baru
                </h3>
                <button type="button" onclick="addTicket()" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
            </div>
            <div id="ticketContainer"></div>
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin menambah tiket baru.</p>
        </div>
        
        {{-- Submit --}}
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                <i class="fas fa-save mr-2"></i>Update Event
            </button>
            <a href="{{ route('panitia.events.index') }}" class="flex-1 bg-gray-300 text-gray-700 text-center py-3 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </form>
</div>

<script>
function addTicket() {
    const div = document.createElement('div');
    div.className = 'ticket-row grid grid-cols-12 gap-2 mb-2 p-3 bg-green-50 rounded-lg border border-green-200 items-end relative';
    div.innerHTML = `
        <div class="col-span-4">
            <label class="text-xs text-gray-500">Nama Tiket</label>
            <input type="text" name="ticket_names[]" placeholder="Nama Tiket" class="w-full border rounded px-2 py-1.5 text-sm" required>
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Harga (isi 0 untuk gratis)</label>
            <input type="number" name="ticket_prices[]" placeholder="0" class="w-full border rounded px-2 py-1.5 text-sm" required min="0">
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Kuota</label>
            <input type="number" name="ticket_quotas[]" placeholder="100" class="w-full border rounded px-2 py-1.5 text-sm" required min="1">
        </div>
        <div class="col-span-2 text-right">
            <button type="button" onclick="this.closest('.ticket-row').remove()" class="text-red-500 text-sm hover:text-red-700">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
        <span class="absolute -top-2 -left-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">BARU</span>
    `;
    document.getElementById('ticketContainer').appendChild(div);
}
</script>
@endsection