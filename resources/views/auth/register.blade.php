@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="bg-purple-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Daftar Akun</h2>
                <p class="text-gray-600 mt-2">Buat akun untuk mendaftar event</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 @error('email') border-red-500 @enderror"
                               placeholder="Masukkan email" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Phone (Opsional)</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 @error('phone') border-red-500 @enderror"
                               placeholder="Masukkan nomor telepon">
                    </div>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 @error('password') border-red-500 @enderror"
                               placeholder="Minimal 6 karakter" required>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password_confirmation" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600"
                               placeholder="Ulangi password" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i> Daftar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-purple-600 font-semibold hover:text-purple-800">
                        Login sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection