<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicContactRequest;
use App\Models\Contact;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PublicContactController extends Controller
{
    public function store(PublicContactRequest $request)
    {
        $key = 'contact:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'message' => "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($key, 60);

        Contact::create($request->validated());

        return redirect()
            ->route('contact')
            ->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}
