<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

spl_autoload_register(static function (string $class): void {
    $prefix = 'Modules\\';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $basePath = dirname(__DIR__).DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR;
    $segments = explode(DIRECTORY_SEPARATOR, $relativeClass);
    $moduleName = $segments[0] ?? null;
    $moduleRelative = count($segments) > 1
        ? implode(DIRECTORY_SEPARATOR, array_slice($segments, 1))
        : null;

    $candidates = [
        $basePath.$relativeClass.'.php',
    ];

    // Fallback for nwidart module structure: Modules/{Module}/app/*
    if ($moduleName && $moduleRelative) {
        $candidates[] = $basePath.$moduleName.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$moduleRelative.'.php';
    }

    foreach ($candidates as $path) {
        if (is_string($path) && file_exists($path)) {
            $resolved = realpath($path) ?: $path;
            require_once $resolved;

            return;
        }
    }
});

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LogModuleActivity::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'assigned.role' => \App\Http\Middleware\EnsureUserHasAssignedRole::class,
            'pr.role'   => \Modules\PrSystem\Http\Middleware\PrRoleMiddleware::class,
            'qc.role'   => \Modules\QcComplaintSystem\Http\Middleware\QcRoleMiddleware::class,
            'ispo.role' => \Modules\SystemISPO\Http\Middleware\IspoRoleMiddleware::class,
            'sas.role'  => \Modules\ServiceAgreementSystem\Http\Middleware\SasRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
