@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <h4 class="fw-bold mb-1">Reset your password</h4>
    <p class="text-muted small mb-4">Choose a new password for your account.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">New Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
        </div>
        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">Reset Password</button>
    </form>

    <p class="text-center small text-muted mt-4 mb-0">
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Back to login</a>
    </p>
@endsection
