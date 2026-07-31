<a href="{{ route('home') }}" class="brand justify-content-center">
    <img src="{{ asset('assets/img/logo.png') }}" alt="PowerNetGlobal" class="site-logo sm">
</a>
<nav class="nav flex-column py-3">
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('wallet.add-fund') }}" class="nav-link {{ request()->routeIs('wallet.add-fund') || request()->routeIs('wallet.fund-request') ? 'active' : '' }}">
        <i class="bi bi-plus-circle"></i> Add Fund
    </a>
    <a href="{{ route('wallet.withdraw.page') }}" class="nav-link {{ request()->routeIs('wallet.withdraw.page') || request()->routeIs('wallet.withdraw') || request()->routeIs('wallet.withdraw.verify-otp') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Withdrawal
    </a>
    <a href="{{ route('wallet.withdrawal-requests') }}" class="nav-link {{ request()->routeIs('wallet.withdrawal-requests') ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff"></i> Withdrawal Requests
    </a>
    <a href="{{ route('wallet.payment-history') }}" class="nav-link {{ request()->routeIs('wallet.payment-history') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> Payment History
    </a>
    <a href="{{ route('payment.index') }}" class="nav-link {{ request()->routeIs('payment.*') ? 'active' : '' }}">
        <i class="bi bi-cash-coin"></i> Payment
    </a>
    <a href="{{ route('team.index') }}" class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i> Team Network
    </a>
    <a href="{{ route('rank.index') }}" class="nav-link {{ request()->routeIs('rank.*') ? 'active' : '' }}">
        <i class="bi bi-trophy"></i> Rank &amp; Rewards Progress
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
