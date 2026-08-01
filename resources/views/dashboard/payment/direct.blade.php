@extends('layouts.dashboard')

@section('title', 'Direct Income')
@section('page-title', 'Payment')

@section('content')
    @include('partials.payment-tabs')

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card gold">
                <div class="stat-label">Total Direct Income</div>
                <div class="stat-value">${{ number_format($total, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Direct (from your referrals)</div>
                <div class="stat-value">${{ number_format($directTotal, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card blue">
                <div class="stat-label">Upline Pass-up (2%)</div>
                <div class="stat-value">${{ number_format($uplineTotal, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Direct Income History</h6>
        @include('partials.payment-date-filter')
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead><tr><th>Date</th><th>Type</th><th>From</th><th>Amount</th></tr></thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $t->type === 'direct_reward' ? 'Direct' : 'Upline Pass-up' }}</td>
                            <td>{{ $t->sourceUser->name ?? '—' }}</td>
                            <td class="fw-semibold">${{ number_format($t->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No direct income yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
