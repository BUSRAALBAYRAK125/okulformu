<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginUserService
{
    public function handle(array $credentials, Request $request): void
    {
        $remember = $request->boolean('remember');

        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => 'E-posta veya şifre hatalı.',
            ]);
        }

        $request->session()->regenerate();
    }
}