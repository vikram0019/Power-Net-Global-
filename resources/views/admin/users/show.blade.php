@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', 'Member Detail')

@section('content')
    <a href="{{ route('admin.users.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3">
        <i class="bi bi-arrow-left"></i> Back to Users List
    </a>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
            <p class="text-muted small mb-0">{{ $user->email }} &middot; {{ $user->mobile }} &middot; Referral: <code>{{ $user->referral_code }}</code></p>
            <p class="small mb-1">Sponsor: {{ $user->sponsor->name ?? '—' }} &middot; Joined {{ $user->created_at->format('d M Y') }}</p>
            @include('partials.investor-status', ['user' => $user])
        </div>
        <div class="text-end">
            <span @class([
                'badge fs-6 mb-2',
                'bg-success' => $user->status === 'active',
                'bg-info text-dark' => $user->status === 'approval_pending',
                'bg-warning text-dark' => $user->status === 'pending',
                'bg-danger' => $user->status === 'suspended',
            ])>{{ str_replace('_', ' ', $user->status) }}</span>
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-navy">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @if ($user->status === 'approval_pending')
                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-gold">Approve</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.users.toggle-roi', $user) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $user->roi_enabled ? 'btn-gold' : 'btn-outline-secondary' }}">
                        Monthly MPG: {{ $user->roi_enabled ? 'On' : 'Off' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">Total Invested</div>
                <div class="stat-value">${{ number_format($totalInvested, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card gold">
                <div class="stat-label">Deposit / MPG / Working / Rank</div>
                <div class="stat-value" style="font-size:0.95rem;">
                    ${{ number_format($user->wallet->deposit_balance ?? 0, 0) }} / ${{ number_format($user->wallet->roi_balance ?? 0, 0) }} / ${{ number_format($user->wallet->working_balance ?? 0, 0) }} / ${{ number_format($user->wallet->rank_reward_balance ?? 0, 0) }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card green">
                <div class="stat-label">Team Size</div>
                <div class="stat-value">{{ $teamSize }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">Weighted Team Business</div>
                <div class="stat-value" style="font-size:1.1rem;">${{ number_format($teamBusiness, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3">Investments</h6>
                <div class="table-responsive">
                    <table class="table table-png align-middle">
                        <thead><tr><th>Date</th><th>Amount</th><th>MPG Paid</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($investments as $inv)
                                <tr>
                                    <td>{{ $inv->created_at->format('d M Y') }}</td>
                                    <td>${{ number_format($inv->amount, 2) }}</td>
                                    <td>{{ $inv->roi_months_paid }}/{{ config('mlm.roi_max_months') }} (${{ number_format($inv->roi_total_paid, 2) }})</td>
                                    <td><span class="badge {{ $inv->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $inv->status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No investments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-png p-4 h-100">
                <h6 class="fw-bold mb-3">Rank History</h6>
                <div class="table-responsive">
                    <table class="table table-png align-middle">
                        <thead><tr><th>Rank</th><th>Achieved</th><th>Reward</th></tr></thead>
                        <tbody>
                            @forelse ($rankHistory as $rh)
                                <tr>
                                    <td>{{ $rh->rank->name }}</td>
                                    <td>{{ $rh->achieved_at->format('d M Y') }}</td>
                                    <td>${{ number_format($rh->rank->reward_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No ranks achieved yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-png p-4 mt-3">
        <h6 class="fw-bold mb-3">Direct Referrals ({{ $user->directReferrals->count() }})</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead><tr><th>Name</th><th>Email</th><th>Rank</th><th>Joined</th></tr></thead>
                <tbody>
                    @forelse ($user->directReferrals as $ref)
                        <tr>
                            <td><a href="{{ route('admin.users.show', $ref) }}" class="text-decoration-none fw-semibold">{{ $ref->name }}</a></td>
                            <td>{{ $ref->email }}</td>
                            <td>{{ $ref->currentRank->name ?? 'Unranked' }}</td>
                            <td>{{ $ref->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No direct referrals yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
