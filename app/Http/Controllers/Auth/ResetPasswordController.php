<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'request' => $request,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:191'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'token.required' => 'Şifre sıfırlama anahtarı eksik.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi gir.',
            'email.max' => 'E-posta en fazla 191 karakter olabilir.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', 'Şifren başarıyla yenilendi. Şimdi giriş yapabilirsin.');
        }

        return back()->withErrors([
            'email' => 'Şifre sıfırlama bağlantısı geçersiz veya süresi dolmuş olabilir.',
        ])->withInput($request->only('email'));
    }
}