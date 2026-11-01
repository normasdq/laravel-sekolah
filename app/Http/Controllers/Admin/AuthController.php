<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        // Cek apakah sedang dalam status banned karena 3x salah login
        $throttleKey = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return view('admin.auth.login', [
                'banned' => true,
                'wait_time' => $seconds,
            ]);
        }

        return view('admin.auth.login', [
            'banned' => false,
            'wait_time' => 0,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = $this->throttleKey();

        // Cek apakah sedang di-ban (sudah salah 3x)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login salah. Silakan tunggu {$seconds} detik lagi.",
            ]);
        }

        // Cek kredensial
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Tambah hitungan salah
            RateLimiter::hit($throttleKey, 120); // 120 detik = 2 menit

            $attemptsLeft = RateLimiter::remaining($throttleKey, 3);

            throw ValidationException::withMessages([
                'email' => "Email atau password salah. Sisa percobaan: {$attemptsLeft} kali.",
            ]);
        }

        // Jika berhasil login, hapus record hitungan salah
        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Membuat key unik berbasis kombinasi email dan IP address
     */
    private function throttleKey(): string
    {
        $email = strtolower((string) request('email'));

        return 'admin_login|'.$email.'|'.request()->ip();
    }
}
