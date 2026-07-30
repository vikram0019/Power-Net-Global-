@extends('layouts.auth')

@section('title', 'Verify OTP')

@section('content')
    <div class="text-center mb-4">
        <i class="bi bi-envelope-check display-5" style="color: var(--png-gold-500);"></i>
        <h4 class="fw-bold mt-2 mb-1">Verify your email</h4>
        <p class="text-muted small mb-0">Enter the 6-digit code sent to <strong>{{ $user->email }}</strong></p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
    @endif

    @if (session('dev_otp_hint'))
        <div class="alert alert-warning py-2 small">
            <i class="bi bi-terminal me-1"></i> Dev mode — your OTP is <strong>{{ session('dev_otp_hint') }}</strong> (email sending is stubbed locally).
        </div>
    @endif

    <form method="POST" action="{{ route('signup.otp.submit') }}">
        @csrf
        <div class="d-flex justify-content-center gap-2 otp-box mb-3">
            <input type="text" name="otp" maxlength="6" inputmode="numeric" class="form-control text-center fs-3 fw-bold" style="letter-spacing: 10px;" placeholder="------" autofocus required>
        </div>
        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">Verify &amp; Continue</button>
    </form>

    <form method="POST" action="{{ route('signup.otp.resend') }}" class="text-center mt-3">
        @csrf
        <button type="submit" class="btn btn-link btn-sm text-decoration-none">Didn't get the code? Resend OTP</button>
    </form>

    <p class="text-center small text-muted mt-2 mb-0">
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Back to login</a>
    </p>
@endsection
