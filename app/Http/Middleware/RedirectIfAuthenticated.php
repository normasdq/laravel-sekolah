<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Adu Gagasan: Kita harus mengarahkan user berdasarkan identitasnya
                if (in_array($user->role, ['admin', 'spmb', 'penulis'])) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('dashboard'); // Dashboard untuk user biasa
            }
        }

        return $next($request);
    }
}
