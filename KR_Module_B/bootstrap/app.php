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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /* api force */
        $exceptions->shouldRenderJsonWhen(fn(\Illuminate\Http\Request $request) => $request->is('api/*'));

        /* 401 Unauthenticated */
        $exceptions->render(function(\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        });

        /* 422 Validation failed */
        $exceptions->render(function(\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => collect($e->errors())->map->first()
            ], 422);
        });

        /* 403 Forbidden */
        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        });

        /* 404 Not Found */
        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        });

    })->create();
