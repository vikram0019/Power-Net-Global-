@extends('layouts.dashboard')

@section('title', 'Withdrawal Requests')
@section('page-title', 'Wallet — Recent Withdrawal Requests')

@section('content')
    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Wallet</th><th>Requested</th><th>Fee</th><th>Payable</th><th>BEP20 Address</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $w)
                        <tr>
                            <td>{{ $w->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-capitalize">{{ $w->wallet_type === 'roi' ? 'MPG' : str_replace('_', ' ', $w->wallet_type) }}</td>
                            <td>${{ number_format($w->amount, 2) }}</td>
                            <td>{{ $w->fee_amount > 0 ? '-$' . number_format($w->fee_amount, 2) : '—' }}</td>
                            <td class="fw-semibold">${{ number_format($w->net_amount, 2) }}</td>
                            <td><code class="small">{{ $w->bep20_address ?? '—' }}</code></td>
                            <td>
                                <span class="badge
                                    @class([
                                        'bg-warning text-dark' => $w->status === 'pending',
                                        'bg-info text-dark' => $w->status === 'otp_verified',
                                        'bg-primary' => $w->status === 'approved',
                                        'bg-danger' => $w->status === 'rejected',
                                        'bg-success' => $w->status === 'paid',
                                    ])">{{ str_replace('_', ' ', $w->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No withdrawal requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $withdrawals->links() }}
        </div>
    </div>
@endsection
