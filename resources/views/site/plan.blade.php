@extends('layouts.app')

@section('title', 'Our Plan — PowerNetGlobal')

@section('content')
    <section class="hero-png py-5">
        <div class="container py-5 text-center">
            <div class="eyebrow-gold mb-2">Our Plan</div>
            <h1 class="display-6 fw-bold">Rewards, Income &amp; Rank System</h1>
            <p class="opacity-75 mt-2">Everything you earn, explained.</p>
        </div>
    </section>

    {{-- Income structure --}}
    <section class="py-5">
        <div class="container py-4">
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-gold-500);">4%</div>
                        <h6 class="fw-bold">Direct Reward</h6>
                        <p class="small text-muted mb-0">Of your direct referral's investment, paid instantly to you.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-gold-500);">2%</div>
                        <h6 class="fw-bold">Upline Reward</h6>
                        <p class="small text-muted mb-0">Passed up to your sponsor when your referral invests.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-gold-500);">4.5%</div>
                        <h6 class="fw-bold">Level Income Pool</h6>
                        <p class="small text-muted mb-0">Distributed across 20 levels of your team, unlocked by rank.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-gold-500);">8%</div>
                        <h6 class="fw-bold">Monthly Profit</h6>
                        <p class="small text-muted mb-0">Of your investment, paid monthly for {{ config('mlm.roi_max_months') }} months.</p>
                    </div>
                </div>
            </div>

            <div class="card-png p-4 mb-5">
                <h5 class="fw-bold mb-3">Level Income Distribution</h5>
                <p class="small text-muted">Each level's share of the 4.5% level-income pool. Levels unlock progressively as you climb the rank ladder.</p>
                <div class="table-responsive">
                    <table class="table table-png text-center align-middle">
                        <thead>
                            <tr>
                                @for ($i = 1; $i <= 10; $i++)<th>L{{ $i }}</th>@endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15%</td><td>10%</td><td>5%</td><td>3%</td><td>2%</td><td>1%</td><td>1%</td><td>1%</td><td>1%</td><td>1%</td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                @for ($i = 11; $i <= 20; $i++)<th>L{{ $i }}</th>@endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @for ($i = 11; $i <= 20; $i++)<td>0.5%</td>@endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="small text-muted mb-0">Figures shown are % of the 4.5% level-income pool.</p>
            </div>

            <div class="card-png p-4 mb-5">
                <h5 class="fw-bold mb-3">Team Legs Distribution (Rank Qualification)</h5>
                <p class="small text-muted mb-3">When qualifying for a rank, your team legs are weighted as follows: your single largest leg (Power), your next largest (2nd), and every other leg combined. All legs still earn normal direct, level, and ROI income regardless of this weighting.</p>
                <div class="row g-3 text-center">
                    <div class="col-4">
                        <div class="fs-4 fw-800" style="color: var(--png-navy-800);">50%</div>
                        <div class="small text-muted">Power Leg</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-800" style="color: var(--png-navy-800);">30%</div>
                        <div class="small text-muted">2nd Leg</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-800" style="color: var(--png-navy-800);">20%</div>
                        <div class="small text-muted">All Other Legs Combined</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Rank ladder --}}
    <section class="py-5" style="background: #f6f7fb;">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Rank Ladder</div>
                <h2 class="section-title">13 Ranks Across 5 Packages</h2>
            </div>

            @foreach ($ranks as $groupName => $groupRanks)
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing: 1px; color: var(--png-ink-500);">{{ $groupName }} Package</h6>
                <div class="row g-3 mb-5">
                    @foreach ($groupRanks as $rank)
                        <div class="col-lg-4 col-md-6">
                            <div class="rank-card p-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rank-badge">{{ $rank->sort_order }}</div>
                                    <div>
                                        <div class="fw-bold">{{ $rank->name }}</div>
                                        <span class="badge-group {{ strtolower($rank->package_group) }}">{{ $rank->package_group }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Own Investment</span>
                                    <span class="fw-semibold">${{ number_format($rank->own_invest_required, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Team Business</span>
                                    <span class="fw-semibold">${{ number_format($rank->team_business_required, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Reward</span>
                                    <span class="fw-bold" style="color: var(--png-gold-500);">${{ number_format($rank->reward_amount, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Legs Open</span>
                                    <span class="fw-semibold">{{ $rank->legs_open >= 255 ? 'No Limit' : $rank->legs_open }}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Levels Unlocked</span>
                                    <span class="fw-semibold">{{ $rank->levels_unlocked >= 20 ? 'All (20)' : $rank->levels_unlocked }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    <section class="py-5">
        <div class="container py-3">
            <div class="card-png p-4">
                <h6 class="fw-bold mb-2">Notes</h6>
                <ul class="small text-muted mb-0">
                    <li>Withdrawals are paid after admin approval and email OTP verification.</li>
                    <li>Monthly profit is 8% of the invested amount, paid monthly for up to {{ config('mlm.roi_max_months') }} months.</li>
                    <li>New accounts require admin approval before their referral code can be used by others.</li>
                    <li>A minimum investment of ${{ number_format(config('mlm.minimum_investment')) }} is required to be eligible for the monthly profit, level income, and rank income benefits.</li>
                    <li>Every member has a wallet system to submit deposits and track all earnings.</li>
                </ul>
            </div>
        </div>
    </section>
@endsection
