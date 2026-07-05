<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - EventKu')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .sidebar-transition { transition: transform 0.1s ease-in-out; }
        html, body { height: 100%; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex flex-col h-full">

<div x-data="{ sidebarOpen: false }" class="flex-1 flex flex-col lg:flex-row min-h-0">
    <!-- Mobile Navbar -->
    <div class="lg:hidden bg-[#141E46] shadow-md sticky top-0 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <i class="fas fa-ticket-alt text-2xl text-[#B6771D]"></i>
                <span class="text-xl font-bold text-[#B6771D]">Admin EventKu</span>
            </a>
            <button @click="sidebarOpen = true" class="text-white hover:text-[#B6771D]">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="sidebar-transition fixed top-0 left-0 z-50 w-64 h-full bg-[#141E46] shadow-lg lg:relative lg:translate-x-0 lg:z-auto flex flex-col">
        <div class="flex items-center justify-between p-4 border-b">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <i class="fas fa-ticket-alt text-2xl text-[#B6771D]"></i>
                <span class="text-xl font-bold text-[#B6771D]">Admin EventKu</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Profil User -->
        <div class="p-4 border-b">
            <div class="flex items-center space-x-3">
                <div class="bg-[#B6771D] rounded-full p-2">
                    <i class="fas fa-user-circle text-2xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-white mt-2 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-3 px-2 py-0.5 text-xs rounded-full bg-[#B6771D] text-white">
                        <i class="fas fa-user-shield mr-1"></i> {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto mt-2">
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.dashboard') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dasbor
    </a>
    <a href="{{ route('admin.events.index') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.events.*') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-calendar-alt w-5 mr-3"></i> Event
    </a>
    <a href="{{ route('admin.categories.index') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.categories.*') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-tags w-5 mr-3"></i> Kategori
    </a>
    <a href="{{ route('admin.panitia.index') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.panitia.*') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-users w-5 mr-3"></i> Panitia
    </a>
    <a href="{{ route('admin.payments.index') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.payments.*') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-credit-card w-5 mr-3"></i> Verifikasi Pembayaran
    </a>
    <a href="{{ route('admin.refunds.index') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.refunds.*') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-undo-alt w-5 mr-3"></i> Refund
    </a>
    <a href="{{ route('admin.announcements.index') }}"
       class="flex items-center px-4 py-3 text-white transition-all duration-200 
              hover:bg-[#B6771D]/20 hover:text-[#B6771D] 
              {{ request()->routeIs('admin.announcements.*') ? 'bg-[#B6771D]/30 border-l-4 border-[#B6771D] text-[#B6771D]' : '' }}">
        <i class="fas fa-bullhorn w-5 mr-3"></i> Pengumuman
    </a>
</nav>

        <div class="border-t p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2 text-red-500 hover:text-white hover:bg-red-500 rounded transition">
                    <i class="fas fa-sign-out-alt w-5 mr-3"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-h-0 bg-[#FFF5E0]/50">
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