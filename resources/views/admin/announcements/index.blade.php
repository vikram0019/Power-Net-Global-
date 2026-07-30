@extends('layouts.admin')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted small mb-0">Shown to members on their dashboard in place of the income breakdown. Expired announcements stop showing automatically.</p>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-gold fw-bold">
            <i class="bi bi-plus-circle me-1"></i> New Announcement
        </a>
    </div>

    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Title</th><th>Description</th><th>Expires</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($announcements as $announcement)
                        <tr>
                            <td class="fw-semibold">{{ $announcement->title }}</td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit($announcement->description, 80) }}</td>
                            <td class="small">{{ $announcement->expires_at?->format('d M Y') ?? 'Never' }}</td>
                            <td>
                                @if ($announcement->isExpired())
                                    <span class="badge bg-secondary">Expired</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-navy">Edit</a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No announcements yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $announcements->links() }}
        </div>
    </div>
@endsection
