@extends('layouts.dashboard')

@section('title', 'Ranks & Reward Income')
@section('page-title', 'Payment')

@section('content')
    @include('partials.payment-tabs')

    <div class="stat-card gold mb-4" style="max-width: 320px;">
        <div class="stat-label">Total Rank Reward Earned</div>
        <div class="stat-value">${{ number_format($total, 2) }}</div>
    </div>

    <div class="card-png p-4 mb-4">
        <h6 class="fw-bold mb-3">Ranks Achieved</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead><tr><th>Rank</th><th>Achieved</th><th>Reward</th></tr></thead>
                <tbody>
                    @forelse ($rankHistory as $rh)
                        <tr>
                            <td>{{ $rh->rank->name }}</td>
                            <td>{{ $rh->achieved_at->format('d M Y, H:i') }}</td>
                            <td class="fw-semibold">${{ number_format($rh->rank->reward_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No ranks achieved yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Reward Payout History</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead><tr><th>Date</th><th>Amount</th></tr></thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                            <td class="fw-semibold">${{ number_format($t->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-3">No rank rewards paid yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
