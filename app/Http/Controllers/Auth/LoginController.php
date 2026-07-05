<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Services\Auth\LoginUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(
    LoginUserRequest $request,
    LoginUserService $loginUserService
): RedirectResponse {
    $loginUserService->handle($request->validated(), $request);

    $user = $request->user();

    if (! $user->hasVerifiedEmail()) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Giriş yapmadan önce e-posta adresini doğrulamalısın.',
            ]);
    }

    if ($user->approval_status === 'rejected') {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Hesabın yönetici tarafından reddedildi.',
            ]);
    }

    if ($user->approval_status !== 'approved') {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('approval.pending')
            ->with('success', 'E-posta doğrulandı. Şimdi yönetici onayı bekleniyor.');
    }

    return redirect()->route('home');
}

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}