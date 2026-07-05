@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Manajemen Akun</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Profile Display (read-only) --}}
        <div class="col-span-1 md:col-span-1">
            <h2 class="text-lg font-medium mb-2">Profil</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Nama</label>
                    <input type="text" value="{{ $user->name }}" readonly class="mt-1 block w-full border rounded px-3 py-2 bg-gray-50 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" value="{{ $user->email }}" readonly class="mt-1 block w-full border rounded px-3 py-2 bg-gray-50 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium">Nomor Telepon</label>
                    <input type="text" value="{{ $user->phone ?? '-' }}" readonly 
                        class="mt-1 block w-full border rounded px-3 py-2 bg-gray-50 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium">Role</label>
                    <input type="text" value="{{ $user->role }}" readonly class="mt-1 block w-full border rounded px-3 py-2 bg-gray-50 cursor-not-allowed">
                </div>
            </div>
        </div>

        {{-- Password display + trigger modal --}}
        <div class="col-span-1 md:col-span-1">
            <h2 class="text-lg font-medium mb-2">Password</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Kata Sandi</label>
                    <div class="mt-1 flex items-center justify-between border rounded px-3 py-2 bg-gray-50">
                        <span class="text-sm text-gray-700">••••••••</span>
                        <button id="openChangePassword" type="button" class="ml-4 bg-red-600 text-white px-3 py-1 rounded">Ganti Kata Sandi</button>
                    </div>
                </div>

                @if(session('success'))
                    <p class="text-green-600 text-sm">{{ session('success') }}</p>
                @endif
                @if(session('error'))
                    <p class="text-red-600 text-sm">{{ session('error') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Change Password Modal --}}
    <div id="changePasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <h3 class="text-lg font-medium mb-4">Ganti Kata Sandi</h3>
            <form id="changePasswordForm" action="{{ route('account.password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="mt-1 block w-full border rounded px-3 py-2" 
                     placeholder="Masukkan kata sandi saat ini" required>
                    @error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Kata Sandi Baru</label>
                    <input id="newPassword" type="password" name="password" 
                        class="mt-1 block w-full border rounded px-3 py-2" 
                        placeholder="Minimal 6 karakter" required>
                    @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Konfirmasi Kata Sandi Baru</label>
                    <input id="confirmPassword" type="password" name="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2" 
                    placeholder="Konfirmasi kata sandi baru" required>
                    <p id="confirmError" class="text-red-600 text-sm mt-1 hidden">Konfirmasi tidak cocok.</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeChangePassword" class="px-4 py-2 rounded border">Batal</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
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
