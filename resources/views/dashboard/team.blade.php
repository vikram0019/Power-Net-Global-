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

    <div class="d-flex flex-wrap gap-3 mb-4 small text-muted">
        <span class="d-inline-flex align-items-center gap-1"><span class="status-dot green"></span> Active — invested</span>
        <span class="d-inline-flex align-items-center gap-1"><span class="status-dot yellow"></span> Dummy — admin-created</span>
        <span class="d-inline-flex align-items-center gap-1"><span class="status-dot red"></span> Inactive — no investment yet</span>
    </div>

    <div class="card-png p-4 mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-1"></i> Team Tree</h6>
        @if (count($tree['children']))
            <div class="org-tree-wrap">
                <ul class="org-tree">
                    <li x-data="{ open: true }">
                        <div class="org-node org-node-root">
                            <div class="org-node-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="org-node-name">{{ $tree['user']->name }} (You)</div>
                            <div class="org-node-rank badge-group {{ strtolower($tree['user']->currentRank?->package_group ?? 'unranked') }}">{{ $tree['user']->currentRank?->name ?? 'Unranked' }}</div>
                            <div class="org-node-invested">${{ number_format($tree['invested'], 2) }}</div>
                            <button type="button" class="org-node-toggle" @click="open = !open">
                                <i class="bi" :class="open ? 'bi-dash-circle' : 'bi-plus-circle'"></i>
                            </button>
                        </div>
                        <ul x-show="open">
                            @foreach ($tree['children'] as $child)
                                @include('partials.team-tree-node', ['node' => $child])
                            @endforeach
                        </ul>
                    </li>
                </ul>
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
                    <tr><th>Name</th><th>Level</th><th>Status</th><th>Rank</th><th>Invested</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $rowStatus = $row->is_dummy ? 'yellow' : ((float) $row->invested > 0 ? 'green' : 'red');
                            $rowStatusLabel = ['yellow' => 'Dummy', 'green' => 'Active', 'red' => 'Inactive'][$rowStatus];
                        @endphp
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>Level {{ $row->depth }}</td>
                            <td>
                                <span class="d-inline-flex align-items-center gap-1" title="{{ $rowStatusLabel }}">
                                    <span class="status-dot {{ $rowStatus }}"></span>
                                    <span class="small">{{ $rowStatusLabel }}</span>
                                </span>
                            </td>
                            <td>{{ $row->rank_name ?? 'Unranked' }}</td>
                            <td>${{ number_format($row->invested, 2) }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No team members yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
