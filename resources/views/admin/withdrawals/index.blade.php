@extends('layouts.admin')

@section('title', 'Withdrawals')
@section('page-title', 'Withdrawals')

@section('content')
    @php
        $statuses = ['otp_verified' => 'Awaiting Approval', 'pending' => 'Pending OTP', 'approved' => 'Approved', 'paid' => 'Paid', 'rejected' => 'Rejected', 'all' => 'All'];
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach ($statuses as $key => $label)
            <a href="{{ route('admin.withdrawals.index', ['status' => $key]) }}" class="wallet-tab-btn {{ $status === $key ? 'active' : '' }} text-decoration-none">{{ $label }}</a>
        @endforeach
    </div>

    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Member</th><th>User ID</th><th>Wallet</th><th>Amount</th><th>BEP20 Address</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $w)
                        <tr>
                            <td>{{ $w->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $w->user->name }}</td>
                            <td>{{ $w->user->id }}</td>
                            <td class="text-capitalize">{{ $w->wallet_type }}</td>
                            <td class="fw-semibold">${{ number_format($w->amount, 2) }}</td>
                            <td>
                                @if ($w->bep20_address)
                                    <code class="small">{{ $w->bep20_address }}</code>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
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
                            <td>
                                @if ($w->status === 'otp_verified')
                                    <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-gold">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $w->id }}">Reject</button>

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
                        <tr><td colspan="8" class="text-center text-muted py-4">No withdrawals in this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $withdrawals->links() }}
        </div>
    </div>
@endsection
