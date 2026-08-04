<li>
    <div class="org-node org-node-compact @if(($node['team'] ?? null) === 'A') org-node-team-a @elseif(($node['team'] ?? null) === 'B') org-node-team-b @elseif(($node['team'] ?? null) === 'C') org-node-team-c @endif">
        <div class="org-node-avatar">
            <i class="bi bi-person-circle"></i>
            <span class="status-dot {{ $node['user']->investorStatus() }}"></span>
        </div>
        <div class="org-node-name">{{ $node['user']->name }}</div>
        <div class="org-node-invested">${{ number_format($node['invested'], 2) }}</div>
        <div class="org-node-referral">
            <span class="org-node-referral-chip"><i class="bi bi-link-45deg"></i>{{ $node['user']->referral_code }}</span>
        </div>
    </div>
    @if (count($node['children']))
        <ul>
            @foreach ($node['children'] as $child)
                @include('partials.team-tree-node-print', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
