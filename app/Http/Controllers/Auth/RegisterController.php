<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\RegisterUserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(
        RegisterUserRequest $request,
        RegisterUserService $registerUserService
    ): RedirectResponse|JsonResponse {
        $user = $registerUserService->handle($request->validated());

        event(new Registered($user));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Kayıt başarılı. E-posta doğrulama bağlantısı gönderildi.',
                'redirect' => route('register.notice'),
            ]);
        }

        return redirect()->route('register.notice');
    }
}