<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Resources\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            ForceJsonResponseMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            $isDev = app()->environment(['local', 'development', 'testing']);

            // Validation
            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    data: $e->errors(),
                    message: $isDev ? $e->getMessage() : 'Validation failed.',
                    status: 422,
                );
            }

            // Token expired
            if ($e instanceof TokenExpiredException) {
                return ApiResponse::error(
                    data: $isDev ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ] : null,
                    message: $isDev ? $e->getMessage() : 'Token expired.',
                    status: 401,
                );
            }

            // Token invalid (bad format/signature/etc)
            if ($e instanceof TokenInvalidException) {
                return ApiResponse::error(
                    data: $isDev ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ] : null,
                    message: $isDev ? $e->getMessage() : 'Token invalid.',
                    status: 401,
                );
            }

            // Token missing / not provided / not parsed
            if ($e instanceof JWTException) {
                return ApiResponse::error(
                    data: $isDev ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ] : null,
                    message: $isDev ? $e->getMessage() : 'Token not provided.',
                    status: 401,
                );
            }

            // Иногда отсутствие Bearer-токена/проблемы auth middleware приходят как UnauthorizedHttpException
            if ($e instanceof UnauthorizedHttpException || $e instanceof AuthenticationException) {
                return ApiResponse::error(
                    data: $isDev ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ] : null,
                    message: $isDev ? $e->getMessage() : 'Unauthenticated.',
                    status: 401,
                );
            }

            // Fallback 500
            return ApiResponse::error(
                data: $isDev ? [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ] : null,
                message: $isDev ? $e->getMessage() : 'Server error.',
                status: 500,
            );
        });
    })->create();
