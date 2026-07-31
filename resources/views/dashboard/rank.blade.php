@extends('layouts.dashboard')

@section('title', 'Rank Progress')
@section('page-title', 'Rank Progress')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-label">Your Own Investment</div>
                <div class="stat-value">${{ number_format($ownInvest, 2) }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card gold">
                <div class="stat-label">Weighted Team Business</div>
                <div class="stat-value">${{ number_format($standardTeamBusiness, 2) }}</div>
                <small class="opacity-75">Power leg 50% / 2nd leg 30% / all other legs combined 20%</small>
            </div>
        </div>
    </div>

    @php $groups = $ranks->groupBy('package_group'); @endphp

    @foreach ($groups as $groupName => $groupRanks)
        <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing: 1px; color: var(--png-ink-500);">{{ $groupName }} Package</h6>
        <div class="row g-3 mb-4">
            @php $tierSizes = ['1rem', '1.3rem', '1.6rem']; @endphp
            @foreach ($groupRanks as $rank)
                @php
                    $tierIndex = $groupRanks->count() === 1 ? 3 : min($loop->iteration, 3);
                    $iconSize = $tierSizes[$tierIndex - 1];
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="rank-card p-4 @if($rank->is_achieved) achieved @elseif(!$rank->is_current && ($rank->invest_progress < 100 || $rank->team_progress < 100)) locked @endif @if($rank->is_current) current @endif">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rank-badge">{{ $rank->sort_order }}</div>
                            <div>
                                <div class="fw-bold d-flex align-items-center gap-1">
                                    {{ $rank->name }}
                                    @if ($rank->is_achieved)
                                        <i class="bi bi-check-circle-fill text-success" title="Rank Achieved"></i>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    @if ($rank->is_achieved)
                                        <span class="badge-group rank-achieved-pill {{ strtolower($rank->package_group) }}">
                                            <i class="bi {{ $rank->icon }}" style="font-size: {{ $iconSize }};"></i>Rank Achieved
                                        </span>
                                    @endif
                                    @if ($rank->is_current)
                                        <span class="badge bg-dark">Current Rank</span>
                                    @elseif (!$rank->is_achieved)
                                        <span class="badge bg-secondary">Locked</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="small text-muted mb-1">Own Investment</div>
                        <div class="progress-png mb-1"><div class="bar" style="width: {{ $rank->invest_progress }}%;"></div></div>
                        <div class="small mb-3">${{ number_format($ownInvest, 0) }} / ${{ number_format($rank->own_invest_required, 0) }}</div>

                        <div class="small text-muted mb-1">Team Business</div>
                        <div class="progress-png mb-1"><div class="bar" style="width: {{ $rank->team_progress }}%;"></div></div>
                        <div class="small mb-3">${{ number_format($rank->team_business_display, 0) }} / ${{ number_format($rank->team_business_required, 0) }}</div>

                        <hr>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Reward</span>
                            <span class="fw-bold">${{ number_format($rank->reward_amount, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Direct</span>
                            <span class="fw-semibold">{{ $rank->sort_order >= 5 ? '-' : ($rank->legs_open >= 255 ? 'No Limit' : $rank->legs_open) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Levels Unlocked</span>
                            <span class="fw-semibold">
                                @if ($rank->code === 'eagle')
                                    Achieved Eagle Rank All Levels opened
                                @elseif ($rank->sort_order >= 5)
                                    -
                                @else
                                    {{ $rank->levels_unlocked >= 20 ? 'All (20)' : $rank->levels_unlocked }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
