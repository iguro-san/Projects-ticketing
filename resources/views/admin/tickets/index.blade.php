@extends('layouts.app')

@section('title', 'Kelola Tiket')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-ticket-alt"></i> Jenis Tiket</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTicketModal">
                <i class="fas fa-plus"></i> Tambah Tiket
            </button>
        </div>
        <div class="card-body">
            @php
                $ticketTypes = session('ticket_types_' . $event['id'], [
                    ['id' => 1, 'name' => 'Regular', 'price' => 50000, 'quota' => 100, 'registered' => 45],
                    ['id' => 2, 'name' => 'VIP', 'price' => 150000, 'quota' => 50, 'registered' => 20],
                    ['id' => 3, 'name' => 'Early Bird', 'price' => 25000, 'quota' => 30, 'registered' => 30],
                ]);
            @endphp
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama Tiket</th>
                            <th>Harga</th>
                            <th>Kuota</th>
                            <th>Terdaftar</th>
                            <th>Sisa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ticketTypes as $ticket)
                        <tr>
                            <td>{{ $ticket['id'] }}</td>
                            <td><strong>{{ $ticket['name'] }}</strong></td>
                            <td>Rp {{ number_format($ticket['price'], 0, ',', '.') }}</td>
                            <td>{{ $ticket['quota'] }}</td>
                            <td>{{ $ticket['registered'] }}</td>
                            <td>
                                <span class="badge bg-{{ ($ticket['quota'] - $ticket['registered']) > 0 ? 'success' : 'danger' }}">
                                    {{ $ticket['quota'] - $ticket['registered'] }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editTicket({{ $ticket['id'] }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteTicket({{ $ticket['id'] }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada jenis tiket</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Tiket -->
<div class="modal fade" id="addTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.events.ticket-types.store', $event['id']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Tiket</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kuota</label>
                        <input type="number" name="quota" class="form-control" required>
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
function editTicket(id) {
    alert('Edit tiket ID: ' + id);
}
function deleteTicket(id) {
    if(confirm('Yakin hapus tiket ini?')) {
        alert('Tiket dihapus');
    }
}
</script>
@endsection