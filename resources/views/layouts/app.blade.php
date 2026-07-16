<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PowerNetGlobal — Grow Your Wealth Together')</title>
    <meta name="description" content="PowerNetGlobal is a global rewards and investment network offering direct rewards, level income, monthly profit and a 13-rank achievement ladder.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @include('partials.site-navbar')

    <main>
        @if (session('status'))
            <div class="container mt-3">
                <div class="alert alert-success">{{ session('status') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.site-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/[email protected]/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>
