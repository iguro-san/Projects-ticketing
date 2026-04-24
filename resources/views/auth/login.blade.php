@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="bg-purple-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Welcome Back!</h2>
                <p class="text-gray-600 mt-2">Silakan login ke akun Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 @error('email') border-red-500 @enderror"
                               placeholder="Masukkan email Anda" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-purple-600 @error('password') border-red-500 @enderror"
                               placeholder="Masukkan password Anda" required>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-purple-600 font-semibold hover:text-purple-800">
                        Daftar sekarang
                    </a>
                </p>
            </div>

            <!-- Demo Accounts -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-semibold mb-3 text-gray-700 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                    Akun Demo:
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="bg-white p-2 rounded border">
                        <span class="font-semibold text-red-600">Admin:</span>
                        <span class="text-gray-600">admin@example.com / password</span>
                    </div>
                    <div class="bg-white p-2 rounded border">
                        <span class="font-semibold text-blue-600">Panitia:</span>
                        <span class="text-gray-600">panitia@example.com / password</span>
                    </div>
                    <div class="bg-white p-2 rounded border">
                        <span class="font-semibold text-green-600">User:</span>
                        <span class="text-gray-600">user@example.com / password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection