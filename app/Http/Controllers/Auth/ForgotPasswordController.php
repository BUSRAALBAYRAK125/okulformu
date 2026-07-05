<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns', 'max:191'],
        ], [
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi gir.',
            'email.max' => 'E-posta en fazla 191 karakter olabilir.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Şifre sıfırlama bağlantısı e-posta adresine gönderildi.');
        }

        return back()->withErrors([
            'email' => 'Bu e-posta adresi için sıfırlama bağlantısı gönderilemedi.',
        ])->onlyInput('email');
    }
}