<?php

use App\Http\Controllers\Admin\ClubApprovalController;
use App\Http\Controllers\Admin\PendingApprovalController;
use App\Http\Controllers\Admin\PostApprovalController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserConnectionController;
use App\Http\Controllers\UserNotificationController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::get('/kayit-basarili', function () {
    return 'Kayıt alındı. E-posta adresini doğrula. Hesabın admin onayından sonra aktif olacaktır.';
})->name('register.notice');

Route::get('/email/verify/{id}/{hash}', function (string $id, string $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect()
        ->route('login')
        ->with('success', 'E-posta adresin doğrulandı. Şimdi giriş yapıp yönetici onayını bekleyebilirsin.');
})->middleware('signed')->name('verification.verify');

Route::view('/kvkk', 'legal.kvkk')->name('legal.kvkk');
Route::view('/aydinlatma-metni', 'legal.aydinlatma')->name('legal.aydinlatma');
Route::view('/cerez-politikasi', 'legal.cerez')->name('legal.cerez');

Route::get('/onay-bekleniyor', function () {
    return view('auth.pending-approval');
})->name('approval.pending');

Route::middleware('auth')->group(function () {
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Doğrulama bağlantısı tekrar gönderildi.');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/onay-bekleyenler', [PendingApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/users/{user}/approve', [PendingApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/users/{user}/reject', [PendingApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('/posts/onay-bekleyenler', [PostApprovalController::class, 'index'])->name('posts.pending');
    Route::post('/posts/{post}/approve', [PostApprovalController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject', [PostApprovalController::class, 'reject'])->name('posts.reject');

    Route::get('/clubs/onay-bekleyenler', [ClubApprovalController::class, 'pending'])->name('clubs.pending');
    Route::patch('/clubs/{club}/approve', [ClubApprovalController::class, 'approve'])->name('clubs.approve');
    Route::patch('/clubs/{club}/reject', [ClubApprovalController::class, 'reject'])->name('clubs.reject');

    Route::post('/posts/{post}/toggle-pin', [PostController::class, 'togglePin'])->name('posts.togglePin');
});

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/panel', function () {
        return 'Onaylı kullanıcı paneli';
    })->name('panel');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/kaydedilen-gonderiler', [PostController::class, 'saved'])->name('posts.saved');
    Route::get('/benim-gonderilerim', [PostController::class, 'myPosts'])->name('posts.my');
    Route::post('/posts/{post}/toggle-active', [PostController::class, 'toggleActive'])->name('posts.toggleActive');
    Route::post('/posts/{post}/toggle-save', [PostController::class, 'toggleSave'])->name('posts.toggleSave');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/profil/duzenle', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil/duzenle', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/sifre-sifirlama-linki-gonder', [ProfileController::class, 'sendPasswordResetLink'])->name('profile.password.reset.link');

    Route::post('/profil/{user}/baglanti-istegi-gonder', [UserConnectionController::class, 'send'])->name('connections.send');
    Route::post('/baglantilar/{connection}/kabul-et', [UserConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/baglantilar/{connection}/reddet', [UserConnectionController::class, 'reject'])->name('connections.reject');
    Route::post('/baglantilar/{connection}/geri-cek', [UserConnectionController::class, 'cancel'])->name('connections.cancel');
    Route::post('/baglantilar/{connection}/baglantiyi-kes', [UserConnectionController::class, 'disconnect'])->name('connections.disconnect');

    Route::get('/bildirimler', [UserNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/bildirimler/{notification}/git', [UserNotificationController::class, 'go'])->name('notifications.go');
    Route::post('/bildirimler/{notification}/okundu', [UserNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/bildirimler/tumunu-okundu-yap', [UserNotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::delete('/bildirimler/{notification}', [UserNotificationController::class, 'destroy'])->name('notifications.destroy');
});

Route::get('/profil/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::prefix('clubs')->name('clubs.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/create', [ClubController::class, 'create'])->name('create');
        Route::post('/', [ClubController::class, 'store'])->name('store');
        Route::post('/{club}/join-request', [ClubController::class, 'joinRequest'])->name('join-request');
        Route::get('/{club}/edit', [ClubController::class, 'edit'])->name('edit');
        Route::put('/{club}', [ClubController::class, 'update'])->name('update');
        Route::delete('/{club}', [ClubController::class, 'destroy'])->name('destroy');
        Route::patch('/{club}/members/{clubMember}/approve', [ClubController::class, 'approveMember'])->name('members.approve');
        Route::patch('/{club}/members/{clubMember}/reject', [ClubController::class, 'rejectMember'])->name('members.reject');
    });

    Route::get('/{club}', [ClubController::class, 'show'])->name('show');
});