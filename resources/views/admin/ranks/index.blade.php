@extends('layouts.admin')

@section('title', 'Rank Log')
@section('page-title', 'Rank Achievement Log')

@section('content')
    <div class="card-png p-4">
        <form method="GET" class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <div>
                <label class="form-label small fw-semibold mb-1">Search</label>
                <input type="text" name="q" class="form-control form-control-sm" style="min-width: 220px;" placeholder="Search member, rank, package..." value="{{ request('q') }}">
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
                <a href="{{ route('admin.ranks.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Member</th><th>Rank</th><th>Package</th><th>Reward</th><th>Achieved</th></tr>
                </thead>
                <tbody>
                    @forelse ($rankLog as $rh)
                        <tr>
                            <td><a href="{{ route('admin.users.show', $rh->user) }}" class="text-decoration-none fw-semibold">{{ $rh->user->name }}</a></td>
                            <td>{{ $rh->rank->name }}</td>
                            <td><span class="badge badge-group star">{{ $rh->rank->package_group }}</span></td>
                            <td>${{ number_format($rh->rank->reward_amount, 2) }}</td>
                            <td>{{ $rh->achieved_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No ranks achieved yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $rankLog->links() }}
        </div>
    </div>
@endsection
