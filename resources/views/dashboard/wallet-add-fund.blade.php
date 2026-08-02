@extends('layouts.dashboard')

@section('title', 'Add Fund')
@section('page-title', 'Wallet — Add Fund')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Deposit Wallet</div>
                <div class="stat-value">${{ number_format($wallet->deposit_balance, 2) }}</div>
                <small class="opacity-75">In-transit balance while a deposit is being processed</small>
            </div>
        </div>
    </div>

    <div class="card-png p-4 mb-4" x-data="{ copied: false }">
        <h6 class="fw-bold mb-3"><i class="bi bi-qr-code me-1"></i> Deposit Funds (BEP20)</h6>

        @if ($paymentSetting?->bep20_address)
            <div class="row g-3 mb-3">
                <div class="col-sm-7">
                    <label class="form-label small fw-semibold">Send payment to this BEP20 address</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $paymentSetting->bep20_address }}" id="bep20Address" readonly>
                        <button class="btn btn-navy" type="button"
                            @click="navigator.clipboard.writeText(document.getElementById('bep20Address').value); copied = true; setTimeout(() => copied = false, 2000)">
                            <span x-show="!copied"><i class="bi bi-clipboard"></i></span>
                            <span x-show="copied"><i class="bi bi-check2"></i></span>
                        </button>
                    </div>
                </div>
                @if ($paymentSetting->barcode_path)
                    <div class="col-sm-5">
                        <label class="form-label small fw-semibold">Or scan barcode</label>
                        <img src="{{ asset('storage/' . $paymentSetting->barcode_path) }}" alt="Payment barcode" class="img-fluid rounded border" style="max-height: 130px;">
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-warning small">Payment details haven't been configured by admin yet. Please check back soon.</div>
        @endif

        <p class="small text-muted">Minimum ${{ number_format($minimumInvestment) }}. After paying, enter the amount and upload a screenshot as proof — your investment starts once admin approves it.</p>

        @error('amount')
            <div class="alert alert-danger py-1 small">{{ $message }}</div>
        @enderror
        @error('screenshot')
            <div class="alert alert-danger py-1 small">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('wallet.fund-request') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold">Amount Paid (USD)</label>
                    <input type="number" step="0.01" min="{{ $minimumInvestment }}" name="amount" class="form-control" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold">Payment Screenshot</label>
                    <input type="file" name="screenshot" accept=".jpg,.jpeg,.png" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-gold w-100 fw-bold mt-3">Submit for Review</button>
        </form>
    </div>

    @if ($fundRequests->count())
        <div class="card-png p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Your Recent Deposit Requests</h6>
                <a href="{{ route('wallet.payment-history') }}" class="small fw-semibold text-decoration-none">View Payment History <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-png align-middle">
                    <thead>
                        <tr><th>Date</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($fundRequests as $fr)
                            <tr>
                                <td>{{ $fr->created_at->format('d M Y, H:i') }}</td>
                                <td>${{ number_format($fr->amount, 2) }}</td>
                                <td>
                                    <span class="badge
                                        @class([
                                            'bg-warning text-dark' => $fr->status === 'pending',
                                            'bg-success' => $fr->status === 'approved',
                                            'bg-danger' => $fr->status === 'rejected',
                                        ])">{{ $fr->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <a href="{{ route('wallet.payment-history') }}" class="btn btn-navy">
        <i class="bi bi-clock-history me-1"></i> View Full Payment History
    </a>
@endsection
