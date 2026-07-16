@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h4 class="fw-bold mb-1">Welcome back</h4>
    <p class="text-muted small mb-4">Login to access your dashboard.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-semibold">Email or Mobile</label>
            <input type="text" name="login" class="form-control" value="{{ old('login') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">Login</button>
    </form>

    <p class="text-center small text-muted mt-4 mb-0">
        New to PowerNetGlobal? <a href="{{ route('signup') }}" class="fw-semibold text-decoration-none">Create an account</a>
    </p>
@endsection
