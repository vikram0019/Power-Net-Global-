<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — PowerNetGlobal</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="app-shell" x-data="{ sidebarOpen: false }">
        <aside class="app-sidebar d-none d-lg-block">
            @include('partials.dashboard-sidebar')
        </aside>

        <div class="offcanvas offcanvas-start d-lg-none" style="background: var(--png-navy-950);" tabindex="-1" id="mobileSidebar">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="offcanvas"></button>
            @include('partials.dashboard-sidebar')
        </div>

        <div class="app-main">
            <div class="app-topbar d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @include('partials.rank-badge', ['rank' => auth()->user()->currentRank, 'size' => 'lg'])
                    <div class="dropdown">
                        <button class="btn btn-sm btn-navy dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="app-content">
                @if (auth()->user()->status === 'approval_pending')
                    <div class="alert alert-warning d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                        <div>
                            <strong>Account pending admin approval.</strong>
                            You can use your wallet and invest normally, but your referral code won't work for new signups until an admin approves your account.
                        </div>
                    </div>
                @endif
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
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>
