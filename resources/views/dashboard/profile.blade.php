@extends('layouts.dashboard')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="card-png p-4" style="max-width: 480px;">
        <h6 class="fw-bold mb-3">Profile Image</h6>

        <div class="text-center mb-3">
            @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
            @else
                <div class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center" style="width:120px;height:120px;">
                    <i class="bi bi-person fs-1 text-muted"></i>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('profile.upload-image') }}" enctype="multipart/form-data">
            @csrf
            <label class="form-label small fw-semibold">Upload New Image</label>
            <input type="file" name="profile_image" accept="image/png,image/jpeg" class="form-control mb-3" required>
            <p class="text-muted small mb-3">This image is shown only in your rank-achievement celebration popup. JPG or PNG, max 5MB.</p>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-gold fw-bold">Save</button>
                @if ($user->profile_image)
                    <button type="submit" form="removeProfileImageForm" class="btn btn-outline-danger fw-bold">Remove</button>
                @endif
            </div>
        </form>
        @if ($user->profile_image)
            <form method="POST" action="{{ route('profile.remove-image') }}" id="removeProfileImageForm" data-confirm-title="Remove Profile Image" data-confirm="Remove your profile image?">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection
