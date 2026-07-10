<nav class="bg-[#141E46] shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            {{-- Logo --}}
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center hover:opacity-80 transition">
                        <img src="{{ asset('images/logo-eventix.png') }}" alt="EventKu" class="h-10 w-auto">
                    </a>
                @elseif(auth()->user()->isPanitia())
                    <a href="{{ route('panitia.dashboard') }}" class="flex items-center hover:opacity-80 transition">
                        <img src="{{ asset('images/logo-eventix.png') }}" alt="EventKu" class="h-10 w-auto">
                    </a>
                @else
                    <a href="{{ route('home') }}" class="flex items-center hover:opacity-80 transition">
                        <img src="{{ asset('images/logo-eventix.png') }}" alt="EventKu" class="h-10 w-auto">
                    </a>
                @endif
            @else
                <a href="{{ route('home') }}" class="flex items-center hover:opacity-80 transition">
                    <img src="{{ asset('images/logo-eventix.png') }}" alt="EventKu" class="h-10 w-auto">
                </a>
            @endauth

            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center space-x-6">
                @auth
                    {{-- ADMIN MENU --}}
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-calendar-alt mr-1"></i> Kelola Event
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-credit-card mr-1"></i> Pembayaran
                        </a>
                        <a href="{{ route('admin.panitia.index') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-users mr-1"></i> Panitia
                        </a>
                        <a href="{{ route('admin.announcements.index') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-bullhorn mr-1"></i> Pengumuman
                        </a>
                    
                    {{-- PANITIA MENU --}}
                    @elseif(auth()->user()->isPanitia())
                        <a href="{{ route('panitia.dashboard') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dasbor
                        </a>
                        <a href="{{ route('panitia.events.index') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-calendar-alt mr-1"></i> Event Saya
                        </a>
                        <a href="{{ route('panitia.events.create') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-plus-circle mr-1"></i> Buat Event
                        </a>
                    
                    {{-- USER MENU --}}
                    @else
                        <a href="{{ route('home') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-home mr-1"></i> Beranda
                        </a>
                        <a href="{{ route('my.tickets') }}" class="text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-ticket-alt mr-1"></i> Tiket Saya
                        </a>
                        {{-- HAPUS LINK EVENT DI SINI --}}
                    @endif

                    {{-- Notification Bell --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-bell text-xl"></i>
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                                    {{ auth()->user()->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                            <div class="p-3 border-b font-semibold text-[#141E46]">Notifikasi</div>
                            <div class="divide-y">
                                @forelse(auth()->user()->notifications()->take(10)->get() as $notif)
                                <div class="p-3 hover:bg-gray-50 {{ $notif->is_read ? '' : 'bg-gray-100' }}">
                                    <p class="text-sm font-semibold">{{ $notif->title }}</p>
                                    <p class="text-xs text-black mt-1">{{ $notif->message }}</p>
                                    <div class="flex justify-between items-center mt-1">
                                        <p class="text-xs text-[#760031]">{{ $notif->created_at->diffForHumans() }}</p>
                                        @if(!$notif->is_read)
                                        <form action="{{ route('notifications.mark-read', $notif) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-[#760031] hover:text-[#760031]/80">Tandai dibaca</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="p-3 text-center text-gray-500 text-sm">Tidak ada notifikasi</div>
                                @endforelse
                            </div>
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                            <div class="p-2 border-t text-center">
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-[#760031] hover:text-[#760031]/80">
                                        Tandai semua sebagai sudah dibaca
                                    </button>
                                </form>
                            </div>
                            @endif
                            <div class="p-2 border-t text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-[#760031] hover:text-[#760031]/80">
                                    Lihat semua notifikasi
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-1 text-white hover:text-[#B6771D] transition">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
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
                            
                            {{-- Menu untuk Admin --}}
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Admin
                                </a>
                                <a href="{{ route('admin.events.index') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-calendar-alt mr-2"></i> Kelola Event
                                </a>
                                <a href="{{ route('admin.payments.index') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-credit-card mr-2"></i> Verifikasi Pembayaran
                                </a>
                                <a href="{{ route('admin.refunds.index') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-undo-alt mr-2"></i> Refund
                                </a>
                                <div class="border-t my-1"></div>
                            @endif
                            
                            {{-- Menu untuk Panitia --}}
                            @if(auth()->user()->isPanitia())
                                <a href="{{ route('panitia.dashboard') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Panitia
                                </a>
                                <div class="border-t my-1"></div>
                            @endif
                            
                            {{-- Menu untuk User Biasa --}}
                            @if(auth()->user()->isUser())
                                <a href="{{ route('account.show') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user-cog mr-2"></i> Akun Saya
                                </a>
                                <a href="{{ route('my.tickets') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-ticket-alt mr-2"></i> Tiket Saya
                                </a>
                                <div class="border-t my-1"></div>
                            @endif
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- TAMU --}}
                    <a href="{{ route('home') }}" class="text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>
                    <a href="{{ route('login') }}" class="text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-[#760031] text-white px-4 py-2 rounded-lg hover:bg-[#760031]/80 transition">
                        <i class="fas fa-user-plus mr-1"></i> Daftar
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Toggle --}}
            <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden md:hidden border-t border-[#B6771D]/30">
        <div class="container mx-auto px-4 py-4 space-y-3">
            @auth
                {{-- ADMIN MOBILE MENU --}}
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Admin
                    </a>
                    <a href="{{ route('admin.events.index') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-calendar-alt mr-2"></i> Kelola Event
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-credit-card mr-2"></i> Verifikasi Pembayaran
                    </a>
                    <a href="{{ route('admin.refunds.index') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-undo-alt mr-2"></i> Refund
                    </a>
                    <a href="{{ route('admin.panitia.index') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-users mr-2"></i> Panitia
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-bullhorn mr-2"></i> Pengumuman
                    </a>
                
                {{-- PANITIA MOBILE MENU --}}
                @elseif(auth()->user()->isPanitia())
                    <a href="{{ route('panitia.dashboard') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dasbor
                    </a>
                    <a href="{{ route('panitia.events.index') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-calendar-alt mr-2"></i> Event Saya
                    </a>
                    <a href="{{ route('panitia.events.create') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-plus-circle mr-2"></i> Buat Event
                    </a>
                
                {{-- USER MOBILE MENU --}}
                @else
                    <a href="{{ route('home') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-home mr-2"></i> Beranda
                    </a>
                    <a href="{{ route('my.tickets') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-ticket-alt mr-2"></i> Tiket Saya
                    </a>
                    {{-- HAPUS LINK EVENT DI MOBILE MENU --}}
                    <a href="{{ route('account.show') }}" class="block text-white hover:text-[#B6771D] transition">
                        <i class="fas fa-user-cog mr-2"></i> Akun Saya
                    </a>
                @endif
                
                <div class="border-t border-[#B6771D]/20 my-2"></div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="block text-red-400 hover:text-red-300 transition w-full text-left">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('home') }}" class="block text-white hover:text-[#B6771D] transition">
                    <i class="fas fa-home mr-2"></i> Beranda
                </a>
                <a href="{{ route('login') }}" class="block text-white hover:text-[#B6771D] transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </a>
                <a href="{{ route('register') }}" class="block text-white hover:text-[#B6771D]/80 transition">
                    <i class="fas fa-user-plus mr-2"></i> Daftar
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }

    // Tutup mobile menu saat klik di luar
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileMenu');
        const toggleButton = document.querySelector('.md\\:hidden');
        
        if (!menu.classList.contains('hidden') && toggleButton && !toggleButton.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
</script>