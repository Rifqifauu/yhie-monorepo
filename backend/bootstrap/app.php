<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
        ->withMiddleware(function (Middleware $middleware) {
    $middleware->statefulApi(); // Tambahkan baris ini
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
    // Backend ini murni JSON API tanpa halaman login berbasis Blade, jadi
    // request tak-terautentikasi jangan dicoba redirect ke route "login" yang
    // tidak pernah didaftarkan (itu akan crash jadi 500) - biarkan selalu 401 JSON.
    $middleware->redirectGuestsTo(fn () => null);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, $throwable) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
