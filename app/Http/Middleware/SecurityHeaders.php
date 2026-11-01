<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // HSTS hanya diaktifkan jika request melalui HTTPS atau di environment production
        if ($request->isSecure() || app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $cdn_domains = implode(' ', [
            'https://cdn.tailwindcss.com',
            'https://cdn.jsdelivr.net',
            'https://cdn.tiny.cloud',
            'https://cdnjs.cloudflare.com',
        ]);

        $midtrans = implode(' ', [
            'https://app.sandbox.midtrans.com',
            'https://app.midtrans.com',
            'https://api.sandbox.midtrans.com',
            'https://api.midtrans.com',
        ]);

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' data: {$cdn_domains} {$midtrans}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com {$cdn_domains} {$midtrans}",
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob: https: {$midtrans}",
            "frame-src 'self' https://www.google.com https://www.google.co.id {$midtrans}",
            "connect-src 'self' {$cdn_domains} {$midtrans}",
            "worker-src 'self' blob:",
        ];

        if ($request->isSecure() || app()->isProduction()) {
            $csp[] = 'upgrade-insecure-requests';
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
