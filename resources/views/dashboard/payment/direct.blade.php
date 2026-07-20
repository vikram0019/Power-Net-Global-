@extends('layouts.dashboard')

@section('title', 'Direct Income')
@section('page-title', 'Payment')

@section('content')
    @include('partials.payment-tabs')

    <div class="stat-card gold mb-4" style="max-width: 320px;">
        <div class="stat-label">Total Direct Income</div>
        <div class="stat-value">${{ number_format($total, 2) }}</div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Direct Income History</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead><tr><th>Date</th><th>From</th><th>Amount</th></tr></thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $t->sourceUser->name ?? '—' }}</td>
                            <td class="fw-semibold">${{ number_format($t->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No direct income yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
