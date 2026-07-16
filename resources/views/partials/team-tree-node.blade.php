<div class="mb-2" x-data="{ open: true }">
    <div class="tree-node">
        @if (count($node['children']))
            <button class="btn btn-sm p-0 border-0" @click="open = !open" style="width: 20px;">
                <i class="bi" :class="open ? 'bi-dash-square' : 'bi-plus-square'"></i>
            </button>
        @else
            <span style="width: 20px; display: inline-block;"></span>
        @endif
        <i class="bi bi-person-circle text-secondary"></i>
        <span class="fw-semibold">{{ $node['user']->name }}</span>
        <span class="badge badge-group star">{{ $node['user']->currentRank?->name ?? 'Unranked' }}</span>
        <span class="small text-muted">${{ number_format($node['invested'], 2) }}</span>
    </div>
    @if (count($node['children']))
        <div class="tree-children mt-2" x-show="open">
            @foreach ($node['children'] as $child)
                @include('partials.team-tree-node', ['node' => $child])
            @endforeach
        </div>
    @endif
</div>
