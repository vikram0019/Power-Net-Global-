@extends('layouts.admin')

@section('title', 'Payment Settings')
@section('page-title', 'Payment Received Wallet')

@section('content')
    <div class="card-png p-4" style="max-width: 640px;">
        <p class="text-muted small mb-4">
            This BEP20 address and barcode/QR image are shown to members on their Add Fund page so they can pay you directly.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.payment-settings.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold">BEP20 Address</label>
                <input type="text" name="bep20_address" class="form-control" value="{{ old('bep20_address', $setting->bep20_address ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Barcode / QR Image (jpg, jpeg, png)</label>
                <input type="file" name="barcode" accept=".jpg,.jpeg,.png" class="form-control">
            </div>
            @if ($setting?->barcode_path)
                <div class="mb-3">
                    <div class="small text-muted mb-1">Current barcode:</div>
                    <img src="{{ asset('storage/' . $setting->barcode_path) }}" alt="Barcode" style="max-width: 200px; border-radius: 8px; border: 1px solid #eceff5;">
                </div>
            @endif
            <button type="submit" class="btn btn-gold fw-bold px-4">Save</button>
        </form>
    </div>
@endsection
