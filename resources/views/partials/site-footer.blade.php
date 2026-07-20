<footer class="pt-5 pb-4" style="background: var(--png-navy-950); color: var(--png-ink-300);">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <img src="{{ asset('assets/img/logo.png') }}" alt="PowerNetGlobal" class="site-logo sm mb-2">
                <p class="small mb-0">A global rewards and investment network built on direct rewards, level income, monthly profit sharing, and a 13-rank achievement ladder.</p>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="text-white fw-bold mb-3">Company</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('about') }}" class="link-light text-decoration-none opacity-75">About Us</a></li>
                    <li class="mb-2"><a href="{{ route('service') }}" class="link-light text-decoration-none opacity-75">Services</a></li>
                    <li class="mb-2"><a href="{{ route('plan') }}" class="link-light text-decoration-none opacity-75">Our Plan</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="link-light text-decoration-none opacity-75">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="text-white fw-bold mb-3">Account</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('login') }}" class="link-light text-decoration-none opacity-75">Login</a></li>
                    <li class="mb-2"><a href="{{ route('signup') }}" class="link-light text-decoration-none opacity-75">Sign Up</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white fw-bold mb-3">Contact</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@powernetglobal.com</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i>+1 (000) 000-0000</li>
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Global Business Center</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="d-flex flex-wrap justify-content-between small opacity-75">
            <span>&copy; {{ date('Y') }} PowerNetGlobal. All rights reserved.</span>
            <span>Investment involves risk. Past performance does not guarantee future results.</span>
        </div>
    </div>
</footer>
