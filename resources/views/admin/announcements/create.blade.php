@extends('layouts.admin')

@section('title', 'Buat Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-[#760031] mb-6">
        <i class="fas fa-bullhorn mr-2"></i>Buat Pengumuman Baru
    </h1>

    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Pengumuman <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                   placeholder="Contoh: Perubahan Jadwal Event" required>
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Target Penerima <span class="text-red-500">*</span></label>
            <select name="target" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
                <option value="all">📢 Semua User (Panitia + User Biasa)</option>
                <option value="panitia">👥 Panitia Saja</option>
                <option value="user">👤 User Biasa Saja</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Notifikasi akan dikirim ke semua user yang dipilih</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Isi Pengumuman <span class="text-red-500">*</span></label>
            <textarea name="content" rows="6" 
                      class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                      placeholder="Tulis isi pengumuman di sini..." required>{{ old('content') }}</textarea>
            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-blue-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                Pengumuman akan langsung dikirim sebagai notifikasi ke semua user yang dipilih. 
                User akan melihat notifikasi di icon bel (🔔) pada navbar.
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-[#760031] text-white py-2 rounded-lg hover:bg-[#760031]/80 transition">
                <i class="fas fa-paper-plane mr-2"></i>Kirim Pengumuman
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="flex-1 bg-gray-300 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </form>
</div>
@endsection