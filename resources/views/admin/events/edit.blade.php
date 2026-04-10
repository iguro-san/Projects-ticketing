@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Event</h4>
                </div>
                <div class="card-body">
                    @php
                        $events = session('events', []);
                        $event = collect($events)->firstWhere('id', $id);
                        if (!$event) {
                            $event = ['id' => $id, 'title' => '', 'category_id' => '', 'description' => '', 'event_date' => '', 'location' => ''];
                        }
                    @endphp
                    <form action="{{ route('admin.events.update', $id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Event</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $event['title'] }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="1" {{ $event['category_id'] == 1 ? 'selected' : '' }}>Seminar</option>
                                <option value="2" {{ $event['category_id'] == 2 ? 'selected' : '' }}>Workshop</option>
                                <option value="3" {{ $event['category_id'] == 3 ? 'selected' : '' }}>Expo</option>
                                <option value="4" {{ $event['category_id'] == 4 ? 'selected' : '' }}>Conference</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required>{{ $event['description'] }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label">Tanggal Event</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" value="{{ $event['event_date'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ $event['location'] }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="poster" class="form-label">Poster Event</label>
                            <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Event</button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection