<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The app is styled with Bootstrap 5; without this, {{ $paginator->links() }}
        // falls back to Laravel's default Tailwind pagination view, whose SVG arrow
        // icons render at native size (huge) since no Tailwind CSS is loaded here.
        Paginator::useBootstrapFive();
    }
}
