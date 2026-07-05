@extends('layouts.guest')

@section('title', 'Giriş Yap | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="register-page">
        <div class="register-layout">
            <div class="register-hero" data-reveal data-reveal-delay="0">
                <div>
                    <div class="register-hero__badge">
                        Giriş Yap
                    </div>

                    <h1 class="register-hero__title">
                        Hesabına giriş yap,
                        bölüm topluluğuna yeniden katıl
                    </h1>

                    <p class="register-hero__text">
                        Doğrulanmış ve onaylanmış hesabınla giriş yaparak bölüm forumuna,
                        duyurulara ve topluluk alanlarına erişebilirsin.
                    </p>
                </div>

                <div class="register-hero__features">
                    <div class="register-feature">
                        <h3>E-posta Doğrulama</h3>
                        <p>Giriş sonrası doğrulanmamış hesaplar e-posta doğrulama ekranına yönlendirilir.</p>
                    </div>

                    <div class="register-feature">
                        <h3>Yönetici Onayı</h3>
                        <p>Onaylanmayan hesaplar iç alanlara erişmeden önce bekleme ekranında tutulur.</p>
                    </div>

                    <div class="register-feature">
                        <h3>Topluluk Erişimi</h3>
                        <p>Onaylı kullanıcılar forum ve bölüm içi alanlara erişebilir.</p>
                    </div>
                </div>
            </div>

            <div class="register-panel" data-reveal data-reveal-delay="120">
                <div class="register-card">
                    <h2 class="register-card__title">Giriş Yap</h2>
                    <p class="register-card__subtitle">
                        E-posta adresin ve şifrenle hesabına giriş yap.
                    </p>

                    <form method="POST" action="{{ route('login.store') }}" novalidate>
                        @csrf

                        <div class="form-group">
                            <label class="form-label" for="email">E-posta</label>
                            <input
                                class="form-control @error('email') is-invalid @enderror"
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="ornek@mail.com"
                                required
                            >
                            <div class="error-text">
                                @error('email'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Şifre</label>
                            <input
                                class="form-control @error('password') is-invalid @enderror"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Şifreni gir"
                                required
                            >
                            <div class="error-text">
                                @error('password'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="login-options">
                            <label class="login-remember">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <span>Beni hatırla</span>
                            </label>

                            <a href="{{ route('password.request') }}" class="login-forgot-link">
                                Şifremi unuttum
                            </a>
                        </div>

                        <button class="btn btn-primary" type="submit">
                            Giriş Yap
                        </button>

                        <div class="register-legal-note">
                            Henüz hesabın yoksa <a href="{{ route('register') }}">kayıt ol</a>.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection