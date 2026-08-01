@extends('layouts.admin')

@section('title', 'Create Dummy User')
@section('page-title', 'Create Dummy User')

@section('content')
    <div class="card-png p-4" style="max-width: 640px;">
        <p class="text-muted small mb-4">
            Dummy users are activated immediately (no OTP/approval flow) but do <strong>not</strong> earn the
            8% monthly MPG benefit by default. You can enable it for this user at any time from their profile page.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store-dummy') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Sponsor Referral Code</label>
                <input type="text" name="sponsor_referral_code" class="form-control" value="{{ old('sponsor_referral_code') }}" placeholder="e.g. 482913" maxlength="6" inputmode="numeric" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Starting Investment (optional)</label>
                <input type="number" step="0.01" min="{{ config('mlm.minimum_investment') }}" name="starting_investment" class="form-control" value="{{ old('starting_investment') }}" placeholder="Leave blank for no investment">
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="roi_enabled" value="1" id="roiEnabled" {{ old('roi_enabled') ? 'checked' : '' }}>
                <label class="form-check-label small" for="roiEnabled">Enable 8% monthly MPG benefit for this user</label>
            </div>
            <button type="submit" class="btn btn-gold fw-bold px-4">Create Dummy User</button>
        </form>
    </div>
@endsection
