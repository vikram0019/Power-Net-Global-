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
                    <div class="row mt-5 g-3">
                        <div class="col-4">
                            <div class="fs-3 fw-800" style="color: var(--png-gold-400);">{{ number_format($memberCount) }}+</div>
                            <div class="small opacity-75">Members Worldwide</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-800" style="color: var(--png-gold-400);">13</div>
                            <div class="small opacity-75">Achievement Ranks</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-800" style="color: var(--png-gold-400);">20</div>
                            <div class="small opacity-75">Income Levels</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card-png p-4" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(6px); border-color: rgba(255,255,255,0.15);">
                        <h5 class="text-white fw-bold mb-3">Quick Login</h5>
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="login" class="form-control" placeholder="Email or Mobile" required>
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

    {{-- Summary Plan --}}
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <div class="eyebrow-gold mb-2">Our Plan</div>
                <h2 class="section-title">A 13-rank journey to the top</h2>
                <p class="text-muted">From Start to Crown Ambassador Universal — every rank unlocks bigger rewards and deeper team income.</p>
            </div>
            <div class="row g-3">
                @foreach ($ranks->take(6) as $rank)
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="rank-card p-3 text-center">
                            <div class="rank-badge mx-auto mb-2">{{ $rank->sort_order }}</div>
                            <div class="fw-bold small">{{ $rank->name }}</div>
                            <div class="small text-muted">${{ number_format($rank->reward_amount, 0) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('plan') }}" class="btn btn-navy px-4">View Full Plan</a>
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
