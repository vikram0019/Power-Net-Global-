@extends('layouts.app')

@section('title', 'PowerNetGlobal — Grow Your Wealth Together')

@section('content')
    {{-- Hero --}}
    <section class="hero-png py-5">
        <div class="container py-5 position-relative">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="eyebrow-gold mb-3">Global Rewards &amp; Investment Network</div>
                    <h1 class="display-5 fw-800 mb-3" style="font-weight:800;">Build a global income stream with <span style="color: var(--png-gold-400);">PowerNetGlobal</span></h1>
                    <p class="fs-5 opacity-75 mb-4">Direct rewards, 20-level team income, monthly profit sharing, and a 13-rank achievement ladder — designed to reward you at every stage of your journey.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('signup') }}" class="btn btn-gold btn-lg px-4 fw-bold">Join Now</a>
                        <a href="{{ route('plan') }}" class="btn btn-outline-light btn-lg px-4">View Plan</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card-png p-4" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(6px); border-color: rgba(255,255,255,0.15);">
                        <h5 class="text-white fw-bold mb-3">Quick Login</h5>
                        @error('login')
                            <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>
                        @enderror
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="login" class="form-control" value="{{ old('login') }}" placeholder="Email or Mobile" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn btn-gold w-100 fw-bold">Login</button>
                        </form>
                        <p class="text-center small text-white-50 mt-3 mb-0">No account? <a href="{{ route('signup') }}" class="text-decoration-none" style="color: var(--png-gold-300);">Sign up free</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Summary About --}}
    <section class="py-5">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="eyebrow-gold mb-2">About Us</div>
                    <h2 class="section-title mb-3">A global network built on trust and transparency</h2>
                    <p class="text-muted">PowerNetGlobal connects ambitious members worldwide through a structured rewards network — combining direct referral bonuses, team-driven income, and long-term profit sharing, all tracked in a transparent wallet-based system.</p>
                    <p class="text-muted">Every member starts on the same footing: build your team, unlock deeper income levels as you rank up, and watch your rewards accumulate in a dashboard you can check anytime — no spreadsheets, no guesswork, just a clear real-time ledger of what you've earned.</p>
                    <a href="{{ route('about') }}" class="fw-semibold text-decoration-none">Learn more about us <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card-png p-4 text-center">
                                <i class="bi bi-shield-check fs-2" style="color: var(--png-gold-500);"></i>
                                <div class="fw-bold mt-2">Secure Wallet</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-png p-4 text-center">
                                <i class="bi bi-globe fs-2" style="color: var(--png-gold-500);"></i>
                                <div class="fw-bold mt-2">Global Access</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-png p-4 text-center">
                                <i class="bi bi-graph-up-arrow fs-2" style="color: var(--png-gold-500);"></i>
                                <div class="fw-bold mt-2">Growth Focused</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-png p-4 text-center">
                                <i class="bi bi-people fs-2" style="color: var(--png-gold-500);"></i>
                                <div class="fw-bold mt-2">Team Rewards</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-5" style="background: #f6f7fb;">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Why PowerNetGlobal</div>
                <h2 class="section-title">Built for members who play the long game</h2>
                <p class="text-muted col-lg-7 mx-auto">A platform designed around one idea — every dollar you or your team invests should be traceable, every reward should post automatically, and you should never have to wonder where you stand.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-lock"></i></div>
                        <h6 class="fw-bold">OTP-Verified Security</h6>
                        <p class="small text-muted mb-0">Account creation and withdrawals are protected by OTP verification, so your wallet stays in your hands alone.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon gold"><i class="bi bi-receipt"></i></div>
                        <h6 class="fw-bold">Fully Transparent Ledger</h6>
                        <p class="small text-muted mb-0">Every direct reward, level payout, and monthly profit share is logged with a timestamp — nothing is ever a mystery figure.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-diagram-3"></i></div>
                        <h6 class="fw-bold">20-Level Team Income</h6>
                        <p class="small text-muted mb-0">Your effort compounds — as your team grows deeper, unlocked ranks let you earn from up to 20 levels of activity.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon gold"><i class="bi bi-lightning-charge"></i></div>
                        <h6 class="fw-bold">Automated Payouts</h6>
                        <p class="small text-muted mb-0">Direct rewards and level income post the moment an investment is made — no manual processing, no waiting on admin.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-trophy"></i></div>
                        <h6 class="fw-bold">13 Rank & Rewards</h6>
                        <p class="small text-muted mb-0">A clear, published path from Start all the way to Universal Crown, with a reward at every milestone.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon gold"><i class="bi bi-headset"></i></div>
                        <h6 class="fw-bold">Real Support, Real People</h6>
                        <p class="small text-muted mb-0">Questions about your wallet, your team, or your next rank? Reach out and a real person gets back to you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Summary Service --}}
    <section class="py-5" style="background: #f6f7fb;">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Our Services</div>
                <h2 class="section-title">Everything you need to grow</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-wallet2 fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h6 class="fw-bold">Wallet System</h6>
                        <p class="small text-muted mb-0">Add funds, invest, and withdraw earnings with a secure OTP-verified wallet.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-diagram-3 fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h6 class="fw-bold">Team Building</h6>
                        <p class="small text-muted mb-0">Track your referral tree and team performance with real-time reports.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-cash-coin fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h6 class="fw-bold">Income Engine</h6>
                        <p class="small text-muted mb-0">Direct rewards, 20-level income, and monthly profit — all automated.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-trophy fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h6 class="fw-bold">Rank Rewards</h6>
                        <p class="small text-muted mb-0">Climb through 13 ranks across 5 packages and unlock bigger rewards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">How It Works</div>
                <h2 class="section-title">From sign-up to your first payout</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="step-card text-center px-2">
                        <div class="step-connector d-none d-lg-block"></div>
                        <div class="step-number mx-auto">1</div>
                        <h6 class="fw-bold">Create Your Account</h6>
                        <p class="small text-muted mb-0">Sign up with a referral code and verify with a one-time code — your account is active instantly.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="step-card text-center px-2">
                        <div class="step-connector d-none d-lg-block"></div>
                        <div class="step-number mx-auto">2</div>
                        <h6 class="fw-bold">Fund Your Wallet</h6>
                        <p class="small text-muted mb-0">Add funds and make your investment — the minimum to activate income is just $100.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="step-card text-center px-2">
                        <div class="step-connector d-none d-lg-block"></div>
                        <div class="step-number mx-auto">3</div>
                        <h6 class="fw-bold">Build Your Team</h6>
                        <p class="small text-muted mb-0">Share your referral link, grow your downline, and unlock deeper levels as you rank up.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="step-card text-center px-2">
                        <div class="step-number mx-auto">4</div>
                        <h6 class="fw-bold">Earn &amp; Withdraw</h6>
                        <p class="small text-muted mb-0">Watch direct, level, and monthly rewards post to your wallet, then withdraw whenever you're ready.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Summary Plan --}}
    <section class="py-5" style="background: #f6f7fb;">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Our Plan</div>
                <h2 class="section-title">A 13-rank journey to the top</h2>
                <p class="text-muted col-lg-7 mx-auto">Five packages, thirteen ranks — every step up unlocks a bigger cash reward, deeper team income, and more unlocked levels. From Start all the way to Universal Crown.</p>
            </div>
            <div class="row g-4">
                @foreach ($ranks->groupBy('package_group') as $groupName => $groupRanks)
                    @php $entryRank = $groupRanks->first(); @endphp
                    <div class="col-lg col-md-4 col-6">
                        <div class="rank-preview-card p-4 text-center h-100">
                            <span class="badge-group {{ strtolower($groupName) }} mb-3 d-inline-block">{{ $groupName }}</span>
                            <div class="rank-badge mx-auto mb-3">{{ $entryRank->sort_order }}</div>
                            <div class="fw-bold mb-1">{{ $entryRank->name }}</div>
                            <div class="fs-4 fw-800 mb-3" style="color: var(--png-gold-500);">${{ number_format($entryRank->reward_amount, 0) }}</div>
                            <div class="rank-preview-stat">
                                <span class="text-muted d-block">Own Invest</span>
                                <span class="fw-semibold">${{ number_format($entryRank->own_invest_required, 0) }}</span>
                            </div>
                            <div class="rank-preview-stat">
                                <span class="text-muted d-block">Team Business</span>
                                <span class="fw-semibold">${{ number_format($entryRank->cumulative_team_business_required, 0) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5 d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('plan') }}" class="btn btn-navy px-4">View Full Plan — All 13 Ranks</a>
                <a href="{{ asset('assets/docs/Power-Net-Global-Plan.pdf') }}" class="btn btn-gold px-4 fw-bold" download>
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Full Plan Download
                </a>
            </div>
        </div>
    </section>

    {{-- Summary Contact --}}
    <section class="py-5" style="background: var(--png-navy-950); color: #fff;">
        <div class="container py-5 text-center">
            <h2 class="fw-bold mb-3">Ready to start your journey?</h2>
            <p class="opacity-75 mb-4">Join PowerNetGlobal today and start building your income network.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('signup') }}" class="btn btn-gold btn-lg px-4 fw-bold">Join Now</a>
                <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-4">Contact Us</a>
            </div>
        </div>
    </section>
@endsection
