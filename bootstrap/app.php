<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A form (e.g. Logout) submitted from a page left open past the
        // session lifetime shows Laravel's bare "Page Expired" screen by
        // default. Redirect back to login with a friendly message instead.
        // Laravel's own Handler::prepareException() converts
        // TokenMismatchException into a plain HttpException(419, ...)
        // before any render() callback runs, so this must match on the
        // converted HttpException + status code, not the original class.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->route('login')
                    ->with('status', 'Your session had expired. Please log in again.');
            }
        });
    })->create();
