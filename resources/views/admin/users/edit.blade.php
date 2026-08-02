@extends('layouts.admin')

@section('title', 'Edit ' . $user->name)
@section('page-title', 'Edit Member')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit {{ $user->name }}</h4>
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-navy">
            <i class="bi bi-arrow-left me-1"></i> Back to Member Detail
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

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label small fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}" required>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-semibold">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current" minlength="8">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-semibold">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Leave blank to keep current" minlength="8">
                </div>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="roi_enabled" id="roi_enabled" value="1" {{ old('roi_enabled', $user->roi_enabled) ? 'checked' : '' }}>
                <label class="form-check-label small" for="roi_enabled">
                    Enable 8% monthly MPG benefit for this user
                </label>
            </div>

            <button type="submit" class="btn btn-gold fw-bold px-4">Save Changes</button>
        </form>
    </div>
@endsection
