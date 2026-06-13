@props(['event'])

<div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
    @if($event->poster)
        <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
    @else
        <div class="w-full h-48 bg-gradient-to-r from-[#141E46] to-[#B6771D] flex items-center justify-center">
            <i class="fas fa-calendar-alt text-5xl text-white opacity-50"></i>
        </div>
    @endif
    <div class="p-4">
        <span class="text-xs bg-[#B6771D]/10 text-[#B6771D] px-2 py-1 rounded">{{ $event->category->name }}</span>
        <h3 class="text-xl font-bold text-[#141E46] mt-2">{{ $event->title }}</h3>
        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($event->description, 100) }}</p>
        <div class="mt-4 text-sm text-gray-500">
            <p><i class="fas fa-calendar text-[#B6771D]"></i> {{ $event->event_date->format('d F Y') }}</p>
            <p><i class="fas fa-map-marker-alt text-[#B6771D]"></i> {{ $event->location }}</p>
        </div>
        <a href="{{ route('events.show', $event) }}" class="block text-center bg-[#141E46] text-white py-2 rounded-lg mt-4 hover:bg-[#141E46]/80 transition">
            Lihat Detail
        </a>
    </div>
</div>