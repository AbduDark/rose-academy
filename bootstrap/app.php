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
    ->withMiddleware(function (Middleware $m): void {
        // Register middleware aliases in Laravel 11
        $m->alias([
            'admin'                => \App\Http\Middleware\AdminMiddleware::class,
            'role'                 => \App\Http\Middleware\RoleMiddleware::class,
            'check.subscription'   => \App\Http\Middleware\CheckSubscription::class,
            'gender.content'       => \App\Http\Middleware\GenderContentMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Exception handling configuration
    })->create();
