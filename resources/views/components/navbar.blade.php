<!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('events.index') }}" class="text-2xl font-bold text-purple-600">
                <i class="fas fa-calendar-alt"></i> Event Management
            </a>
            <div class="space-x-4">
                <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-purple-600">Home</a>
                @auth
                    <a href="{{ route('my.tickets') }}" class="text-gray-700 hover:text-purple-600">Tiket Saya</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-purple-600">Dashboard</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600">Login</a>
                @endauth
            </div>
        </div>
    </nav>