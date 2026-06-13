<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - EventKu')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        html, body {
            height: 100%;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex flex-col h-full">

<div x-data="{ sidebarOpen: false }" class="flex-1 flex flex-col lg:flex-row min-h-0">
    <!-- Mobile Navbar (hanya tampil di layar < lg) -->
    <div class="lg:hidden bg-white shadow-md sticky top-0 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <i class="fas fa-ticket-alt text-2xl text-purple-600"></i>
                <span class="text-xl font-bold text-gray-800">Admin EventKu</span>
            </a>
            <button @click="sidebarOpen = true" class="text-gray-600 hover:text-purple-600">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Overlay gelap untuk mobile -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- SIDEBAR - Full Height -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="sidebar-transition fixed top-0 left-0 z-50 w-64 h-full bg-white shadow-lg lg:relative lg:translate-x-0 lg:z-auto flex flex-col">
        <!-- Header Sidebar -->
        <div class="flex items-center justify-between p-4 border-b">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <i class="fas fa-ticket-alt text-2xl text-purple-600"></i>
                <span class="text-xl font-bold text-gray-800">Admin EventKu</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Profil User (Nama, Email, Role) -->
        <div class="p-4 border-b bg-purple-50">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-100 rounded-full p-2">
                    <i class="fas fa-user-circle text-2xl text-purple-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full bg-purple-200 text-purple-700">
                        <i class="fas fa-user-shield mr-1"></i> {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi (scrollable) -->
        <nav class="flex-1 overflow-y-auto mt-2">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.dashboard') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dashboard
            </a>
            <a href="{{ route('admin.events.index') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.events.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-calendar-alt w-5 mr-3"></i> Events
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.categories.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-tags w-5 mr-3"></i> Kategori
            </a>
            <a href="{{ route('admin.panitia.index') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.panitia.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-users w-5 mr-3"></i> Panitia
            </a>
            <a href="{{ route('admin.payments.index') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.payments.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-credit-card w-5 mr-3"></i> Pembayaran
            </a>
            <a href="{{ route('admin.refunds.index') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.refunds.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-undo-alt w-5 mr-3"></i> Refund
            </a>
            <a href="{{ route('admin.announcements.index') }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition {{ request()->routeIs('admin.announcements.*') ? 'bg-purple-50 text-purple-600' : '' }}">
                <i class="fas fa-bullhorn w-5 mr-3"></i> Pengumuman
            </a>
        </nav>

        <!-- Tombol Logout (di bagian bawah sidebar) -->
        <div class="border-t p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 rounded transition">
                    <i class="fas fa-sign-out-alt w-5 mr-3"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- AREA KONTEN UTAMA + FOOTER -->
    <main class="flex-1 flex flex-col min-h-0 bg-gray-100">
        <div class="flex-1 overflow-y-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>