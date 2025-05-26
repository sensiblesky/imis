<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Workspace\EnsureUserBelongsToWorkspace;
use App\Http\Middleware\Base\MaintenanceMode;
use App\Http\Middleware\Base\CustomCors;
use App\Http\Middleware\Base\ScreenLockMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //


         // Register your route middleware here
         $middleware->alias([
            'workspace.access' => EnsureUserBelongsToWorkspace::class,
            'maintenance' => MaintenanceMode::class,
            'cors' => CustomCors::class,
            'screen.lock' => ScreenLockMiddleware::class,
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();


    