@extends('layouts.admin')

@section('title', 'Run MPG')
@section('page-title', 'Monthly MPG Processing')

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
                <div class="stat-label">Completed ({{ config('mlm.roi_max_months') }} months)</div>
                <div class="stat-value">{{ $completedInvestments }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card green">
                <div class="stat-label">Total MPG Paid</div>
                <div class="stat-value">${{ number_format($totalRoiPaid, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card-png p-4 mb-4">
        <h6 class="fw-bold mb-2"><i class="bi bi-calendar-check me-1"></i> Automatic Daily Payout</h6>
        <p class="small text-muted mb-1">
            MPG now runs automatically <strong>once every day</strong> — each active, MPG-enabled investment is credited
            {{ config('mlm.monthly_roi_percent') }}% &divide; days-in-month, so every calendar month still totals
            {{ config('mlm.monthly_roi_percent') }}%, up to the lifetime cap of
            {{ config('mlm.monthly_roi_percent') * config('mlm.roi_max_months') }}% ({{ config('mlm.roi_max_months') }}
            months' worth). Dummy users with the MPG benefit turned off are skipped, and each investment is only paid
            once per calendar day.
        </p>
        <p class="small text-muted mb-0">
            Next scheduled run: <strong>{{ $nextScheduledRun->format('d M Y, H:i') }}</strong>
            <span class="text-muted">({{ $nextScheduledRun->diffForHumans() }})</span>
        </p>
        <p class="small text-muted mt-2 mb-0">
            The schedule is defined in <code>routes/console.php</code> and fires whenever
            <code>php artisan schedule:run</code> is triggered — wire that to a cron entry
            (<code>* * * * * php artisan schedule:run</code>) on your production server.
        </p>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-2">Run Manually</h6>
        <p class="small text-muted">
            Use this to run a payout on demand (e.g. to catch up if a scheduled run was missed, or during local testing
            where no cron is wired up). Safe to click more than once — each investment is only paid once per day.
        </p>
        <form method="POST" action="{{ route('admin.roi.run') }}" data-confirm-title="Run Daily MPG Payout" data-confirm="Run daily MPG payout now? This will credit today's MPG slice to every eligible active investment not yet paid today, and cannot be undone.">
            @csrf
            <button type="submit" class="btn btn-gold fw-bold px-4">
                <i class="bi bi-play-circle me-1"></i> Run MPG Now
            </button>
        </form>
    </div>
@endsection
