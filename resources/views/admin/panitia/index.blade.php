@extends('layouts.admin')

@section('title', 'Kelola Panitia')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-users mr-2 text-[#760031]"></i>Daftar Panitia
        </h1>
        <a href="{{ route('admin.panitia.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus mr-1"></i>Tambah Panitia
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Event Dibuat</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($panitia as $p)
                <tr>
                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $p->name }}</td>
                    <td class="px-4 py-3">{{ $p->email }}</td>
                    <td class="px-4 py-3 text-center">{{ $p->events_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('admin.panitia.destroy', $p) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus akun panitia ini? Event yang sudah dibuat tidak akan terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada panitia</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $panitia->links() }}</div>
</div>
@endsection