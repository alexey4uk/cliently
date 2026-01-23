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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.plan.limit' => \App\Http\Middleware\CheckPlanLimit::class,
            'check.business.permission' => \App\Http\Middleware\CheckBusinessRolePermission::class,
        ]);
        
        // Добавляем middleware для принудительной смены пароля в начало группы web
        // Это важно, чтобы он выполнялся до других middleware, которые могут перенаправлять
        $middleware->web(prepend: [
            \App\Http\Middleware\RequirePasswordChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
