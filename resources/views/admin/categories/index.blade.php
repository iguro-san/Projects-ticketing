@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-tags"></i> Daftar Kategori</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
        </div>
        <div class="card-body">
            @php
                $categories = session('categories', [
                    ['id' => 1, 'name' => 'Seminar', 'description' => 'Acara seminar dan presentasi'],
                    ['id' => 2, 'name' => 'Workshop', 'description' => 'Pelatihan praktis'],
                    ['id' => 3, 'name' => 'Expo', 'description' => 'Pameran produk dan teknologi'],
                    ['id' => 4, 'name' => 'Conference', 'description' => 'Konferensi besar'],
                ]);
            @endphp
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category['id'] }}</td>
                            <td><strong>{{ $category['name'] }}</strong></td>
                            <td>{{ $category['description'] ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editCategory({{ $category['id'] }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada kategori</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id) {
    alert('Edit kategori dengan ID: ' + id);
}
</script>
@endsection