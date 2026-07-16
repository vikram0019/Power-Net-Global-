<a href="{{ route('home') }}" class="brand">Power<span class="text-white">Net</span>Global</a>
<nav class="nav flex-column py-3">
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Overview
    </a>
    <a href="{{ route('wallet.index') }}" class="nav-link {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
        <i class="bi bi-wallet2"></i> Wallet
    </a>
    <a href="{{ route('income.index') }}" class="nav-link {{ request()->routeIs('income.*') ? 'active' : '' }}">
        <i class="bi bi-cash-coin"></i> Income
    </a>
    <a href="{{ route('team.index') }}" class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i> Team
    </a>
    <a href="{{ route('rank.index') }}" class="nav-link {{ request()->routeIs('rank.*') ? 'active' : '' }}">
        <i class="bi bi-trophy"></i> Rank Progress
    </a>
    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25">
        <a href="{{ route('home') }}" class="nav-link">
            <i class="bi bi-globe2"></i> Visit Website
        </a>
        @if(auth()->user()->is_admin)
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-shield-lock"></i> Admin Panel
        </a>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>
