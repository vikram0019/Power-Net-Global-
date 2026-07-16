@extends('layouts.dashboard')

@section('title', 'Team')
@section('page-title', 'Team')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-label">Total Team Members</div>
                <div class="stat-value">{{ $teamSize }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card green">
                <div class="stat-label">Total Team Business</div>
                <div class="stat-value">${{ number_format($totalTeamBusiness, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card-png p-4 mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-1"></i> Team Tree</h6>
        @if (count($tree['children']))
            <div>
                @foreach ($tree['children'] as $child)
                    @include('partials.team-tree-node', ['node' => $child])
                @endforeach
            </div>
        @else
            <p class="text-muted small mb-0">You haven't referred anyone yet. Share your referral code to build your team.</p>
        @endif
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Team Payment Detail</h6>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Name</th><th>Level</th><th>Rank</th><th>Invested</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>Level {{ $row->depth }}</td>
                            <td>{{ $row->rank_name ?? 'Unranked' }}</td>
                            <td>${{ number_format($row->invested, 2) }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No team members yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
