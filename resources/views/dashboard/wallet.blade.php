@extends('layouts.dashboard')

@section('title', 'Wallet')
@section('page-title', 'Wallet')

@section('content')
    @if (session('dev_otp_hint'))
        <div class="alert alert-warning py-2 small">
            <i class="bi bi-terminal me-1"></i> Dev mode — withdrawal OTP is <strong>{{ session('dev_otp_hint') }}</strong>.
            @if (session('pending_withdrawal_id'))
                <form method="POST" action="{{ route('wallet.withdraw.verify-otp', session('pending_withdrawal_id')) }}" class="d-inline-flex gap-2 mt-2">
                    @csrf
                    <input type="text" name="otp" maxlength="6" class="form-control form-control-sm" style="width: 120px;" placeholder="OTP" required>
                    <button type="submit" class="btn btn-sm btn-navy">Verify Now</button>
                </form>
            @endif
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Deposit Wallet</div>
                <div class="stat-value">${{ number_format($wallet->deposit_balance, 2) }}</div>
                <small class="opacity-75">Used to purchase investments</small>
            </div>
        </div>
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
                <small class="opacity-75">Direct, level &amp; rank rewards</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1"></i> Add Fund</h6>
                <p class="small text-muted">Payment gateway is a demo/mock — funds are credited instantly.</p>
                <form method="POST" action="{{ route('wallet.add-fund') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Amount (USD)</label>
                        <input type="number" step="0.01" min="1" name="amount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-navy w-100">Add Fund</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-1"></i> Invest</h6>
                <p class="small text-muted">Minimum investment ${{ number_format($minimumInvestment) }}. Deducted from your deposit wallet.</p>
                @error('amount')
                    <div class="alert alert-danger py-1 small">{{ $message }}</div>
                @enderror
                <form method="POST" action="{{ route('wallet.invest') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Amount (USD)</label>
                        <input type="number" step="0.01" min="{{ $minimumInvestment }}" name="amount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 fw-bold">Invest Now</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-cash-stack me-1"></i> Withdraw</h6>
                <p class="small text-muted">Requires OTP verification, then admin approval.</p>
                @error('otp')
                    <div class="alert alert-danger py-1 small">{{ $message }}</div>
                @enderror
                <form method="POST" action="{{ route('wallet.withdraw') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Wallet</label>
                        <select name="wallet_type" class="form-select" required>
                            <option value="roi">ROI Income</option>
                            <option value="working">Working Income</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Amount (USD)</label>
                        <input type="number" step="0.01" min="1" name="amount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-navy w-100">Request Withdrawal</button>
                </form>
            </div>
        </div>
    </div>

    @if ($withdrawals->count())
        <div class="card-png p-4 mb-4">
            <h6 class="fw-bold mb-3">Recent Withdrawal Requests</h6>
            <div class="table-responsive">
                <table class="table table-png align-middle">
                    <thead>
                        <tr><th>Date</th><th>Wallet</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($withdrawals as $w)
                            <tr>
                                <td>{{ $w->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-capitalize">{{ $w->wallet_type }}</td>
                                <td>${{ number_format($w->amount, 2) }}</td>
                                <td>
                                    <span class="badge
                                        @class([
                                            'bg-warning text-dark' => $w->status === 'pending',
                                            'bg-info text-dark' => $w->status === 'otp_verified',
                                            'bg-primary' => $w->status === 'approved',
                                            'bg-danger' => $w->status === 'rejected',
                                            'bg-success' => $w->status === 'paid',
                                        ])">{{ str_replace('_', ' ', $w->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Payment History</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Wallet</th><th>Direction</th><th>Amount</th><th>Balance After</th><th>Description</th></tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-capitalize">{{ $t->wallet_type }}</td>
                            <td>
                                <span class="badge {{ $t->direction === 'credit' ? 'bg-success' : 'bg-secondary' }}">{{ $t->direction }}</span>
                            </td>
                            <td>${{ number_format($t->amount, 2) }}</td>
                            <td>${{ number_format($t->balance_after, 2) }}</td>
                            <td class="small text-muted">{{ $t->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
