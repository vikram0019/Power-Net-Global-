<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
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
        // Some MySQL/MariaDB versions (common on shared hosting) cap index key
        // length below what a utf8mb4 VARCHAR(255) unique column needs. Capping
        // the default string length keeps indexed columns (email, etc.) within
        // that limit without touching every migration individually.
        Schema::defaultStringLength(191);

        // The app is styled with Bootstrap 5; without this, {{ $paginator->links() }}
        // falls back to Laravel's default Tailwind pagination view, whose SVG arrow
        // icons render at native size (huge) since no Tailwind CSS is loaded here.
        Paginator::useBootstrapFive();
    }
}
