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
        <h6 class="fw-bold mb-2"><i class="bi bi-calendar-check me-1"></i> Automatic Monthly Payout</h6>
        <p class="small text-muted mb-1">
            MPG now runs automatically on the <strong>1st of every month</strong> — 8% of invested amount paid to every
            MPG-enabled active investment under the {{ config('mlm.roi_max_months') }}-month cap. Dummy users with the
            monthly MPG benefit turned off are skipped.
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
            where no cron is wired up).
        </p>
        <form method="POST" action="{{ route('admin.roi.run') }}" onsubmit="return confirm('Run monthly MPG payout now? This will credit 8% MPG to every eligible active investment and cannot be undone.');">
            @csrf
            <button type="submit" class="btn btn-gold fw-bold px-4">
                <i class="bi bi-play-circle me-1"></i> Run MPG Now
            </button>
        </form>
    </div>
@endsection
