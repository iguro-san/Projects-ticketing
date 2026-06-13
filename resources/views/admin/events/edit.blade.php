@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Event</h1>
    
    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Event</label>
            <input type="text" name="title" value="{{ $event->title }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
            <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $event->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
            <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>{{ $event->description }}</textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal Event</label>
                <input type="date" name="event_date" value="{{ $event->event_date->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Lokasi</label>
                <input type="text" name="location" value="{{ $event->location }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Status</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
                <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Poster Baru (opsional)</label>
            <input type="file" name="poster" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600">
            @if($event->poster)
                <p class="text-xs text-gray-500 mt-1">Poster saat ini: {{ basename($event->poster) }}</p>
            @endif
        </div>
        
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-save"></i> Update Event</button>
            <a href="{{ route('admin.events.index') }}" class="flex-1 bg-gray-300 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection