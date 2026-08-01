@extends('layouts.dashboard')

@section('title', 'Level Income')
@section('page-title', 'Payment')

@section('content')
    @include('partials.payment-tabs')

    <div class="stat-card gold mb-4" style="max-width: 320px;">
        <div class="stat-label">Total Level Income Earned</div>
        <div class="stat-value">${{ number_format($total, 2) }}</div>
    </div>

    @if ($levelBreakdown->count())
        <div class="card-png p-4 mb-4">
            <h6 class="fw-bold mb-3">Level-wise Breakdown</h6>
            <div class="row g-2">
                @foreach ($levelBreakdown as $lvl)
                    <div class="col-md-2 col-4">
                        <div class="border rounded-3 p-3 text-center">
                            <div class="small text-muted">Level {{ $lvl->level }}</div>
                            <div class="fw-bold">${{ number_format($lvl->total, 2) }}</div>
                            <div class="small text-muted">{{ $lvl->count }} payout(s)</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Level Income History</h6>
        @include('partials.payment-date-filter')
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead><tr><th>Date</th><th>From</th><th>Level</th><th>Amount</th></tr></thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $t->sourceUser->name ?? '—' }}</td>
                            <td>Level {{ $t->level }}</td>
                            <td class="fw-semibold">${{ number_format($t->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No level income yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
