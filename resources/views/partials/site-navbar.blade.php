<nav class="navbar navbar-expand-lg navbar-png sticky-top py-2">
    <div class="container">
        <a class="navbar-brand fs-4 py-0" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo.png') }}" alt="PowerNetGlobal" class="site-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('service') ? 'active' : '' }}" href="{{ route('service') }}">Service</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('plan') ? 'active' : '' }}" href="{{ route('plan') }}">Plan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a></li>
                @auth
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-gold btn-sm px-3">Dashboard</a>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('signup') }}" class="btn btn-gold btn-sm px-3">Join Now</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
