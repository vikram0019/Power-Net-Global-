@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h4 class="fw-bold mb-1">Forgot your password?</h4>
    <p class="text-muted small mb-4">Enter your account email and we'll send you a link to reset it.</p>

    @if (session('status'))
        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">Send Reset Link</button>
    </form>

    <p class="text-center small text-muted mt-4 mb-0">
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Back to login</a>
    </p>
@endsection
