<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        // Izinkan akses ke area admin untuk role admin, penulis, dan spmb
        if (! in_array($user->role, ['admin', 'penulis', 'spmb'])) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
