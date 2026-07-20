@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-edit mr-2 text-purple-600"></i>Edit Event
    </h1>

    {{-- ========================================== --}}
    {{-- FORM UPDATE EVENT (TIDAK MENCAKUP TIKET LAMA) --}}
    {{-- ========================================== --}}
    <form action="{{ route('panitia.events.update', $event) }}" method="POST" enctype="multipart/form-data" id="eventForm">
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
            <input type="file" name="poster" accept="image/*" id="posterInput" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WebP (Max 2MB)</p>
            @if($event->poster)
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-image mr-1"></i> Poster saat ini: {{ basename($event->poster) }}
                    <a href="{{ Storage::url($event->poster) }}" target="_blank" class="text-blue-500 hover:underline ml-2">Lihat</a>
                </p>
            @endif
            <div id="posterAlert" class="hidden mt-2 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i> Ukuran poster melebihi 2MB. Silakan pilih file yang lebih kecil.
            </div>
            @error('poster') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- ========================================== --}}
        {{-- TAMBAH TIKET BARU (masih dalam form update) --}}
        {{-- ========================================== --}}
        <div class="border-t pt-6 mt-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-lg">
                    <i class="fas fa-plus-circle mr-2 text-green-500"></i>Tambah Tiket Baru
                </h3>
                <button type="button" onclick="addTicket()" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
            </div>
            <div id="ticketContainer">
                <!-- Tiket baru akan ditambahkan di sini melalui JavaScript -->
            </div>
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin menambah tiket baru.</p>
            @error('ticket_names') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('ticket_prices') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('ticket_quotas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

    {{-- ========================================== --}}
    {{-- TIKET YANG SUDAH ADA (DI LUAR FORM UPDATE) --}}
    {{-- ========================================== --}}
    @if($event->ticketTypes->count() > 0)
    <div class="border-t pt-6 mt-8">
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
                        <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500">Aksi</th>
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
                        <td class="px-3 py-2 text-center">
                            @if($ticket->registrations()->count() == 0)
                                {{-- ✅ FORM HAPUS TIKET (di luar form update) --}}
                                <form action="{{ route('panitia.events.tickets.destroy', [$event, $ticket]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus tiket {{ $ticket->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs" title="Hapus tiket">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs" title="Tiket sudah ada pendaftar">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            <i class="fas fa-info-circle mr-1"></i>Tiket yang sudah ada pendaftar tidak dapat dihapus.
        </p>
    </div>
    @endif
</div>

{{-- ========================================== --}}
{{-- JAVASCRIPT (untuk tambah tiket & validasi) --}}
{{-- ========================================== --}}
<script>
/**
 * ==========================================
 * TAMBAH TIKET BARU
 * ==========================================
 */
let ticketCounter = 0;

function addTicket() {
    const container = document.getElementById('ticketContainer');
    const div = document.createElement('div');
    ticketCounter++;
    const uniqueId = 'ticket_' + ticketCounter + '_' + Date.now();

    div.className = 'ticket-row grid grid-cols-12 gap-2 mb-2 p-3 bg-green-50 rounded-lg border border-green-200 items-end relative';
    div.id = uniqueId;
    div.innerHTML = `
        <div class="col-span-4">
            <label class="text-xs text-gray-500">Nama Tiket <span class="text-red-500">*</span></label>
            <input type="text" name="ticket_names[]" placeholder="Nama Tiket" class="w-full border rounded px-2 py-1.5 text-sm" required>
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Harga (isi 0 untuk gratis) <span class="text-red-500">*</span></label>
            <input type="number" name="ticket_prices[]" placeholder="0" class="w-full border rounded px-2 py-1.5 text-sm" min="0" step="1" required>
        </div>
        <div class="col-span-3">
            <label class="text-xs text-gray-500">Kuota <span class="text-red-500">*</span></label>
            <input type="number" name="ticket_quotas[]" placeholder="100" class="w-full border rounded px-2 py-1.5 text-sm" min="1" step="1" required>
        </div>
        <div class="col-span-2 text-right">
            <button type="button" onclick="removeTicket('${uniqueId}')" class="text-red-500 text-sm hover:text-red-700">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
        <span class="absolute -top-2 -left-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">BARU</span>
    `;
    container.appendChild(div);

    setTimeout(() => {
        const firstInput = div.querySelector('input[name="ticket_names[]"]');
        if (firstInput) firstInput.focus();
    }, 100);

    div.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeTicket(id) {
    const element = document.getElementById(id);
    if (element) {
        if (confirm('Hapus tiket ini?')) {
            element.remove();
        }
    }
}

/**
 * ==========================================
 * VALIDASI UKURAN POSTER (MAX 2MB)
 * ==========================================
 */
document.addEventListener('DOMContentLoaded', function() {
    const posterInput = document.getElementById('posterInput');
    if (posterInput) {
        posterInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const alertDiv = document.getElementById('posterAlert');
            const maxSize = 2 * 1024 * 1024;

            if (file) {
                if (file.size > maxSize) {
                    alertDiv.classList.remove('hidden');
                    this.value = '';
                    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    alertDiv.classList.add('hidden');
                }
            } else {
                alertDiv.classList.add('hidden');
            }
        });
    }
});

/**
 * ==========================================
 * VALIDASI SEBELUM SUBMIT (FORM UPDATE)
 * ==========================================
 */
document.getElementById('eventForm').addEventListener('submit', function(e) {
    // Cek ukuran poster
    const fileInput = document.getElementById('posterInput');
    const alertDiv = document.getElementById('posterAlert');
    const maxSize = 2 * 1024 * 1024;

    if (fileInput && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.size > maxSize) {
            e.preventDefault();
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Ukuran poster melebihi 2MB! Silakan pilih file yang lebih kecil.');
            return false;
        }
    }

    // Validasi tiket baru (jika ada)
    const ticketRows = document.querySelectorAll('.ticket-row');
    let hasError = false;

    ticketRows.forEach(function(row) {
        const nameInput = row.querySelector('input[name="ticket_names[]"]');
        const priceInput = row.querySelector('input[name="ticket_prices[]"]');
        const quotaInput = row.querySelector('input[name="ticket_quotas[]"]');

        if (!nameInput || !nameInput.value.trim()) {
            alert('Nama tiket tidak boleh kosong!');
            if (nameInput) {
                nameInput.focus();
                nameInput.style.borderColor = 'red';
            }
            hasError = true;
            return;
        }

        if (!priceInput) {
            alert('Harga tiket harus diisi!');
            hasError = true;
            return;
        }

        const price = parseInt(priceInput.value);
        if (isNaN(price) || price < 0) {
            alert('Harga tiket harus diisi dengan angka yang valid (min 0)!');
            priceInput.focus();
            priceInput.style.borderColor = 'red';
            hasError = true;
            return;
        }

        if (!quotaInput) {
            alert('Kuota tiket harus diisi!');
            hasError = true;
            return;
        }

        const quota = parseInt(quotaInput.value);
        if (isNaN(quota) || quota < 1) {
            alert('Kuota tiket harus diisi dengan angka minimal 1!');
            quotaInput.focus();
            quotaInput.style.borderColor = 'red';
            hasError = true;
            return;
        }
    });

    if (hasError) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection