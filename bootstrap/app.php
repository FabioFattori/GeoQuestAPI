<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $routesToSkip = [];

        if(env('DEBUG', true)){
            $routesToSkip = ["*"];
        }else{
            $routesToSkip = [
                '/api/user', 
                '/api/user/login',
            ];
        }

        // Exclude CSRF for a specific route or URI
        $middleware->validateCsrfTokens($routesToSkip);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
