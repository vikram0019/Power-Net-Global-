@extends('layouts.admin')

@section('title', 'Fund Requests')
@section('page-title', 'Fund Receive List')

@section('content')
    @php
        $statuses = ['pending' => 'Pending Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach ($statuses as $key => $label)
            <a href="{{ route('admin.fund-requests.index', ['status' => $key]) }}" class="wallet-tab-btn {{ $status === $key ? 'active' : '' }} text-decoration-none">{{ $label }}</a>
        @endforeach
    </div>

    <div class="row g-3">
        @forelse ($fundRequests as $fr)
            <div class="col-lg-4 col-md-6">
                <div class="card-png p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <a href="{{ route('admin.users.show', $fr->user) }}" class="fw-bold text-decoration-none">{{ $fr->user->name }}</a>
                            <div class="small text-muted">Referral Code: <code>{{ $fr->user->referral_code }}</code></div>
                        </div>
                        <span class="badge
                            @class([
                                'bg-warning text-dark' => $fr->status === 'pending',
                                'bg-success' => $fr->status === 'approved',
                                'bg-danger' => $fr->status === 'rejected',
                            ])">{{ $fr->status }}</span>
                    </div>
                    <a href="{{ asset('storage/' . $fr->screenshot_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $fr->screenshot_path) }}" alt="Payment screenshot" class="img-fluid rounded mb-2" style="max-height: 220px; object-fit: cover; width: 100%;">
                    </a>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Amount</span>
                        <span class="fw-bold">${{ number_format($fr->amount, 2) }}</span>
                    </div>
                    <div class="small text-muted mb-2">Submitted {{ $fr->created_at->format('d M Y, H:i') }}</div>

                    @if ($fr->status === 'pending')
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.fund-requests.approve', $fr) }}" class="flex-fill" onsubmit="return confirm('Approve this fund request for ${{ number_format($fr->amount, 2) }}? This will credit the user\'s wallet.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-gold w-100">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger flex-fill" data-bs-toggle="modal" data-bs-target="#rejectFund{{ $fr->id }}">Reject</button>
                        </div>

                        <div class="modal fade" id="rejectFund{{ $fr->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.fund-requests.reject', $fr) }}">
                                        @csrf
                                        <div class="modal-header"><h6 class="modal-title">Reject Fund Request #{{ $fr->id }}</h6></div>
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
                    @elseif ($fr->admin_note)
                        <div class="small text-muted">{{ $fr->admin_note }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-png p-4 text-center text-muted">No fund requests in this category.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $fundRequests->links() }}</div>
@endsection
