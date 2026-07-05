@extends('layouts.guest')

@section('title', 'Şifremi Unuttum | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="register-page">
        <div class="register-layout">
            <div class="register-hero" data-reveal data-reveal-delay="0">
                <div>
                    <div class="register-hero__badge">
                        Şifre Sıfırlama
                    </div>

                    <h1 class="register-hero__title">
                        Şifreni sıfırla,
                        hesabına yeniden eriş
                    </h1>

                    <p class="register-hero__text">
                        Hesabına ait e-posta adresini gir. Eğer sistemde kayıtlıysa,
                        şifreni yenilemen için sana bir bağlantı göndereceğiz.
                    </p>
                </div>

                <div class="register-hero__features">
                    <div class="register-feature">
                        <h3>1. E-postanı gir</h3>
                        <p>Hesabına ait kayıtlı e-posta adresini yaz.</p>
                    </div>

                    <div class="register-feature">
                        <h3>2. Maili kontrol et</h3>
                        <p>Şifre sıfırlama bağlantısı gelen kutuna gönderilir.</p>
                    </div>

                    <div class="register-feature">
                        <h3>3. Yeni şifre oluştur</h3>
                        <p>Bağlantıya tıklayıp yeni şifreni belirleyebilirsin.</p>
                    </div>
                </div>
            </div>

            <div class="register-panel" data-reveal data-reveal-delay="120">
                <div class="register-card">
                    <h2 class="register-card__title">Şifremi Unuttum</h2>
                    <p class="register-card__subtitle">
                        Şifre sıfırlama bağlantısı göndermek için e-posta adresini gir.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}" novalidate>
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

                        <button class="btn btn-primary" type="submit">
                            Sıfırlama Bağlantısı Gönder
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