@extends('layouts.guest')

@section('title', 'Yeni Şifre Oluştur | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="register-page">
        <div class="register-layout">
            <div class="register-hero" data-reveal data-reveal-delay="0">
                <div>
                    <div class="register-hero__badge">
                        Yeni Şifre Oluştur
                    </div>

                    <h1 class="register-hero__title">
                        Hesabın için yeni bir şifre belirle
                    </h1>

                    <p class="register-hero__text">
                        E-posta adresini ve yeni şifreni girerek hesabının şifresini yenileyebilirsin.
                    </p>
                </div>

                <div class="register-hero__features">
                    <div class="register-feature">
                        <h3>1. E-postanı doğrula</h3>
                        <p>Şifre sıfırlama bağlantısının ait olduğu e-posta adresini kontrol et.</p>
                    </div>

                    <div class="register-feature">
                        <h3>2. Yeni şifre oluştur</h3>
                        <p>Güçlü ve tahmin edilmesi zor bir şifre belirle.</p>
                    </div>

                    <div class="register-feature">
                        <h3>3. Giriş yap</h3>
                        <p>Şifren yenilendikten sonra yeni bilgilerinle giriş yapabilirsin.</p>
                    </div>
                </div>
            </div>

            <div class="register-panel" data-reveal data-reveal-delay="120">
                <div class="register-card">
                    <h2 class="register-card__title">Şifreyi Yenile</h2>
                    <p class="register-card__subtitle">
                        Aşağıdaki alanları doldurarak yeni şifreni oluştur.
                    </p>

                    <form method="POST" action="{{ route('password.update') }}" novalidate>
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="form-group">
                            <label class="form-label" for="email">E-posta</label>
                            <input
                                class="form-control @error('email') is-invalid @enderror"
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                placeholder="ornek@mail.com"
                                required
                            >
                            <div class="error-text">
                                @error('email'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Yeni Şifre</label>
                            <input
                                class="form-control @error('password') is-invalid @enderror"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Yeni şifreni gir"
                                required
                            >
                            <div class="error-text">
                                @error('password'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Yeni Şifre Tekrar</label>
                            <input
                                class="form-control"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Yeni şifreni tekrar gir"
                                required
                            >
                        </div>

                        <button class="btn btn-primary" type="submit">
                            Şifreyi Güncelle
                        </button>

                        <div class="register-legal-note">
                            Şifreni hatırladıysan <a href="{{ route('login') }}">giriş yap</a>.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection