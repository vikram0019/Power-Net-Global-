@extends('layouts.admin')

@section('title', 'Withdrawals')
@section('page-title', 'Withdrawals')

@section('content')
    @php
        $statuses = ['otp_verified' => 'Awaiting Approval', 'pending' => 'Pending OTP', 'approved' => 'Approved', 'paid' => 'Paid', 'rejected' => 'Rejected', 'all' => 'All'];
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach ($statuses as $key => $label)
            <a href="{{ route('admin.withdrawals.index', array_merge(request()->except('page'), ['status' => $key])) }}" class="wallet-tab-btn {{ $status === $key ? 'active' : '' }} text-decoration-none">{{ $label }}</a>
        @endforeach
    </div>

    <div class="card-png p-4">
        <form method="GET" class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <input type="hidden" name="status" value="{{ $status }}">
            <div>
                <label class="form-label small fw-semibold mb-1">Search</label>
                <input type="text" name="q" class="form-control form-control-sm" style="min-width: 200px;" placeholder="Search name, referral code, amount..." value="{{ request('q') }}">
            </div>
            <div>
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div>
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-navy">Filter</button>
            @if (request()->filled('from') || request()->filled('to') || request()->filled('q'))
                <a href="{{ route('admin.withdrawals.index', ['status' => $status]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Member</th><th>Referral Code</th><th>Wallet</th><th>Requested</th><th>Fee</th><th>Payable</th><th>BEP20 Address</th>@if ($status === 'all')<th>Status</th>@endif<th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $w)
                        <tr>
                            <td>{{ $w->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $w->user->name }}</td>
                            <td><code class="small">{{ $w->user->referral_code }}</code></td>
                            <td class="text-capitalize">{{ $w->wallet_type === 'roi' ? 'MPG' : str_replace('_', ' ', $w->wallet_type) }}</td>
                            <td>${{ number_format($w->amount, 2) }}</td>
                            <td>{{ $w->fee_amount > 0 ? '-$' . number_format($w->fee_amount, 2) : '—' }}</td>
                            <td class="fw-bold" style="color: var(--png-gold-500);">${{ number_format($w->net_amount, 2) }}</td>
                            <td>
                                @if ($w->bep20_address)
                                    <code class="small">{{ $w->bep20_address }}</code>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @if ($status === 'all')
                                <td>
                                    <span @class([
                                        'badge',
                                        'bg-warning text-dark' => $w->status === 'pending',
                                        'bg-info text-dark' => $w->status === 'otp_verified',
                                        'bg-primary' => $w->status === 'approved',
                                        'bg-danger' => $w->status === 'rejected',
                                        'bg-success' => $w->status === 'paid',
                                    ])>{{ str_replace('_', ' ', $w->status) }}</span>
                                </td>
                            @endif
                            <td>
                                @if ($w->status === 'otp_verified')
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}" data-confirm-title="Approve Withdrawal" data-confirm="Approve this withdrawal for {{ $w->user->name }}? Requested: ${{ number_format($w->amount, 2) }}{{ $w->fee_amount > 0 ? ', fee: $' . number_format($w->fee_amount, 2) : '' }}, pay out: ${{ number_format($w->net_amount, 2) }} to their BEP20 address. This cannot be undone.">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-gold text-nowrap">Approve</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger text-nowrap" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $w->id }}">Reject</button>
                                    </div>

                                    <div class="modal fade" id="rejectModal{{ $w->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}">
                                                    @csrf
                                                    <div class="modal-header"><h6 class="modal-title">Reject Withdrawal #{{ $w->id }}</h6></div>
                                                    <div class="modal-body">
                                                        <label class="form-label small">Reason (optional)</label>
                                                        <textarea name="admin_note" class="form-control" rows="3"></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($w->admin_note)
                                    <span class="small text-muted">{{ $w->admin_note }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $status === 'all' ? 10 : 9 }}" class="text-center text-muted py-4">No withdrawals in this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $withdrawals->links() }}
        </div>
    </div>
@endsection
