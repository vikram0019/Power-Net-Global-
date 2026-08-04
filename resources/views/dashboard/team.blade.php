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

    <div class="row g-3 mb-4">
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="stat-card">
                <div class="stat-label">Team A Investment</div>
                <div class="stat-value">${{ number_format($tree['leg_stats']['power']['investment'], 2) }}</div>
                <div class="small opacity-75 mt-1">Total member</div>
                <div class="fw-bold">{{ $tree['leg_stats']['power']['count'] }}</div>
                <div class="d-flex gap-3 mt-2 small">
                    <span class="d-inline-flex align-items-center gap-1"><span class="status-dot green"></span>{{ $tree['leg_stats']['power']['active_count'] }} active</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="status-dot red"></span>{{ $tree['leg_stats']['power']['inactive_count'] }} inactive</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="stat-card gold">
                <div class="stat-label">Team B Investment</div>
                <div class="stat-value">${{ number_format($tree['leg_stats']['second']['investment'], 2) }}</div>
                <div class="small opacity-75 mt-1">Total member</div>
                <div class="fw-bold">{{ $tree['leg_stats']['second']['count'] }}</div>
                <div class="d-flex gap-3 mt-2 small">
                    <span class="d-inline-flex align-items-center gap-1"><span class="status-dot green"></span>{{ $tree['leg_stats']['second']['active_count'] }} active</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="status-dot red"></span>{{ $tree['leg_stats']['second']['inactive_count'] }} inactive</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="stat-card blue">
                <div class="stat-label">Team C Investment</div>
                <div class="stat-value">${{ number_format($tree['leg_stats']['rest']['investment'], 2) }}</div>
                <div class="small opacity-75 mt-1">Total member</div>
                <div class="fw-bold">{{ $tree['leg_stats']['rest']['count'] }}</div>
                <div class="d-flex gap-3 mt-2 small">
                    <span class="d-inline-flex align-items-center gap-1"><span class="status-dot green"></span>{{ $tree['leg_stats']['rest']['active_count'] }} active</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="status-dot red"></span>{{ $tree['leg_stats']['rest']['inactive_count'] }} inactive</span>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-3 mb-4 small text-muted">
        <span class="d-inline-flex align-items-center gap-1"><span class="status-dot green"></span> Active — invested</span>
        <span class="d-inline-flex align-items-center gap-1"><span class="status-dot red"></span> Inactive — no investment yet</span>
    </div>

    <div class="card-png p-4 mb-4" x-data="{ popupOpen: false, selected: null }">
        <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-1"></i> Team Tree</h6>
        <div class="d-flex flex-wrap gap-3 mb-3 small text-muted">
            <span class="d-inline-flex align-items-center gap-1"><span class="tree-legend-swatch team-a"></span> Team A (Power leg)</span>
            <span class="d-inline-flex align-items-center gap-1"><span class="tree-legend-swatch team-b"></span> Team B (2nd leg)</span>
            <span class="d-inline-flex align-items-center gap-1"><span class="tree-legend-swatch team-c"></span> Team C (Rest legs)</span>
        </div>
        <div class="org-tree-wrap">
            <ul class="org-tree">
                <li x-data="{ open: true }">
                    <div class="org-node org-node-root org-node-compact">
                        <button type="button" class="org-node-avatar org-node-avatar-btn" title="{{ $tree['user']->name }} (You)"
                            @click="selected = {
                                name: @js($tree['user']->name . ' (You)'),
                                joined: @js($tree['user']->created_at->format('d M Y')),
                                invested: {{ (float) $tree['invested'] }},
                                rank: @js($tree['user']->currentRank?->name ?? 'Unranked'),
                                power: { count: {{ (int) $tree['leg_stats']['power']['count'] }}, investment: {{ (float) $tree['leg_stats']['power']['investment'] }} },
                                second: { count: {{ (int) $tree['leg_stats']['second']['count'] }}, investment: {{ (float) $tree['leg_stats']['second']['investment'] }} },
                                rest: { count: {{ (int) $tree['leg_stats']['rest']['count'] }}, investment: {{ (float) $tree['leg_stats']['rest']['investment'] }} }
                            }; popupOpen = true">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <div class="org-node-referral">
                            <span class="org-node-referral-chip"><i class="bi bi-link-45deg"></i>{{ $tree['user']->referral_code }}</span>
                        </div>
                        @if (count($tree['children']))
                            <button type="button" class="org-node-toggle" @click="open = !open">
                                <i class="bi" :class="open ? 'bi-dash-circle' : 'bi-plus-circle'"></i>
                            </button>
                        @endif
                    </div>
                    @if (count($tree['children']))
                        <ul x-show="open">
                            @foreach ($tree['children'] as $child)
                                @include('partials.team-tree-node', ['node' => $child])
                            @endforeach
                        </ul>
                    @endif
                </li>
            </ul>
        </div>
        @unless (count($tree['children']))
            <p class="text-muted small mt-2 mb-0">You haven't referred anyone yet. Share your referral code to build your team.</p>
        @endunless

        <div class="modal" :class="{ 'd-block': popupOpen }" x-show="popupOpen" x-cloak tabindex="-1" style="background: rgba(5,11,24,0.6);" @click.self="popupOpen = false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" x-show="popupOpen" x-transition>
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Member Detail</h6>
                        <button type="button" class="btn-close" @click="popupOpen = false"></button>
                    </div>
                    <div class="modal-body" x-show="selected">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <div class="small text-muted">Joining Date</div>
                                <div class="fw-semibold" x-text="selected?.joined"></div>
                                <div class="mt-2 small text-muted">Name</div>
                                <div class="fw-semibold" x-text="selected?.name"></div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">Investment</div>
                                <div class="fw-semibold">$<span x-text="selected?.invested.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span></div>
                                <div class="mt-2 small text-muted">Rank</div>
                                <div class="fw-semibold" x-text="selected?.rank"></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-png text-center align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Team A<div class="small text-muted fw-normal">Power Leg</div></th>
                                        <th>Team B<div class="small text-muted fw-normal">2nd Leg</div></th>
                                        <th>Team C<div class="small text-muted fw-normal">3rd &amp; Other Legs</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="fw-bold" x-text="selected?.power.count"></div>
                                            <div class="small text-muted">$<span x-text="selected?.power.investment.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold" x-text="selected?.second.count"></div>
                                            <div class="small text-muted">$<span x-text="selected?.second.investment.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold" x-text="selected?.rest.count"></div>
                                            <div class="small text-muted">$<span x-text="selected?.rest.investment.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-navy" @click="popupOpen = false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-png p-4">
        <h6 class="fw-bold mb-3">Team Payment Detail</h6>
        <form method="GET" class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <div>
                <label class="form-label small fw-semibold mb-1">Search</label>
                <input type="text" name="q" class="form-control form-control-sm" style="min-width: 220px;" placeholder="Search any column..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn btn-sm btn-navy">Search</button>
            @if ($search !== '')
                <a href="{{ route('team.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>S.No.</th><th>Name</th><th>Level</th><th>Status</th><th>Rank</th><th>Invested</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $rowStatus = $row->is_dummy ? 'yellow' : ((float) $row->invested > 0 ? 'green' : 'red');
                            $rowStatusLabel = ['yellow' => 'Dummy', 'green' => 'Active', 'red' => 'Inactive'][$rowStatus];
                        @endphp
                        <tr>
                            <td>{{ $rows->firstItem() + $loop->index }}</td>
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
                        <tr><td colspan="7" class="text-center text-muted py-4">No team members found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $rows->links() }}
        </div>
    </div>
@endsection
