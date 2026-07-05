@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="bg-[#141E46] w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key text-3xl text-[#B6771D]"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#141E46]">Lupa Kata Sandi?</h2>
                <p class="text-gray-600 mt-2">Masukkan email Anda, kami akan mengirimkan tautan reset kata sandi.</p>
            </div>

            @if(session('reset_link'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-700 font-semibold mb-2">🔗 Tautan Reset Kata Sandi (Development Mode):</p>
                <a href="{{ session('reset_link') }}" target="_blank" class="text-sm text-blue-600 break-all hover:underline">
                    {{ session('reset_link') }}
                </a>
                <p class="text-xs text-blue-500 mt-2">*Klik tautan di atas untuk reset kata sandi</p>
            </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:border-[#B6771D] @error('email') border-red-500 @enderror"
                               placeholder="Masukkan email Anda" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-[#B6771D] text-white py-3 rounded-lg font-semibold hover:bg-[#B6771D]/80 transition duration-300">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Tautan Reset Kata Sandi
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    <a href="{{ route('login') }}" class="text-[#B6771D] font-semibold hover:text-[#B6771D]/80">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Masuk
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection