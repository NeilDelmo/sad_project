<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as FrameworkVerifyCsrfToken;
use App\Http\Middleware\VerifyCsrfToken as AppVerifyCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->replace(
            FrameworkVerifyCsrfToken::class,
            AppVerifyCsrfToken::class
        );

        // Route middleware aliases
        $middleware->alias([
            'vendor.onboarded' => \App\Http\Middleware\EnsureVendorOnboarded::class,
        ]);

        // Add this line to apply UpdateLastSeen to web routes:
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
