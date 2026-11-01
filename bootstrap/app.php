<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\IsSpmbMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. Redirect Guest ke Login secara Dinamis
        $middleware->redirectGuestsTo(function ($request) {
            // Jika URL mengandung kata 'admin', lempar ke login admin
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            // Jika selain itu (seperti /spmb), lempar ke login pendaftar
            return route('login');
        });

        // 2. Alias Middleware (Tetap seperti kode Anda)
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'check.role' => CheckRole::class,
            'is.spmb' => IsSpmbMiddleware::class,
        ]);

        // 3. Security Headers (Tetap)
        $middleware->append(SecurityHeaders::class);

        // 4. Exclude Webhook Callback dari CSRF
        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);

        // 5. Trust Proxies (Cloudflare, Load Balancer, Nginx Reverse Proxy)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
