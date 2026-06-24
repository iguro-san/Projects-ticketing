@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Manajemen Akun</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Profile Form --}}
        <div class="col-span-1 md:col-span-1">
            <h2 class="text-lg font-medium mb-2">Profil</h2>
            <form action="{{ route('account.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border rounded px-3 py-2" required>
                    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border rounded px-3 py-2" required>
                    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full border rounded px-3 py-2">
                    @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Role</label>
                    <input type="text" value="{{ $user->role }}" disabled class="mt-1 block w-full border rounded px-3 py-2 bg-gray-100">
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Password Form --}}
        <div class="col-span-1 md:col-span-1">
            <h2 class="text-lg font-medium mb-2">Ganti Password</h2>
            <form action="{{ route('account.password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Password Saat Ini</label>
                    <input type="password" name="current_password" class="mt-1 block w-full border rounded px-3 py-2" required>
                    @error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Password Baru</label>
                    <input type="password" name="password" class="mt-1 block w-full border rounded px-3 py-2" required>
                    @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
