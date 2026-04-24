@extends('layouts.app')

@section('title', 'Kelola Event Saya')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Event Saya</h1>
        <a href="{{ route('panitia.events.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus"></i> Buat Event Baru
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Judul</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Kategori</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Peserta</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $event->title }}</td>
                    <td class="px-4 py-3">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $event->event_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $event->registrations_count }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs
                            @if($event->status == 'active') bg-green-100 text-green-700
                            @elseif($event->status == 'draft') bg-gray-100 text-gray-700
                            @elseif($event->status == 'completed') bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('panitia.events.registrations', $event) }}" 
                               class="bg-green-500 text-white px-2 py-1 rounded text-sm hover:bg-green-600 transition" title="Lihat Peserta">
                                <i class="fas fa-users"></i>
                            </a>
                            <a href="{{ route('panitia.events.edit', $event) }}" 
                               class="bg-yellow-500 text-white px-2 py-1 rounded text-sm hover:bg-yellow-600 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        Belum ada event. <a href="{{ route('panitia.events.create') }}" class="text-purple-600">Buat event pertama!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection