@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <div class="text-center mb-8">
        <div class="bg-purple-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-calendar-alt text-3xl text-white"></i>
        </div>
        <h2 class="text-3xl font-bold">Welcome Back!</h2>
        <p class="text-gray-600 mt-2">Silakan login ke akun Anda</p>
    </div>

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Email</label>
            <input type="email" name="email" value="admin@example.com" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Password</label>
            <input type="password" name="password" value="password" 
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600" required>
        </div>
        <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
            Login
        </button>
    </form>

    <div class="mt-6 p-4 bg-gray-100 rounded-lg">
        <p class="text-sm font-semibold mb-2">Akun Demo:</p>
        <p class="text-sm">Admin: admin@example.com / password</p>
        <p class="text-sm">User: user@example.com / password</p>
    </div>
</div>
@endsection