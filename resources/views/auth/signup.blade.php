@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <h4 class="fw-bold mb-1">Create your account</h4>
    <p class="text-muted small mb-4">Join PowerNetGlobal and start earning today.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('signup.submit') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-semibold">Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label small fw-semibold">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label small fw-semibold">Referral Number</label>
                <input type="text" name="referral_code" class="form-control" value="{{ old('referral_code', $referral) }}" placeholder="e.g. PNGABC123" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label small fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label small fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">Create Account</button>
    </form>

    <p class="text-center small text-muted mt-4 mb-0">
        Already have an account? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Login</a>
    </p>
@endsection
