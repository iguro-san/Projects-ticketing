@extends('layouts.app')

@section('title', 'Buat Event Baru')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-calendar-plus mr-2 text-[#760031]"></i>Buat Event Baru
    </h1>
    
    <form action="{{ route('panitia.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
        @csrf
        
        <!-- Judul -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Event <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                   placeholder="Masukkan judul event" required>
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Kategori -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
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
            <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
            placeholder="Masukkan deskripsi event" required>{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Tanggal & Lokasi -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" value="{{ old('event_date') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
                @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" placeholder="Contoh: Aula Kampus" required>
                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <!-- Poster -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Poster Event</label>
            <input type="file" name="poster" accept="image/*" id="posterInput" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WebP (Max 2MB)</p>
            <div id="posterAlert" class="hidden mt-2 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i> Ukuran poster melebihi 2MB. Silakan pilih file yang lebih kecil.
            </div>
        </div>
        
        <!-- TIKET -->
        <div class="border-t pt-6 mt-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-lg">Jenis Tiket <span class="text-red-500">*</span></h3>
                <button type="button" onclick="addTicket()" class="bg-[#B6771D] text-white px-3 py-1 rounded text-sm hover:bg-[#5a0024] transition">
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
                        <input type="text" name="ticket_prices[]" placeholder="0" 
                               class="w-full border rounded px-2 py-1.5 text-sm format-number" 
                               required min="0">
                    </div>
                    <div class="col-span-3">
                        <label class="text-xs text-gray-500">Kuota</label>
                        <input type="text" name="ticket_quotas[]" placeholder="100" 
                               class="w-full border rounded px-2 py-1.5 text-sm format-number" 
                               required min="1">
                    </div>
                    <div class="col-span-2 text-right">
                        <button type="button" onclick="this.closest('.ticket-row').remove()" class="text-red-500 text-sm hover:text-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-[#760031] text-white py-3 rounded-lg hover:bg-[#5a0024] transition font-semibold">
                <i class="fas fa-save mr-2"></i>Simpan Event
            </button>
            <a href="{{ route('panitia.events.index') }}" class="flex-1 bg-gray-300 text-gray-700 text-center py-3 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </form>
</div>

<script>

// FUNGSI FORMAT ANGKA RIBUAN

function formatNumber(value) {
    // Hapus semua karakter non-digit
    let num = value.replace(/\D/g, '');
    if (num === '') return '';
    // Konversi ke number dan format dengan titik ribuan
    return parseInt(num, 10).toLocaleString('id-ID');
}

function formatInputField(input) {
    let cursorPos = input.selectionStart;
    let raw = input.value.replace(/\D/g, '');
    if (raw === '') {
        input.value = '';
        return;
    }
    let formatted = parseInt(raw, 10).toLocaleString('id-ID');
    input.value = formatted;
    // Atur posisi kursor setelah format
    let newPos = cursorPos + (formatted.length - raw.length);
    input.setSelectionRange(newPos, newPos);
}

function attachFormatListener() {
    document.querySelectorAll('.format-number').forEach(function(input) {
        // Hapus listener lama untuk mencegah duplikasi
        input.removeEventListener('input', formatHandler);
        input.addEventListener('input', formatHandler);
    });
}

function formatHandler(e) {
    formatInputField(e.target);
}

// Terapkan ke semua input yang sudah ada
document.addEventListener('DOMContentLoaded', function() {
    attachFormatListener();
});

// TAMBAH TIKET (DENGAN FORMAT NUMBER)
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
            <input type="text" name="ticket_prices[]" placeholder="0" class="w-full border rounded px-2 py-1.5 text-sm format-number" required min="0">
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Kuota</label>
            <input type="text" name="ticket_quotas[]" placeholder="100" class="w-full border rounded px-2 py-1.5 text-sm format-number" required min="1">
        </div>
        <div class="col-span-2 text-right">
            <button type="button" onclick="this.closest('.ticket-row').remove()" class="text-red-500 text-sm hover:text-red-700">Hapus</button>
        </div>
    `;
    document.getElementById('ticketContainer').appendChild(div);
    // Terapkan format listener ke input baru
    attachFormatListener();
}

// VALIDASI UKURAN POSTER (MAX 2MB)
document.getElementById('posterInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const alertDiv = document.getElementById('posterAlert');
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (file) {
        if (file.size > maxSize) {
            alertDiv.classList.remove('hidden');
            this.value = ''; // reset input
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alertDiv.classList.add('hidden');
        }
    } else {
        alertDiv.classList.add('hidden');
    }
});


// HAPUS TITIK SEBELUM SUBMIT

document.getElementById('eventForm').addEventListener('submit', function(e) {
    // Cek ukuran poster
    const fileInput = document.getElementById('posterInput');
    const alertDiv = document.getElementById('posterAlert');
    const maxSize = 2 * 1024 * 1024;

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.size > maxSize) {
            e.preventDefault();
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Ukuran poster melebihi 2MB! Silakan pilih file yang lebih kecil.');
            return false;
        }
    }

    // Hapus titik pada input harga dan kuota agar nilai menjadi angka murni
    document.querySelectorAll('.format-number').forEach(function(input) {
        let raw = input.value.replace(/\D/g, '');
        if (raw !== '') {
            input.value = raw;
        }
    });
});
</script>
@endsection