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
            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title mb-3">Our Vision</h2>
                    <p class="text-muted">To build the world's most trusted global rewards network — one where every member has a transparent, fair path to financial growth through collaboration, teamwork, and shared success.</p>
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title mb-3">Our Mission</h2>
                    <p class="text-muted">We provide a structured, technology-driven platform that rewards direct effort and team building alike — combining direct referral bonuses, deep-level team income, monthly profit sharing, and a clear 13-rank achievement ladder so every member always knows what's next.</p>
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
                        <p class="small text-muted mb-0">A clear path from your first investment to Crown Ambassador Universal status.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
