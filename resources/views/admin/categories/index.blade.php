@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#760031]">
            <i class="fas fa-tags w-5 mr-3"></i>Daftar Kategori
        </h1>
        <button onclick="showModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($categories as $cat)
                <tr>
                    <td class="px-4 py-3">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-gray-600 break-words max-w-md">{{ $cat->description ?? '-' }}</td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <button onclick="editCategory({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->description }}')" 
                                class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                            Edit
                        </button>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition" 
                                    onclick="return confirm('Yakin hapus kategori ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $categories->onEachSide(1)->links() }}
    </div>
</div>

<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Kategori</h2>
        <form id="categoryForm" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nama Kategori" 
                   class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:border-[#760031]" required>
            <textarea name="description" placeholder="Deskripsi (opsional)" 
                      class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:border-[#760031]" rows="3"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="hideModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#760031] text-white rounded-lg hover:bg-[#760031]/80 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showModal() {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = 'Tambah Kategori';
    document.getElementById('categoryForm').action = "{{ route('admin.categories.store') }}";
    document.getElementById('categoryForm').method = "POST";
    document.querySelector('input[name="name"]').value = '';
    document.querySelector('textarea[name="description"]').value = '';
    let methodInput = document.querySelector('input[name="_method"]');
    if(methodInput) methodInput.remove();
}

function editCategory(id, name, desc) {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = 'Edit Kategori';
    let form = document.getElementById('categoryForm');
    form.action = `/admin/categories/${id}`;
    let method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'PUT';
    form.appendChild(method);
    document.querySelector('input[name="name"]').value = name;
    document.querySelector('textarea[name="description"]').value = desc || '';
}

function hideModal() {
    document.getElementById('modal').classList.add('hidden');
}

@if($errors->any())
    @foreach($errors->all() as $error)
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ $error }}',
            confirmButtonColor: '#d33'
        });
    @endforeach
@endif

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33'
    });
@endif
</script>
@endsection