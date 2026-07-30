@php
    $packageGroup = $rank ? strtolower($rank->package_group) : 'unranked';
    $rankIcons = [
        'star' => 'bi-star-fill',
        'eagle' => 'bi-award-fill',
        'diamond' => 'bi-gem',
        'crown' => 'bi-trophy-fill',
        'universal' => 'bi-globe-americas',
        'unranked' => 'bi-person-badge',
    ];
    $icon = $rankIcons[$packageGroup] ?? 'bi-person-badge';
    $size = $size ?? 'md';
@endphp
<span class="badge-group {{ $packageGroup }} rank-badge-pill rank-badge-{{ $size }}">
    <i class="bi {{ $icon }}"></i>{{ $rank->name ?? 'Unranked' }}
</span>
