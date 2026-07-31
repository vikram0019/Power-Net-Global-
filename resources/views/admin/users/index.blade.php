@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <div class="card-png p-4 mb-4 d-flex flex-wrap flex-row justify-content-between align-items-center gap-3">
        <form method="GET" class="d-flex gap-2 flex-fill" style="max-width: 480px;">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by name, email, mobile, or referral code">
            <button type="submit" class="btn btn-navy">Search</button>
        </form>
        <a href="{{ route('admin.users.create-dummy') }}" class="btn btn-gold fw-bold"><i class="bi bi-person-plus me-1"></i>Create Dummy User</a>
    </div>

    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Name</th><th>Contact</th><th>Referral Code</th><th>Sponsor</th><th>Rank</th><th>Investor Status</th><th>Account Status</th><th>ROI</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="small">{{ $u->email }}<br><span class="text-muted">{{ $u->mobile }}</span></td>
                            <td><code>{{ $u->referral_code }}</code></td>
                            <td>{{ $u->sponsor->name ?? '—' }}</td>
                            <td>{{ $u->currentRank->name ?? 'Unranked' }}</td>
                            <td>@include('partials.investor-status', ['user' => $u])</td>
                            <td>
                                @php
                                    $accountState = $u->is_dummy ? 'Dummy' : ($u->totalInvested() > 0 ? 'Active' : 'Unactive');
                                @endphp
                                <span class="badge
                                    @class([
                                        'bg-warning text-dark' => $accountState === 'Dummy',
                                        'bg-success' => $accountState === 'Active',
                                        'bg-secondary' => $accountState === 'Unactive',
                                    ])">{{ $accountState }}</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.toggle-roi', $u) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $u->roi_enabled ? 'btn-gold' : 'btn-outline-secondary' }}">
                                        {{ $u->roi_enabled ? 'On' : 'Off' }}
                                    </button>
                                </form>
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-navy">View</a>
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @if ($u->status === 'approval_pending')
                                    <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-gold">Approve</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
@endsection
