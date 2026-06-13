@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-bullhorn mr-2 text-purple-600"></i>Pengumuman
        </h1>
        <a href="{{ route('admin.announcements.create') }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus mr-1"></i>Buat Pengumuman
        </a>
    </div>

    @if($announcements->count() > 0)
    <div class="space-y-4">
        @foreach($announcements as $announcement)
        <div class="border rounded-lg p-4 hover:shadow transition {{ $announcement->is_active ? 'bg-white' : 'bg-gray-50' }}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="font-bold text-lg">{{ $announcement->title }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full 
                            @if($announcement->target == 'all') bg-purple-100 text-purple-700
                            @elseif($announcement->target == 'panitia') bg-blue-100 text-blue-700
                            @else bg-green-100 text-green-700 @endif">
                            <i class="fas fa-users mr-1"></i>{{ $announcement->target_label }}
                        </span>
                        @if(!$announcement->is_active)
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-500 rounded-full">
                                <i class="fas fa-eye-slash mr-1"></i>Tidak Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600">{{ $announcement->content }}</p>
                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                        <span><i class="fas fa-user mr-1"></i>{{ $announcement->creator->name }}</span>
                        <span><i class="fas fa-clock mr-1"></i>{{ $announcement->published_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="flex gap-2 ml-4">
                    <form action="{{ route('admin.announcements.toggle', $announcement) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm" 
                                title="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fas {{ $announcement->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        </button>
                    </form>
                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" 
                          onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $announcements->links() }}</div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-bullhorn text-5xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada pengumuman</p>
        <a href="{{ route('admin.announcements.create') }}" class="inline-block mt-3 text-purple-600 hover:text-purple-800">
            Buat pengumuman pertama
        </a>
    </div>
    @endif
</div>
@endsection