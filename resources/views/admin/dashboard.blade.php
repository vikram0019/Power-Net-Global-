@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">Total Members</div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <small class="opacity-75">{{ $activeUsers }} active</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card gold">
                <div class="stat-label">Total Invested</div>
                <div class="stat-value">${{ number_format($totalInvested, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card green">
                <div class="stat-label">Total Wallet Balance</div>
                <div class="stat-value">${{ number_format($totalWalletBalance, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">Pending Withdrawals</div>
                <div class="stat-value">{{ $pendingWithdrawals }}</div>
                <small class="opacity-75">${{ number_format($pendingWithdrawalAmount, 2) }}</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3">Signups (last 14 days)</h6>
                <canvas id="signupsChart" height="110"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3">Withdrawals Awaiting Approval</h6>
                @forelse ($recentWithdrawals as $w)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <div class="fw-semibold small">{{ $w->user->name }}</div>
                            <div class="text-muted small text-capitalize">{{ $w->wallet_type }} wallet</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small">${{ number_format($w->amount, 2) }}</div>
                            <a href="{{ route('admin.withdrawals.index') }}" class="small">Review</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No withdrawals awaiting approval.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Recently Joined Members</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Referral Code</th><th>Status</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    @forelse ($recentUsers as $u)
                        <tr>
                            <td><a href="{{ route('admin.users.show', $u) }}" class="text-decoration-none fw-semibold">{{ $u->name }}</a></td>
                            <td>{{ $u->email }}</td>
                            <td><code>{{ $u->referral_code }}</code></td>
                            <td><span class="badge {{ $u->status === 'active' ? 'bg-success' : ($u->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $u->status }}</span></td>
                            <td>{{ $u->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No members yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('signupsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($signupsByDay->keys()) !!},
            datasets: [{
                label: 'Signups',
                data: {!! json_encode($signupsByDay->values()) !!},
                backgroundColor: '#16294d',
                borderRadius: 4,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
</script>
@endpush
