@extends('layouts.app')

@section('title', 'Kelola Tiket')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jenis Tiket: {{ $event->title }}</h1>
        <button onclick="showModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus"></i> Tambah Tiket
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama Tiket</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Harga</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Kuota</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Terdaftar</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Sisa</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($ticketTypes as $ticket)
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $ticket->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $ticket->quota }}</td>
                    <td class="px-4 py-3">{{ $ticket->registered }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs {{ $ticket->remaining_quota > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $ticket->remaining_quota }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <!-- Tombol Edit -->
                        <button onclick="editTicket({{ $ticket->id }}, '{{ $ticket->name }}', {{ $ticket->price }}, {{ $ticket->quota }})" 
                                class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                            Edit
                        </button>
<<<<<<< HEAD
                        <form action="{{ route('admin.ticket-types.destroy', [$event, $ticket]) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
=======
                        <!-- Form Hapus -->
                        <form action="{{ route('admin.ticket-types.destroy', $ticket) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
>>>>>>> aac5c4ccf1602807fa0bd17e89aaff6196f326fc
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition" 
                                    onclick="return confirm('Yakin hapus tiket ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Tiket -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Tiket</h2>
        <form id="ticketForm" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nama Tiket" 
                   class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:border-purple-600" required>
            <input type="number" name="price" placeholder="Harga" 
                   class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:border-purple-600" required>
            <input type="number" name="quota" placeholder="Kuota" 
                   class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:border-purple-600" required>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="hideModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal() {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = 'Tambah Tiket';
    document.getElementById('ticketForm').action = "{{ route('admin.events.ticket-types.store', $event) }}";
    document.getElementById('ticketForm').method = "POST";
    // Hapus input _method jika ada (karena ini tambah, bukan edit)
    let methodInput = document.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    // Reset form
    document.querySelector('input[name="name"]').value = '';
    document.querySelector('input[name="price"]').value = '';
    document.querySelector('input[name="quota"]').value = '';
}

function editTicket(id, name, price, quota) {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = 'Edit Tiket';
    let form = document.getElementById('ticketForm');
    // Gunakan route update yang benar (tanpa event karena shallow)
    form.action = "{{ url('/admin/ticket-types') }}/" + id;
    // Hapus method input lama jika ada
    let oldMethod = document.querySelector('input[name="_method"]');
    if (oldMethod) oldMethod.remove();
    // Tambahkan input _method untuk PUT
    let methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    // Isi nilai form
    document.querySelector('input[name="name"]').value = name;
    document.querySelector('input[name="price"]').value = price;
    document.querySelector('input[name="quota"]').value = quota;
}

function hideModal() {
    document.getElementById('modal').classList.add('hidden');
}
</script>
@endsection