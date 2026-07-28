@extends('layouts.app')

@section('title', 'About Us — PowerNetGlobal')

@section('content')
    <section class="hero-png py-5">
        <div class="container py-5 text-center">
            <div class="eyebrow-gold mb-2">About Us</div>
            <h1 class="display-6 fw-bold">Who We Are</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-9 text-center">
                    <p class="fs-5 text-muted">Power Net Global Hedge Fund is a professional investment management company dedicated to creating long-term wealth through disciplined investment strategies, advanced market research, and strong risk management.</p>
                    <p class="fs-5 text-muted mb-0">Our objective is to identify high-quality investment opportunities across global financial markets while protecting investor capital through a diversified portfolio and a systematic investment approach.</p>
                </div>
            </div>

            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="eyebrow-gold mb-2">Our Vision</div>
                    <h2 class="section-title mb-3">A globally trusted investment partner</h2>
                    <p class="text-muted">To become a globally trusted investment management company by delivering consistent, transparent, and sustainable investment performance.</p>
                </div>
                <div class="col-lg-6">
                    <div class="eyebrow-gold mb-2">Our Mission</div>
                    <h2 class="section-title mb-3">Principles we invest by</h2>
                    <ul class="text-muted mb-0 ps-3">
                        <li class="mb-2">Deliver long-term value for investors.</li>
                        <li class="mb-2">Maintain the highest standards of integrity and transparency.</li>
                        <li class="mb-2">Use data-driven investment decisions.</li>
                        <li>Focus on effective risk management and capital preservation.</li>
                    </ul>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-png p-4 h-100 text-center">
                        <i class="bi bi-eye fs-1 mb-3" style="color: var(--png-gold-500);"></i>
                        <h5 class="fw-bold">Transparency</h5>
                        <p class="small text-muted mb-0">Every transaction, reward, and rank achievement is tracked and visible in your dashboard.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-png p-4 h-100 text-center">
                        <i class="bi bi-people fs-1 mb-3" style="color: var(--png-gold-500);"></i>
                        <h5 class="fw-bold">Community</h5>
                        <p class="small text-muted mb-0">We grow together — team success is built into every layer of our rewards structure.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-png p-4 h-100 text-center">
                        <i class="bi bi-rocket-takeoff fs-1 mb-3" style="color: var(--png-gold-500);"></i>
                        <h5 class="fw-bold">Growth</h5>
                        <p class="small text-muted mb-0">A clear path from your first investment to Universal Crown status.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
