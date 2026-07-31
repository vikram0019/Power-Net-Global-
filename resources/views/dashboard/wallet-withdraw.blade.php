@extends('layouts.dashboard')

@section('title', 'Withdrawal')
@section('page-title', 'Wallet — Withdrawal')

@section('content')
    @if (session('pending_withdrawal_id'))
        <div class="card-png p-4 mb-4" style="max-width: 420px;">
            <h6 class="fw-bold mb-1"><i class="bi bi-shield-lock me-1"></i> Verify Withdrawal</h6>
            <p class="small text-muted mb-3">Enter the 6-digit code sent to your email to confirm this withdrawal.</p>

            @error('otp')
                <div class="alert alert-danger py-1 small">{{ $message }}</div>
            @enderror

            @if (session('dev_otp_hint'))
                <div class="alert alert-warning py-2 small">
                    <i class="bi bi-terminal me-1"></i> Dev mode — your OTP is <strong>{{ session('dev_otp_hint') }}</strong>.
                </div>
            @endif

            <form method="POST" action="{{ route('wallet.withdraw.verify-otp', session('pending_withdrawal_id')) }}"
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
                x-init="$nextTick(() => $refs.w0.focus())">
                @csrf
                <input type="hidden" name="otp" :value="otp">
                @include('partials.otp-digit-boxes', ['refPrefix' => 'w'])
                <button type="submit" class="btn btn-navy w-100">Verify Now</button>
            </form>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card gold">
                <div class="stat-label">ROI Income Wallet</div>
                <div class="stat-value">${{ number_format($wallet->roi_balance, 2) }}</div>
                <small class="opacity-75">Monthly profit earnings</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card green">
                <div class="stat-label">Working Income Wallet</div>
                <div class="stat-value">${{ number_format($wallet->working_balance, 2) }}</div>
                <small class="opacity-75">Direct &amp; level income</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card blue">
                <div class="stat-label">Rank &amp; Reward Wallet</div>
                <div class="stat-value">${{ number_format($wallet->rank_reward_balance, 2) }}</div>
                <small class="opacity-75">Rank achievement rewards</small>
            </div>
        </div>
    </div>

    <div class="alert alert-warning small mb-4" style="max-width: 520px;">
        <i class="bi bi-calendar-event me-1"></i>
        <strong>Withdrawal windows:</strong> ROI Income can only be withdrawn on the <strong>1st &amp; 2nd of every month</strong>.
        Working Income and Rank &amp; Reward Income can only be withdrawn on <strong>Sundays</strong>. No withdrawals are accepted on any other day.
    </div>

    <div class="card-png p-4 mb-4" style="max-width: 520px;"
        x-data="{
            balances: { roi: {{ (float) $wallet->roi_balance }}, working: {{ (float) $wallet->working_balance }}, rank_reward: {{ (float) $wallet->rank_reward_balance }} },
            windowOpen: { roi: {{ $roiWindowOpen ? 'true' : 'false' }}, working: {{ $weeklyWindowOpen ? 'true' : 'false' }}, rank_reward: {{ $weeklyWindowOpen ? 'true' : 'false' }} },
            walletType: {{ $roiWindowOpen ? "'roi'" : ($weeklyWindowOpen ? "'working'" : "'roi'") }},
            amount: '',
            get balance() { return this.balances[this.walletType]; },
            get windowClosed() { return !this.windowOpen[this.walletType]; },
            get insufficient() { return this.balance <= 0 || (this.amount !== '' && Number(this.amount) > this.balance); }
        }">
        <h6 class="fw-bold mb-3"><i class="bi bi-cash-stack me-1"></i> Withdraw</h6>
        <p class="small text-muted">Requires OTP verification, then admin approval. Funds are sent to the BEP20 address you provide below.</p>

        @error('amount')
            <div class="alert alert-danger py-1 small">{{ $message }}</div>
        @enderror

        @if (!$roiWindowOpen && !$weeklyWindowOpen)
            <div class="alert alert-danger py-2 small mb-3">Please come back on the 1st/2nd of the month (ROI) or on Every Sunday for (Working &amp; Rank/Reward).</div>
        @endif

        <form method="POST" action="{{ route('wallet.withdraw') }}" @submit="if (insufficient || windowClosed) $event.preventDefault()">
            @csrf
            <div class="mb-2">
                <label class="form-label small fw-semibold">Wallet</label>
                <select name="wallet_type" class="form-select" x-model="walletType" required>
                    <option value="roi" @if(!$roiWindowOpen) disabled @endif>ROI Income @if(!$roiWindowOpen)(closed today)@endif</option>
                    <option value="working" @if(!$weeklyWindowOpen) disabled @endif>Working Income @if(!$weeklyWindowOpen)(closed today)@endif</option>
                    <option value="rank_reward" @if(!$weeklyWindowOpen) disabled @endif>Rank &amp; Reward Income @if(!$weeklyWindowOpen)(closed today)@endif</option>
                </select>
                <div class="small mt-1">
                    Available balance: <strong>$<span x-text="balance.toFixed(2)"></span></strong>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Amount (USD)</label>
                <input type="number" step="0.01" min="1" name="amount" class="form-control" x-model="amount" required>
            </div>
            <div class="mb-2" x-show="windowClosed" x-cloak>
                <div class="alert alert-danger py-1 small mb-0">Check the withdrawal window above.</div>
            </div>
            <div class="mb-2" x-show="!windowClosed && insufficient" x-cloak>
                <div class="alert alert-danger py-1 small mb-0">Insufficient fund</div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Your BEP20 Address (to receive funds)</label>
                <input type="text" name="bep20_address" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-navy w-100" :disabled="balance <= 0 || windowClosed">Request Withdrawal</button>
        </form>
    </div>

    <a href="{{ route('wallet.withdrawal-requests') }}" class="btn btn-navy">
        <i class="bi bi-clock-history me-1"></i> View Recent Withdrawal Requests
    </a>
@endsection
