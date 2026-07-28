@extends('layouts.app')

@section('title', 'Services — PowerNetGlobal')

@section('content')
    <section class="hero-png py-5">
        <div class="container py-5 text-center">
            <div class="eyebrow-gold mb-2">Our Services</div>
            <h1 class="display-6 fw-bold">What We Offer</h1>
            <p class="opacity-75 mt-2 col-lg-7 mx-auto">A full suite of investment management services built around one goal — protecting and growing investor capital.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-briefcase fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Portfolio Management</h5>
                        <p class="text-muted small mb-0">We design and actively manage diversified investment portfolios tailored for long-term wealth creation — balancing growth opportunities against capital protection across multiple asset classes and global markets.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-shuffle fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Alternative Investment Strategies</h5>
                        <p class="text-muted small mb-0">Beyond traditional markets, we deploy alternative investment strategies designed to capture opportunities less correlated with conventional equity and bond markets, strengthening diversification across the portfolio.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-search fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Global Market Research</h5>
                        <p class="text-muted small mb-0">Our research process continuously tracks global financial markets, macroeconomic trends, and emerging opportunities, so every investment decision is grounded in data rather than speculation.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-shield-check fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Risk Management</h5>
                        <p class="text-muted small mb-0">A systematic, disciplined risk framework governs every position we take — capital preservation comes first, with consistent long-term performance pursued within clearly defined risk limits.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-person-check fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Investment Advisory</h5>
                        <p class="text-muted small mb-0">Investors receive clear, ongoing guidance on portfolio performance, market conditions, and strategy — so you always understand where your capital is deployed and why.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-png p-4 h-100">
                        <i class="bi bi-gem fs-2 mb-3" style="color: var(--png-navy-700);"></i>
                        <h5 class="fw-bold">Wealth Creation Solutions</h5>
                        <p class="text-muted small mb-0">From your first investment onward, our structured programs are designed to compound value over time, with full transparency on performance tracked through your personal dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
