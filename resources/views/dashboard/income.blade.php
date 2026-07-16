@extends('layouts.dashboard')

@section('title', 'Income')
@section('page-title', 'Income')

@section('content')
    @php
        $tabs = [
            'all' => 'All',
            'direct_reward' => 'Direct Reward',
            'direct_reward_upline' => 'Direct Reward (Upline)',
            'level_income' => 'Level Income',
            'monthly_roi' => 'Monthly ROI',
            'rank_reward' => 'Rank Reward',
        ];
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('income.index', ['type' => $key]) }}" class="wallet-tab-btn {{ $type === $key ? 'active' : '' }} text-decoration-none">
                {{ $label }}
                @if (isset($totals[$key]))
                    <span class="ms-1 opacity-75">(${{ number_format($totals[$key], 0) }})</span>
                @endif
            </a>
        @endforeach
    </div>

    @if ($type === 'level_income' && $levelBreakdown->count())
        <div class="card-png p-4 mb-4">
            <h6 class="fw-bold mb-3">Per-Level Breakdown</h6>
            <div class="row g-2">
                @foreach ($levelBreakdown as $lvl)
                    <div class="col-md-3 col-6">
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
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Type</th><th>From</th><th>Level</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $income)
                        <tr>
                            <td>{{ $income->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $income->type) }}</td>
                            <td>{{ $income->sourceUser->name ?? '—' }}</td>
                            <td>{{ $income->level ?? '—' }}</td>
                            <td class="fw-semibold">${{ number_format($income->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No income transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
