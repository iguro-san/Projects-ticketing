@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- User Header Banner --}}
        <div class="bg-gradient-to-r from-[#1e3a8a] to-[#312e81] rounded-lg p-8 mb-8 text-white">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-3xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                    <p class="text-blue-100">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Profile Card --}}
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="bg-gradient-to-r from-orange-400 to-orange-300 px-6 py-4 flex items-center gap-3">
                    <i class="fas fa-user text-white text-xl"></i>
                    <div>
                        <h2 class="text-white font-semibold">Profil</h2>
                        <p class="text-orange-50 text-sm">Detail identitas dan kontak akun kamu</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Nama
                        </label>
                        <input type="text" value="{{ $user->name }}" readonly 
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 cursor-not-allowed focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>Email
                        </label>
                        <input type="email" value="{{ $user->email }}" readonly 
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 cursor-not-allowed focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-gray-400 mr-2"></i>Nomor Telepon
                        </label>
                        <input type="text" value="{{ $user->phone ?? '-' }}" readonly 
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 cursor-not-allowed focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-star text-gray-400 mr-2"></i>Role
                        </label>
                        <input type="text" value="{{ ucfirst($user->role) }}" readonly 
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 cursor-not-allowed focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- Password Card --}}
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="bg-gradient-to-r from-amber-400 to-yellow-300 px-6 py-4 flex items-center gap-3">
                    <i class="fas fa-lock text-white text-xl"></i>
                    <div>
                        <h2 class="text-white font-semibold">Kata Sandi</h2>
                        <p class="text-amber-50 text-sm">Jaga akun kamu tetap aman</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kata Sandi Saat Ini
                        </label>
                        <div class="flex items-center gap-3 border border-gray-200 rounded-lg px-4 py-3 bg-gray-50">
                            <span class="text-gray-600 text-lg tracking-wider">••••••••</span>
                            <span class="text-xs text-gray-400">Terakhir diubah 14 hari lalu</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <button id="openChangePassword" type="button" class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-all shadow-sm hover:shadow-md">
                        <i class="fas fa-key mr-2"></i>Ganti Kata Sandi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Password Modal --}}
    <div id="changePasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden px-4">
        <div class="bg-white rounded-lg w-full max-w-md p-8 shadow-2xl">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="bg-gradient-to-br from-amber-400 to-yellow-300 p-3 rounded-full">
                    <i class="fas fa-key text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Ganti Kata Sandi</h3>
            </div>

            <form id="changePasswordForm" action="{{ route('account.password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-gray-400 mr-2"></i>Kata Sandi Saat Ini
                    </label>
                    <input type="password" name="current_password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition"
                     placeholder="Masukkan kata sandi saat ini" required>
                    @error('current_password') 
                        <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock-open text-gray-400 mr-2"></i>Kata Sandi Baru
                    </label>
                    <input id="newPassword" type="password" name="password" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition" 
                        placeholder="Minimal 8 karakter" required>
                    @error('password') 
                        <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-check-circle text-gray-400 mr-2"></i>Konfirmasi Kata Sandi Baru
                    </label>
                    <input id="confirmPassword" type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition" 
                    placeholder="Konfirmasi kata sandi baru" required>
                    <div id="confirmError" class="text-red-600 text-sm mt-1 hidden flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>Kata sandi tidak cocok
                    </div>
                </div>

                <div class="flex gap-3 pt-6">
                    <button type="button" id="closeChangePassword" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold hover:from-red-600 hover:to-red-700 transition shadow-sm">
                        <i class="fas fa-check mr-2"></i>Simpan Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function(){
            const modal = document.getElementById('changePasswordModal');
            const openBtn = document.getElementById('openChangePassword');
            const closeBtn = document.getElementById('closeChangePassword');
            const form = document.getElementById('changePasswordForm');
            const newPass = document.getElementById('newPassword');
            const confirmPass = document.getElementById('confirmPassword');
            const confirmError = document.getElementById('confirmError');

            function openModal(){ modal.classList.remove('hidden'); }
            function closeModal(){ modal.classList.add('hidden'); confirmError.classList.add('hidden'); }

            openBtn && openBtn.addEventListener('click', openModal);
            closeBtn && closeBtn.addEventListener('click', closeModal);

            form && form.addEventListener('submit', function(e){
                if(newPass.value !== confirmPass.value){
                    e.preventDefault();
                    confirmError.classList.remove('hidden');
                }
            });

            // If server-side validation failed for password fields, open modal
            @if($errors->has('current_password') || $errors->has('password') || session('error'))
                openModal();
            @endif
        })();
    </script>
</div>
@endsection
