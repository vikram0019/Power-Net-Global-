<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — PowerNetGlobal</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <aside class="app-sidebar d-none d-lg-block">
            <a href="{{ route('admin.dashboard') }}" class="brand justify-content-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="PowerNetGlobal" class="site-logo sm">
            </a>
            <nav class="nav flex-column py-3">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="{{ route('admin.users.create-dummy') }}" class="nav-link {{ request()->routeIs('admin.users.create-dummy') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i> Create Dummy User
                </a>
                <a href="{{ route('admin.fund-requests.index') }}" class="nav-link {{ request()->routeIs('admin.fund-requests.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Fund Requests
                </a>
                <a href="{{ route('admin.withdrawals.index') }}" class="nav-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Withdrawals
                </a>
                <a href="{{ route('admin.payment-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.payment-settings.*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code"></i> Payment Settings
                </a>
                <a href="{{ route('admin.ranks.index') }}" class="nav-link {{ request()->routeIs('admin.ranks.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i> Rank Log
                </a>
                <a href="{{ route('admin.roi.index') }}" class="nav-link {{ request()->routeIs('admin.roi.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i> Run ROI
                </a>
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-25">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="bi bi-arrow-left-circle"></i> Member Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="app-main">
            <div class="app-topbar d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">@yield('page-title', 'Admin')</h5>
                <span class="badge bg-dark">{{ auth()->user()->name }}</span>
            </div>
            <div class="app-content">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>
