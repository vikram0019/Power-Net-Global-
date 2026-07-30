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

    <form method="POST" action="{{ route('signup.otp.submit') }}"
        x-data="{
            digits: ['', '', '', '', '', ''],
            get otp() { return this.digits.join(''); },
            onInput(i, e, p) {
                const v = e.target.value.replace(/[^0-9]/g, '').slice(-1);
                this.digits[i] = v;
                e.target.value = v;
                if (v && i < 5) this.$refs[p + (i + 1)].focus();
            },
            onKeydown(i, e, p) {
                if (e.key === 'Backspace' && e.target.value === '' && i > 0) {
                    this.$refs[p + (i - 1)].focus();
                }
            },
            onPaste(e, p) {
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                if (!text) return;
                e.preventDefault();
                for (let i = 0; i < 6; i++) {
                    this.digits[i] = text[i] || '';
                    if (this.$refs[p + i]) this.$refs[p + i].value = text[i] || '';
                }
                const last = Math.min(text.length, 6) - 1;
                if (last >= 0 && this.$refs[p + last]) this.$refs[p + last].focus();
            }
        }"
        x-init="$nextTick(() => $refs.s0.focus())">
        @csrf
        <input type="hidden" name="otp" :value="otp">
        @include('partials.otp-digit-boxes', ['refPrefix' => 's'])
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
