@extends('layouts.admin')

@section('title', 'Run ROI')
@section('page-title', 'Monthly ROI Processing')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Active Investments</div>
                <div class="stat-value">{{ $activeInvestments }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card gold">
                <div class="stat-label">Completed (24 months)</div>
                <div class="stat-value">{{ $completedInvestments }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card green">
                <div class="stat-label">Total ROI Paid</div>
                <div class="stat-value">${{ number_format($totalRoiPaid, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-2">Run Monthly ROI Payout</h6>
        <p class="small text-muted">
            There is no real cron scheduler wired up in this environment. Click below to pay one month's ROI
            (8% of invested amount) to every active investment under the 24-month cap. In production,
            wire <code>php artisan investments:pay-roi</code> to a monthly cron job instead.
        </p>
        <form method="POST" action="{{ route('admin.roi.run') }}">
            @csrf
            <button type="submit" class="btn btn-gold fw-bold px-4">
                <i class="bi bi-play-circle me-1"></i> Run ROI Now
            </button>
        </form>
    </div>
@endsection
