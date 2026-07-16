@extends('layouts.app')

@section('title', 'Services — PowerNetGlobal')

@section('content')
    <section class="hero-png py-5">
        <div class="container py-5 text-center">
            <div class="eyebrow-gold mb-2">Our Services</div>
            <h1 class="display-6 fw-bold">What We Offer</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-wallet2 fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Wallet &amp; Investment</h5>
                        <p class="text-muted small mb-0">Add funds to your wallet and invest from just $100. Every dollar invested is tracked transparently, with income automatically distributed to your upline the moment you invest.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-cash-coin fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Direct Rewards</h5>
                        <p class="text-muted small mb-0">Earn 4% of your direct referral's investment instantly, plus 2% passed up to your own sponsor — rewarding both recruitment and mentorship.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-diagram-3 fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">20-Level Team Income</h5>
                        <p class="text-muted small mb-0">A 4.5% level-income pool distributed across 20 levels of your team — unlocked progressively as you climb the rank ladder.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-graph-up-arrow fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Monthly Profit Sharing</h5>
                        <p class="text-muted small mb-0">Every investment earns 8% monthly profit for 24 months, credited directly to your ROI wallet.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-trophy fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">13-Rank Achievement Ladder</h5>
                        <p class="text-muted small mb-0">From Start to Crown Ambassador Universal — climb through 5 packages, unlocking bigger cash rewards and deeper level income at every rank.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-shield-lock fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Secure Withdrawals</h5>
                        <p class="text-muted small mb-0">Withdraw from your ROI or Working income wallet with email OTP verification and admin approval for every payout.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
