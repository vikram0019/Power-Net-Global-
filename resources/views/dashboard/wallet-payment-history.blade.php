@extends('layouts.dashboard')

@section('title', 'Payment History')
@section('page-title', 'Wallet — Payment History')

@section('content')
    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Date</th><th>Wallet</th><th>Direction</th><th>Amount</th><th>Balance After</th><th>Description</th></tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-capitalize">{{ $t->wallet_type }}</td>
                            <td>
                                <span class="badge {{ $t->direction === 'credit' ? 'bg-success' : 'bg-secondary' }}">{{ $t->direction }}</span>
                            </td>
                            <td>${{ number_format($t->amount, 2) }}</td>
                            <td>${{ number_format($t->balance_after, 2) }}</td>
                            <td class="small text-muted">{{ $t->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
