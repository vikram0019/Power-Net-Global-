@extends('layouts.dashboard')

@section('title', 'Monthly ROI Income')
@section('page-title', 'Payment')

@section('content')
    @include('partials.payment-tabs')

    <div class="stat-card gold mb-4" style="max-width: 320px;">
        <div class="stat-label">Total Monthly ROI Earned</div>
        <div class="stat-value">${{ number_format($totalRoi, 2) }}</div>
    </div>

    @forelse ($investments as $investment)
        @php $rows = $roiTransactions->get($investment->id, collect()); @endphp
        <div class="card-png p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold mb-1">Investment of ${{ number_format($investment->amount, 2) }}</h6>
                    <div class="small text-muted">Invested {{ $investment->created_at->format('d M Y') }} &middot; 8% monthly, {{ config('mlm.roi_max_months') }}-month schedule</div>
                </div>
                <div class="text-end">
                    <span class="badge {{ $investment->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $investment->status }}</span>
                    <div class="small text-muted mt-1">{{ $investment->roi_months_paid }} / {{ config('mlm.roi_max_months') }} months paid</div>
                </div>
            </div>
            <div class="progress-png mb-3"><div class="bar" style="width: {{ min(100, ($investment->roi_months_paid / config('mlm.roi_max_months')) * 100) }}%;"></div></div>

            @if ($rows->count())
                <div class="table-responsive">
                    <table class="table table-png align-middle mb-0">
                        <thead><tr><th>Month</th><th>Date Paid</th><th>Amount</th></tr></thead>
                        <tbody>
                            @foreach ($rows as $i => $row)
                                <tr>
                                    <td>Month {{ $i + 1 }}</td>
                                    <td>{{ $row->created_at->format('d M Y, H:i') }}</td>
                                    <td class="fw-semibold">${{ number_format($row->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">No ROI paid yet for this investment.</p>
            @endif
        </div>
    @empty
        <div class="card-png p-4 text-center text-muted">No investments yet — invest to start earning monthly ROI.</div>
    @endforelse
@endsection
