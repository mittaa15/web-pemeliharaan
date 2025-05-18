<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.common' => \App\Http\Middleware\CommonAuthMiddleware::class,
        'auth.admin' => \App\Http\Middleware\AdminMiddleware::class,
        'auth.sarpras' => \App\Http\Middleware\SarprasMiddleware::class,
        'auth.user' => \App\Http\Middleware\UserMiddleware::class
        // tambahkan middleware lain jika perlu
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();