<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Event Management System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<<<<<<< HEAD
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <i class="fas fa-ticket-alt text-2xl text-purple-600"></i>
                    <span class="text-xl font-bold text-gray-800">EventKu</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-purple-600 transition">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-purple-600 transition">
                                <i class="fas fa-tachometer-alt mr-1"></i> Admin Panel
                            </a>
                        @elseif(auth()->user()->isPanitia())
                            <a href="{{ route('panitia.dashboard') }}" class="text-gray-600 hover:text-purple-600 transition">
                                <i class="fas fa-tachometer-alt mr-1"></i> Panel Panitia
                            </a>
                        @endif
                        
                        <a href="{{ route('my.tickets') }}" class="text-gray-600 hover:text-purple-600 transition">
                            <i class="fas fa-ticket-alt mr-1"></i> Tiket Saya
                        </a>
                        
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-1 text-gray-600 hover:text-purple-600 transition">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span>{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full 
                                        @if(auth()->user()->isAdmin()) bg-red-100 text-red-700
                                        @elseif(auth()->user()->isPanitia()) bg-blue-100 text-blue-700
                                        @else bg-green-100 text-green-700 @endif">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </div>
                                
                                <form action="{{ route('logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-purple-600 transition">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-user-plus mr-1"></i> Register
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-600" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="{{ route('home') }}" class="block text-gray-600 hover:text-purple-600">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block text-gray-600 hover:text-purple-600">
                            <i class="fas fa-tachometer-alt mr-2"></i> Admin Panel
                        </a>
                    @elseif(auth()->user()->isPanitia())
                        <a href="{{ route('panitia.dashboard') }}" class="block text-gray-600 hover:text-purple-600">
                            <i class="fas fa-tachometer-alt mr-2"></i> Panel Panitia
                        </a>
                    @endif
                    <a href="{{ route('my.tickets') }}" class="block text-gray-600 hover:text-purple-600">
                        <i class="fas fa-ticket-alt mr-2"></i> Tiket Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-gray-600 hover:text-purple-600">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="block text-purple-600 hover:text-purple-800">
                        <i class="fas fa-user-plus mr-2"></i> Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>
=======
<body class="bg-gray-100">
    <x-navbar />
>>>>>>> aac5c4ccf1602807fa0bd17e89aaff6196f326fc

    <main class="container mx-auto px-4 py-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <p>{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

<<<<<<< HEAD
    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center text-gray-600">
                <p>&copy; {{ date('Y') }} EventKu - Event Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
    
    @stack('scripts')
=======
    <x-footer />
>>>>>>> aac5c4ccf1602807fa0bd17e89aaff6196f326fc
</body>
</html>