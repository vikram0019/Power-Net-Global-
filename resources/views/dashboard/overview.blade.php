@extends('layouts.dashboard')

@section('title', 'Overview')
@section('page-title', 'Overview')

@section('content')
    <div class="card-png p-4 mb-4" x-data="{ copiedCode: false, copiedLink: false }">
        <h6 class="fw-bold mb-3"><i class="bi bi-share me-1"></i> Your Referral</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Referral Code</label>
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ auth()->user()->referral_code }}" id="referralCode" readonly>
                    <button class="btn btn-navy" type="button"
                        @click="navigator.clipboard.writeText(document.getElementById('referralCode').value); copiedCode = true; setTimeout(() => copiedCode = false, 2000)">
                        <span x-show="!copiedCode"><i class="bi bi-clipboard"></i></span>
                        <span x-show="copiedCode"><i class="bi bi-check2"></i></span>
                    </button>
                </div>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Signup Link — share this so new members auto-fill your referral code</label>
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ route('signup', ['ref' => auth()->user()->referral_code]) }}" id="referralLink" readonly>
                    <button class="btn btn-gold fw-bold" type="button"
                        @click="navigator.clipboard.writeText(document.getElementById('referralLink').value); copiedLink = true; setTimeout(() => copiedLink = false, 2000)">
                        <span x-show="!copiedLink"><i class="bi bi-clipboard me-1"></i> Copy Link</span>
                        <span x-show="copiedLink"><i class="bi bi-check2 me-1"></i> Copied</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">Total Investment</div>
                <div class="stat-value">${{ number_format($totalInvested, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card gold">
                <div class="stat-label">Total Income</div>
                <div class="stat-value">${{ number_format($totalIncome, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card green">
                <div class="stat-label">Team Members</div>
                <div class="stat-value">{{ $teamSize }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">Direct Referrals</div>
                <div class="stat-value">{{ $directCount }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3">Income Trend (last 6 months)</h6>
                <canvas id="incomeChart" height="110"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3">Income Breakdown</h6>
                @php
                    $labels = ['direct_reward' => 'Direct Reward', 'level_income' => 'Level Income', 'monthly_roi' => 'Monthly ROI', 'rank_reward' => 'Rank Reward'];
                @endphp
                @forelse ($incomeByType as $type => $amount)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">{{ $labels[$type] ?? $type }}</span>
                        <span class="fw-semibold">${{ number_format($amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No income yet. Invest to start earning.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Recent Income</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Type</th><th>From</th><th>Level</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    @forelse ($recentIncome as $income)
                        <tr>
                            <td>{{ $income->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $income->type) }}</td>
                            <td>{{ $income->sourceUser->name ?? '—' }}</td>
                            <td>{{ $income->level ?? '—' }}</td>
                            <td class="fw-semibold">${{ number_format($income->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No income transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('incomeChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthly->keys()) !!},
            datasets: [{
                label: 'Income',
                data: {!! json_encode($monthly->values()) !!},
                borderColor: '#d4a94a',
                backgroundColor: 'rgba(212,169,74,0.15)',
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush
