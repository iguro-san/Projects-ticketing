@extends('layouts.app')

@section('title', 'Buat Event Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Buat Event Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Event <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <option value="1">Seminar</option>
                                <option value="2">Workshop</option>
                                <option value="3">Expo</option>
                                <option value="4">Conference</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label">Tanggal Event <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="poster" class="form-label">Poster Event</label>
                            <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Event
                            </button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection