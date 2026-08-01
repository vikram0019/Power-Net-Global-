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

    {{-- Headline offer --}}
    <section class="py-5">
        <div class="container">
            <div class="stats-band p-5 text-center">
                <div class="position-relative" style="color: #fff;">
                    <div class="eyebrow-gold mb-2">Monthly Profit Sharing</div>
                    <h2 class="display-5 fw-800 mb-2">{{ config('mlm.monthly_roi_percent') }}% Monthly — <span style="color: var(--png-gold-400);">2x in {{ config('mlm.roi_max_months') }} Months</span></h2>
                    <p class="opacity-75 col-lg-7 mx-auto mb-4">Every eligible investment earns {{ config('mlm.monthly_roi_percent') }}% every month for {{ config('mlm.roi_max_months') }} months — {{ config('mlm.monthly_roi_percent') * config('mlm.roi_max_months') }}% in total, doubling your original investment through monthly profit sharing alone.</p>
                    <div class="row g-3 justify-content-center">
                        <div class="col-4 col-md-3">
                            <div class="fs-3 fw-800" style="color: var(--png-gold-400);">{{ config('mlm.monthly_roi_percent') }}%</div>
                            <div class="small opacity-75 text-uppercase" style="letter-spacing: 0.5px;">Every Month</div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="fs-3 fw-800" style="color: var(--png-gold-400);">{{ config('mlm.roi_max_months') }}</div>
                            <div class="small opacity-75 text-uppercase" style="letter-spacing: 0.5px;">Months</div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="fs-3 fw-800" style="color: var(--png-gold-400);">2x</div>
                            <div class="small opacity-75 text-uppercase" style="letter-spacing: 0.5px;">Total Return</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Direct Income --}}
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Direct Income</div>
                <h2 class="section-title">Earn the moment your team invests</h2>
                <p class="text-muted col-lg-7 mx-auto">Direct income rewards the two people closest to a new investment — the sponsor who brought the member in, and that sponsor's own upline. It's paid instantly, no waiting for a payout cycle.</p>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-4">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-gold-500);">4%</div>
                        <h6 class="fw-bold">Direct Reward</h6>
                        <p class="small text-muted mb-0">Paid instantly to you when someone you personally referred makes an investment — a direct reward for bringing new members into the network.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-gold-500);">2%</div>
                        <h6 class="fw-bold">Upline Reward</h6>
                        <p class="small text-muted mb-0">Passed one level further up to your own sponsor — rewarding the mentor who helped build the team beneath them.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-png p-4 h-100">
                        <div class="fs-3 fw-800" style="color: var(--png-navy-800);">Example</div>
                        <h6 class="fw-bold">$500 investment</h6>
                        <p class="small text-muted mb-0">Your direct sponsor earns $20 (4%) instantly. Your sponsor's sponsor earns $10 (2%) instantly. Both post to their working wallet right away.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Level Income --}}
    <section class="py-5" style="background: #f6f7fb;">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Level Income</div>
                <h2 class="section-title">Income from up to 20 levels of your team</h2>
                <p class="text-muted col-lg-7 mx-auto">On top of direct income, a share of every investment is split across your team's first 20 levels — the deeper your team, the more of that pool you can reach.</p>
            </div>

            <div class="card-png p-4 mb-4">
                <h6 class="fw-bold mb-1">Level Income</h6>
                <p class="small text-muted mb-3">Figures below show each level's share of the level-income pool, not of the raw investment.</p>
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
            </div>

            <div class="card-png p-4">
                <h6 class="fw-bold mb-1">Levels Unlock as You Rank Up</h6>
                <p class="small text-muted mb-3">You only earn from levels your current rank has unlocked — the rest of the pool for deeper levels rolls up to the next qualifying upline.</p>
                <div class="table-responsive">
                    <table class="table table-png align-middle mb-0">
                        <thead><tr><th>Rank</th><th>Levels Unlocked</th></tr></thead>
                        <tbody>
                            <tr><td>Star</td><td>2 D - 1 level Open</td></tr>
                            <tr><td>Super Star</td><td>+1 D, 2 Level Open</td></tr>
                            <tr><td>Seven Star</td><td>+1 D, 3 Level Open</td></tr>
                            <tr><td>Eagle and above</td><td>Total 10 D, Achieved Eagle Rank, All Level are opened</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Rank & Rewards --}}
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Rank &amp; Rewards</div>
                <h2 class="section-title">13 Ranks Across 5 Packages</h2>
                <p class="text-muted col-lg-7 mx-auto">Ranks are earned through your own investment plus your team business, split into three independent targets: your single largest leg (Power) must clear 50% of the Team Business amount, your next largest (2nd) must clear 30%, and every other leg combined (Rest) must clear the remaining 20% — all three on their own. Every leg still earns its own normal direct, level, and profit-sharing income regardless.</p>
            </div>

            @foreach ($ranks as $groupName => $groupRanks)
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing: 1px; color: var(--png-ink-500);">{{ $groupName }}</h6>
                <div class="table-responsive mb-5">
                    <table class="table table-png align-middle" style="table-layout: fixed; width: 100%; min-width: 700px;">
                        <thead>
                            <tr>
                                <th style="width: 6%;">#</th>
                                <th style="width: 14%;">Rank</th>
                                <th style="width: 13%;">Own Investment</th>
                                <th style="width: 14%;">Team Business</th>
                                <th style="width: 12%;">Reward</th>
                                <th style="width: 12%;">Direct</th>
                                <th style="width: 29%;">Levels Unlocked</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupRanks as $rank)
                                <tr>
                                    <td><div class="rank-badge" style="width: 34px; height: 34px; font-size: 0.85rem;">{{ $rank->sort_order }}</div></td>
                                    <td class="fw-semibold">{{ $rank->name }}</td>
                                    <td>${{ number_format($rank->own_invest_required, 0) }}</td>
                                    <td>{{ $loop->first ? '' : '+' }}${{ number_format($rank->team_business_required, 0) }}</td>
                                    <td class="fw-bold" style="color: var(--png-gold-500);">${{ number_format($rank->reward_amount, 0) }}</td>
                                    <td>{{ $rank->sort_order >= 5 ? '-' : ($rank->legs_open >= 255 ? 'No Limit' : $rank->legs_open) }}</td>
                                    <td>
                                        @if ($rank->code === 'eagle')
                                            Achieved Eagle Rank All Levels opened
                                        @elseif ($rank->sort_order >= 5)
                                            -
                                        @else
                                            {{ $rank->levels_unlocked >= 20 ? 'All (20)' : $rank->levels_unlocked }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <li>A minimum investment of ${{ number_format(config('mlm.minimum_investment')) }} is required to be eligible for the monthly profit, level income, and rank income benefits.</li>
                    <li>Every member has a wallet system to submit deposits and track all earnings.</li>
                    <li>Team Business requires all three legs (Power/2nd/Rest) to independently clear their own share — a large Power leg can't make up for an empty Rest bucket.</li>
                </ul>
            </div>
        </div>
    </section>
@endsection
