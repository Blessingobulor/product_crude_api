<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware ) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $exception, $request) {
            if ($request->is('api/*')) {
                
                // Handle validation errors (let Laravel handle them naturally)
                if ($exception instanceof ValidationException) {
                    return null; // Let Laravel's default handler take over
                }
                
                // Handle authentication errors (401 - Unauthenticated)
                if ($exception instanceof AuthenticationException) {
                    return response()->json([
                        'message' => 'Unauthenticated'
                    ], 401);
                }
                
                // Handle route not found (when Sanctum tries to redirect to login)
                if ($exception instanceof RouteNotFoundException && 
                    str_contains($exception->getMessage(), 'login')) {
                    return response()->json([
                        'message' => 'Unauthenticated'
                    ], 401);
                }
                
                // Handle authorization errors (403 - Forbidden)
                if ($exception instanceof AuthorizationException) {
                    return response()->json([
                        'message' => 'Forbidden'
                    ], 403);
                }
                
                // Handle method not allowed (405)
                if ($exception instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'message' => 'Method not allowed'
                    ], 405);
                }
                
                // Handle model not found (404)
                if ($exception instanceof ModelNotFoundException) {
                    return response()->json([
                        'message' => 'Record not found'
                    ], 404);
                }
                
                // Handle general 404 errors
                if ($exception instanceof NotFoundHttpException) {
                    return response()->json([
                        'message' => 'Endpoint not found'
                    ], 404);
                }
                
                // Handle general server errors (500)
                return response()->json([
                    'message' => 'Internal server error'
                ], 500);
            }
        });
    })
    ->create();