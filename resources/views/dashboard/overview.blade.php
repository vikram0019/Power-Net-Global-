@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @if ($achievedToday)
        <div class="rank-celebrate-overlay" x-data="{ open: true }" x-show="open" x-cloak @click.self="open = false">
            <div class="rank-celebrate-card">
                <div class="rank-confetti">
                    @for ($i = 0; $i < 30; $i++)
                        @php
                            $left = ($i * 37) % 100;
                            $delay = ($i % 10) * 0.15;
                            $duration = 2.5 + ($i % 5) * 0.3;
                            $colors = ['#d4a94a', '#ffd97a', '#8ea6ff', '#ff8fa3', '#6ee7b7'];
                            $color = $colors[$i % count($colors)];
                            $rotate = ($i * 47) % 360;
                        @endphp
                        <span style="left: {{ $left }}%; background: {{ $color }}; animation-delay: {{ $delay }}s; animation-duration: {{ $duration }}s; transform: rotate({{ $rotate }}deg);"></span>
                    @endfor
                </div>
                <button type="button" class="rank-celebrate-close" @click="open = false" aria-label="Close">&times;</button>
                <i class="bi bi-trophy-fill rank-celebrate-trophy"></i>
                <div class="rank-celebrate-name">{{ auth()->user()->name }}</div>
                <div class="rank-celebrate-msg">🎉 Congratulations! You achieved <strong>{{ $achievedToday->rank->name }}</strong>!</div>
                <button type="button" class="btn btn-gold fw-bold mt-3" @click="open = false">Awesome!</button>
            </div>
        </div>
    @endif

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
        <div class="col-md-3 col-6">
            <div class="stat-card gold">
                <div class="stat-label">MPG Income Wallet</div>
                <div class="stat-value">${{ number_format($wallet?->roi_balance ?? 0, 2) }}</div>
                <small class="opacity-75">Monthly Profit Growth</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card green">
                <div class="stat-label">Working Income Wallet</div>
                <div class="stat-value">${{ number_format($wallet?->working_balance ?? 0, 2) }}</div>
                <small class="opacity-75">Direct, level &amp; rank rewards</small>
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
            <div class="card-png p-4 h-100" x-data="{ open: false, selected: null }">
                <h6 class="fw-bold mb-3"><i class="bi bi-megaphone me-1"></i> Announcements</h6>

                @if ($announcements->isEmpty())
                    <p class="text-muted small mb-0">No announcements right now.</p>
                @else
                    <div class="announcement-ticker">
                        <div class="announcement-ticker-track" style="animation-duration: {{ max(10, $announcements->count() * 4) }}s;">
                            @foreach ($announcements->concat($announcements) as $a)
                                <div class="announcement-ticker-item">
                                    <span class="small fw-semibold text-truncate">{{ $a->title }}</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 flex-shrink-0"
                                        @click="open = true; selected = { title: @js($a->title), description: @js($a->description) }">
                                        Read More
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal" :class="{ 'd-block': open }" x-show="open" x-cloak tabindex="-1" style="background: rgba(5,11,24,0.6);" @click.self="open = false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title fw-bold" x-text="selected?.title"></h6>
                                    <button type="button" class="btn-close" @click="open = false"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0" x-text="selected?.description" style="white-space: pre-line;"></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-navy" @click="open = false">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
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
