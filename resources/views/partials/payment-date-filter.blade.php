<form method="GET" action="{{ request()->url() }}" class="d-flex flex-wrap align-items-end gap-2 mb-3">
    <div>
        <label class="form-label small fw-semibold mb-1">Search</label>
        <input type="text" name="q" class="form-control form-control-sm" style="min-width: 200px;" placeholder="Search any column..." value="{{ request('q') }}">
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
        <a href="{{ request()->url() }}" class="btn btn-sm btn-outline-secondary">Clear</a>
    @endif
</form>
