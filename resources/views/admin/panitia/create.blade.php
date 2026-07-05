@extends('layouts.admin')

@section('title', 'Tambah Panitia')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-user-plus mr-2 text-[#760031]"></i>Tambah Akun Panitia
    </h1>

    <form action="{{ route('admin.panitia.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" required>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Kata Sandi <span class="text-red-500">*</span></label>
            <input type="password" name="password" 
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                placeholder="Minimal 6 karakter" required>
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-[#760031]" 
                   placeholder="Konfirmasi kata sandi" required>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-[#760031] text-white py-2 rounded-lg hover:bg-[#760031]/80 transition">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
            <a href="{{ route('admin.panitia.index') }}" class="flex-1 bg-gray-300 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </form>
</div>
@endsection