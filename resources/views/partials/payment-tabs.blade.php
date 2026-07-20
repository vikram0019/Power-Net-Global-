<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('payment.roi') }}" class="wallet-tab-btn {{ request()->routeIs('payment.roi') ? 'active' : '' }} text-decoration-none">
        <i class="bi bi-graph-up-arrow me-1"></i> Monthly ROI Income
    </a>
    <a href="{{ route('payment.rank-reward') }}" class="wallet-tab-btn {{ request()->routeIs('payment.rank-reward') ? 'active' : '' }} text-decoration-none">
        <i class="bi bi-trophy me-1"></i> Ranks &amp; Reward Income
    </a>
    <a href="{{ route('payment.level') }}" class="wallet-tab-btn {{ request()->routeIs('payment.level') ? 'active' : '' }} text-decoration-none">
        <i class="bi bi-diagram-3 me-1"></i> Level Income
    </a>
    <a href="{{ route('payment.direct') }}" class="wallet-tab-btn {{ request()->routeIs('payment.direct') ? 'active' : '' }} text-decoration-none">
        <i class="bi bi-person-check me-1"></i> Direct Income
    </a>
</div>
