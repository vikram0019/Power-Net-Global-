@extends('layouts.admin')

@section('title', 'New Announcement')
@section('page-title', 'New Announcement')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">New Announcement</h4>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-navy">
            <i class="bi bi-arrow-left me-1"></i> Back to Announcements
        </a>
    </div>

    <div class="card-png p-4" style="max-width: 560px;">
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Expiry Date</label>
                <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                <div class="small text-muted mt-1">Leave blank for an announcement that never expires.</div>
            </div>

            <button type="submit" class="btn btn-gold fw-bold px-4">Publish Announcement</button>
        </form>
    </div>
@endsection
