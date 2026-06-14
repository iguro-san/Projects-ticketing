@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        </div>
        <div class="divide-y">
            @forelse($notifications as $notif)
            <div class="p-4 {{ $notif->is_read ? '' : 'bg-gray-100' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold">{{ $notif->title }}</p>
                        <p class="text-sm text-black mt-2">{{ $notif->message }}</p>
                        <p class="text-xs text-[#760031] mt-5">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notif->is_read)
                    <form action="{{ route('notifications.mark-read', $notif) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-[#760031] hover:text-[#760031]/80">Tandai dibaca</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-bell-slash text-5xl mb-3"></i>
                <p>Tidak ada notifikasi</p>
            </div>
            @endforelse
        </div>
        <div class="p-4 border-t">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection