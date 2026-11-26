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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Os dados enviados são inválidos.',
                    'data' => null,
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if (($request->is('api/*') || $request->expectsJson()) && $e->getStatusCode() === 403) {
                return response()->json([
                    'message' => 'Você não tem autorização para executar esta ação.',
                    'data' => null,
                    'errors' => [],
                ], 403);
            }

            if (($request->is('api/*') || $request->expectsJson()) && $e->getStatusCode() === 422) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Os dados enviados são inválidos.',
                    'data' => null,
                    'errors' => [],
                ], 422);
            }
        });
    })->create();
