@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="bg-white rounded-lg shadow p-8 mb-8">
    {{-- POSTER EVENT (bisa diklik) --}}
    @if($event->poster)
    <div class="mb-6 cursor-pointer" onclick="openModal('{{ Storage::url($event->poster) }}')">
        <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" 
             class="w-full max-h-96 object-cover rounded-lg shadow-md hover:opacity-90 transition">
    </div>
    @else
    <div class="mb-6 w-full h-64 bg-gradient-to-r from-purple-500 to-blue-500 rounded-lg flex items-center justify-center">
        <i class="fas fa-calendar-alt text-5xl text-white opacity-50"></i>
    </div>
    @endif

    <div class="mb-4">
        <span class="bg-[#760031]/10 text-[#760031] px-3 py-1 rounded-full text-sm">{{ $event->category->name }}</span>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $event->title }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-gray-600">
        <p><i class="fas fa-calendar-alt text-[#760031] w-6"></i> {{ $event->event_date->format('l, d F Y') }}</p>
        <p><i class="fas fa-map-marker-alt text-[#760031] w-6"></i> {{ $event->location }}</p>
    </div>
    <div class="mb-6">
        <h3 class="font-bold text-lg mb-2">Deskripsi Event</h3>
        <p class="text-gray-600">{{ $event->description }}</p>
    </div>
</div>

{{-- Cek Suspension Status --}}
@if($event->suspension_status === 'pending')
<div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 rounded-r-lg">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    Event ini sedang dalam peninjauan oleh admin. Pendaftaran untuk sementara ditutup.
</div>
@elseif($event->suspension_status === 'cancelled')
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
    <i class="fas fa-ban mr-2"></i>
    Event ini telah dibatalkan.
</div>
@endif

<h2 class="text-2xl font-bold text-gray-800 mb-4">Pilih Tiket</h2>

@if($event->canRegister())
    @if(isset($ticketTypes) && count($ticketTypes) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($ticketTypes as $ticket)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $ticket->name }}</h3>
                <p class="text-3xl font-bold mt-2">
                    @if($ticket->price == 0)
                        <span class="text-green-600">GRATIS</span>
                    @else
                        <span class="text-purple-600">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                    @endif
                </p>
                <p class="text-gray-500 text-sm mt-2">
                    Sisa kuota: {{ $ticket->quota - $ticket->registered }}
                </p>
                
                @if(($ticket->quota - $ticket->registered) > 0)
                    @auth
                        <form action="{{ route('events.register', $event) }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="ticket_type_id" value="{{ $ticket->id }}">
                            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                                Daftar Sekarang
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-purple-600 text-white text-center py-2 rounded-lg hover:bg-purple-700 transition mt-4">
                            Login untuk Daftar
                        </a>
                    @endauth
                @else
<<<<<<< HEAD
                    <button class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed mt-4" disabled>
                        Tiket Habis
                    </button>
=======
                    <span class="text-[#760031]">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
>>>>>>> c6603cbe3ded401c6db0fb95458164972058d1a6
                @endif
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Belum ada tiket tersedia untuk event ini.</p>
        </div>
    @endif
@else
    <div class="bg-gray-100 rounded-lg p-8 text-center">
        <i class="fas fa-clock text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-600">Pendaftaran event sedang ditutup sementara.</p>
    </div>
@endif

@if(!auth()->check())
<div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
    <p class="text-yellow-700">Silakan <a href="{{ route('login') }}" class="font-bold underline">login</a> terlebih dahulu untuk mendaftar event.</p>
</div>
@endif

<!-- Modal Lightbox untuk melihat poster ukuran penuh -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeModal()">
    <div class="relative max-w-5xl max-h-screen p-4" onclick="event.stopPropagation()">
        <img id="modalImage" src="" alt="Poster Event" class="max-w-full max-h-screen object-contain rounded-lg shadow-2xl">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300 transition">
            <i class="fas fa-times-circle"></i>
        </button>
    </div>
</div>

<style>
    #imageModal {
        transition: all 0.3s ease;
    }
</style>

<script>
    function openModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modalImg.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endsection