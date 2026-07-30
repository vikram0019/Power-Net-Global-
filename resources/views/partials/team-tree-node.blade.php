<li x-data="{ open: true }">
    <div class="org-node">
        <div class="org-node-avatar">
            <i class="bi bi-person-circle"></i>
            <span class="status-dot {{ $node['user']->investorStatus() }}" title="{{ $node['user']->investorStatusLabel() }}"></span>
        </div>
        <div class="org-node-name" title="{{ $node['user']->name }}">{{ $node['user']->name }}</div>
        <div class="org-node-rank badge-group {{ strtolower($node['user']->currentRank?->package_group ?? 'unranked') }}">{{ $node['user']->currentRank?->name ?? 'Unranked' }}</div>
        <div class="org-node-referral">{{ $node['user']->referral_code }}</div>
        <div class="org-node-invested">${{ number_format($node['invested'], 2) }}</div>
        @if (count($node['children']))
            <button type="button" class="org-node-toggle" @click="open = !open">
                <i class="bi" :class="open ? 'bi-dash-circle' : 'bi-plus-circle'"></i>
            </button>
        @endif
    </div>
    @if (count($node['children']))
        <ul x-show="open">
            @foreach ($node['children'] as $child)
                @include('partials.team-tree-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
