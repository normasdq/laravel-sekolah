<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSpmbMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan user sudah login
        if (auth()->check() === false) {
            return redirect()->route('admin.login');
        }

        // 2. Cek Role: Hanya Admin dan SPMB yang boleh lewat
        // Kita cek manual tanpa rumit
        $userRole = auth()->user()->role;

        if ($userRole !== 'admin' && $userRole !== 'spmb') {
            // Jika bukan admin dan bukan spmb, tolak
            abort(403, 'Akses ditolak. Halaman ini khusus Admin dan Panitia SPMB.');
        }

        return $next($request);
    }
}
