@extends('layouts.app')

@section('title', 'Buat Event Baru')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-calendar-plus mr-2 text-purple-600"></i>Buat Event Baru
    </h1>
    
    <form action="{{ route('panitia.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Judul -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Event <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Kategori -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Deskripsi -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Deskripsi <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Tanggal & Lokasi -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" value="{{ old('event_date') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <!-- Poster -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Poster Event</label>
            <input type="file" name="poster" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
        </div>
        
        <!-- TIKET -->
        <div class="border-t pt-6 mt-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-lg">Jenis Tiket <span class="text-red-500">*</span></h3>
                <button type="button" onclick="addTicket()" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-1"></i>Tambah Tiket
                </button>
            </div>
            @error('ticket_names') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
            <div id="ticketContainer">
                <div class="ticket-row grid grid-cols-12 gap-2 mb-2 p-3 bg-gray-50 rounded-lg items-end">
                    <div class="col-span-4">
                        <label class="text-xs text-gray-500">Nama</label>
                        <input type="text" name="ticket_names[]" placeholder="Nama Tiket" class="w-full border rounded px-2 py-1.5 text-sm" required>
                    </div>
                    <div class="col-span-3">
                        <label class="text-xs text-gray-500">Harga (Rp)</label>
                        <input type="number" name="ticket_prices[]" placeholder="0" class="w-full border rounded px-2 py-1.5 text-sm" required min="0">
                    </div>
                    <div class="col-span-3">
                        <label class="text-xs text-gray-500">Kuota</label>
                        <input type="number" name="ticket_quotas[]" placeholder="100" class="w-full border rounded px-2 py-1.5 text-sm" required min="1">
                    </div>
                    <div class="col-span-2 text-right">
                        <button type="button" onclick="this.closest('.ticket-row').remove()" class="text-red-500 text-sm hover:text-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                <i class="fas fa-save mr-2"></i>Simpan Event
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
    div.className = 'ticket-row grid grid-cols-12 gap-2 mb-2 p-3 bg-gray-50 rounded-lg items-end';
    div.innerHTML = `
        <div class="col-span-4">
            <label class="text-xs text-gray-500">Nama</label>
            <input type="text" name="ticket_names[]" placeholder="Nama Tiket" class="w-full border rounded px-2 py-1.5 text-sm" required>
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Harga (Rp)</label>
            <input type="number" name="ticket_prices[]" placeholder="0" class="w-full border rounded px-2 py-1.5 text-sm" required min="0">
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Kuota</label>
            <input type="number" name="ticket_quotas[]" placeholder="100" class="w-full border rounded px-2 py-1.5 text-sm" required min="1">
        </div>
        <div class="col-span-2 text-right">
            <button type="button" onclick="this.closest('.ticket-row').remove()" class="text-red-500 text-sm hover:text-red-700">Hapus</button>
        </div>
    `;
    document.getElementById('ticketContainer').appendChild(div);
}
</script>
@endsection