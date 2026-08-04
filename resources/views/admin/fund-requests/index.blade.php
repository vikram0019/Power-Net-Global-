@extends('layouts.admin')

@section('title', 'Fund Requests')
@section('page-title', 'Fund Receive List')

@section('content')
    @php
        $statuses = ['pending' => 'Pending Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach ($statuses as $key => $label)
            <a href="{{ route('admin.fund-requests.index', array_merge(request()->except('page'), ['status' => $key])) }}" class="wallet-tab-btn {{ $status === $key ? 'active' : '' }} text-decoration-none">{{ $label }}</a>
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
                <a href="{{ route('admin.fund-requests.index', ['status' => $status]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>User Name</th><th>Amount</th><th>Referral Code</th><th>Date</th><th>Screenshot</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($fundRequests as $fr)
                        <tr>
                            <td class="fw-semibold">{{ $fr->user->name }}</td>
                            <td>${{ number_format($fr->amount, 2) }}</td>
                            <td><code class="small">{{ $fr->user->referral_code }}</code></td>
                            <td>{{ $fr->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewScreenshotModal" data-image-url="{{ asset('storage/' . $fr->screenshot_path) }}" data-user-name="{{ $fr->user->name }}">
                                    <i class="bi bi-image"></i> View
                                </button>
                            </td>
                            <td>
                                <span class="badge
                                    @class([
                                        'bg-warning text-dark' => $fr->status === 'pending',
                                        'bg-success' => $fr->status === 'approved',
                                        'bg-danger' => $fr->status === 'rejected',
                                    ])">{{ $fr->status }}</span>
                            </td>
                            <td>
                                @if ($fr->status === 'pending')
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('admin.fund-requests.approve', $fr) }}" data-confirm-title="Approve Fund Request" data-confirm="Approve this fund request for ${{ number_format($fr->amount, 2) }}? This will credit the user's wallet.">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-gold">Approve</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectFund{{ $fr->id }}">Reject</button>
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
                                    <span class="small text-muted">{{ $fr->admin_note }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No fund requests in this category.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $fundRequests->links() }}
        </div>
    </div>

    <div class="modal fade" id="viewScreenshotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Screenshot — <span id="viewScreenshotUserName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="viewScreenshotImage" src="" alt="Payment screenshot" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('viewScreenshotModal');
    if (!modalEl) {
        return;
    }

    modalEl.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('viewScreenshotImage').src = button.getAttribute('data-image-url');
        document.getElementById('viewScreenshotUserName').textContent = button.getAttribute('data-user-name');
    });
});
</script>
@endpush
