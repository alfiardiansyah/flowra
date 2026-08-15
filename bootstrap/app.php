<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\Access\AuthorizationException;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 404 Not Found (invalid URLs, deleted models, bad IDs)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Halaman yang Anda cari tidak ada / sudah dihapus.'], 404);
            }

            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();

            // Avoid infinite redirect loop if user directly opened invalid URL
            if ($previousUrl === $currentUrl || empty($previousUrl) || $previousUrl === url('/')) {
                return redirect()->route('dashboard')->with('error', 'Halaman yang Anda cari tidak ada / sudah dihapus.');
            }

            return redirect()->back()->with('error', 'Halaman yang Anda cari tidak ada / sudah dihapus.');
        });

        // Handle Model Not Found
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Halaman yang Anda cari tidak ada / sudah dihapus.'], 404);
            }

            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();

            if ($previousUrl === $currentUrl || empty($previousUrl) || $previousUrl === url('/')) {
                return redirect()->route('dashboard')->with('error', 'Halaman yang Anda cari tidak ada / sudah dihapus.');
            }

            return redirect()->back()->with('error', 'Halaman yang Anda cari tidak ada / sudah dihapus.');
        });

        // Handle Database Query Exceptions (e.g. Postgres 22P02 invalid integer syntax like /accounts/fggg)
        $exceptions->render(function (QueryException $e, Request $request) {
            $msg = $e->getMessage();
            $code = $e->getCode();

            if ($code === '22P02' || str_contains($msg, '22P02') || str_contains($msg, 'invalid input syntax')) {
                if ($request->is('api/*')) {
                    return response()->json(['message' => 'Halaman yang Anda cari tidak ada / sudah dihapus.'], 404);
                }

                $previousUrl = url()->previous();
                $currentUrl = $request->fullUrl();

                if ($previousUrl === $currentUrl || empty($previousUrl) || $previousUrl === url('/')) {
                    return redirect()->route('dashboard')->with('error', 'Halaman yang Anda cari tidak ada / sudah dihapus.');
                }

                return redirect()->back()->with('error', 'Halaman yang Anda cari tidak ada / sudah dihapus.');
            }
        });

        // Handle 403 Forbidden / Unauthorized Access
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Anda tidak memiliki hak akses ke halaman tersebut.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Anda tidak memiliki hak akses ke halaman tersebut.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        });
    })->create();

// Dynamic storage path if APP_STORAGE_PATH is specified
if (getenv('APP_STORAGE_PATH')) {
    $app->useStoragePath(getenv('APP_STORAGE_PATH'));
}

return $app;
