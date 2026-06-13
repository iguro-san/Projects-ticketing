@extends('layouts.admin')

@section('title', 'Buat Event')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Event Baru</h1>
    
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Event <span class="text-red-500">*</span></label>
            <input type="text" name="title" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Deskripsi <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal Event <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="location" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Poster Event</label>
            <input type="file" name="poster" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
        </div>
        
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-save"></i> Simpan Event
            </button>
            <a href="{{ route('admin.events.index') }}" class="flex-1 bg-gray-300 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection