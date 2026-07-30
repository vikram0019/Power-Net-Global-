@php
    $packageGroup = $rank ? strtolower($rank->package_group) : 'unranked';
    $icon = $rank?->icon ?? 'bi-person-badge';
    $size = $size ?? 'md';
@endphp
<span class="badge-group {{ $packageGroup }} rank-badge-pill rank-badge-{{ $size }}">
    <i class="bi {{ $icon }}"></i>{{ $rank->name ?? 'Unranked' }}
</span>
