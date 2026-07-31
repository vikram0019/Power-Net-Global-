<li x-data="{ open: true }">
    <div class="org-node org-node-compact">
        <button type="button" class="org-node-avatar org-node-avatar-btn" title="View details"
            @click="selected = {
                name: @js($node['user']->name),
                joined: @js($node['user']->created_at->format('d M Y')),
                invested: {{ (float) $node['invested'] }},
                rank: @js($node['user']->currentRank?->name ?? 'Unranked'),
                power: { count: {{ (int) $node['leg_stats']['power']['count'] }}, investment: {{ (float) $node['leg_stats']['power']['investment'] }} },
                second: { count: {{ (int) $node['leg_stats']['second']['count'] }}, investment: {{ (float) $node['leg_stats']['second']['investment'] }} },
                rest: { count: {{ (int) $node['leg_stats']['rest']['count'] }}, investment: {{ (float) $node['leg_stats']['rest']['investment'] }} }
            }; popupOpen = true">
            <i class="bi bi-person-circle"></i>
            <span class="status-dot {{ $node['user']->investorStatus() }}" title="{{ $node['user']->investorStatusLabel() }}"></span>
        </button>
        <div class="org-node-referral">
            <span class="org-node-referral-chip"><i class="bi bi-link-45deg"></i>{{ $node['user']->referral_code }}</span>
        </div>
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
