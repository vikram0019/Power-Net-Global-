@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <div class="card-png p-4 mb-4">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by name, email, mobile, or referral code">
            <button type="submit" class="btn btn-navy">Search</button>
        </form>
    </div>

    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Name</th><th>Contact</th><th>Referral Code</th><th>Sponsor</th><th>Rank</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="small">{{ $u->email }}<br><span class="text-muted">{{ $u->mobile }}</span></td>
                            <td><code>{{ $u->referral_code }}</code></td>
                            <td>{{ $u->sponsor->name ?? '—' }}</td>
                            <td>{{ $u->currentRank->name ?? 'Unranked' }}</td>
                            <td><span class="badge {{ $u->status === 'active' ? 'bg-success' : ($u->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $u->status }}</span></td>
                            <td><a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-navy">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
@endsection
