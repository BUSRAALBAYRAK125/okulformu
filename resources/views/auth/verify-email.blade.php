@extends('layouts.guest')

@section('title', 'E-Posta Doğrulama | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="register-page">
        <div class="register-layout">
            <div class="register-hero" data-reveal data-reveal-delay="0">
                <div>
                    <div class="register-hero__badge">
                        E-Posta Doğrulama
                    </div>

                    <h1 class="register-hero__title">
                        Hesabını aktifleştirmek için e-posta doğrulamasını tamamla
                    </h1>

                    <p class="register-hero__text">
                        Kayıt işlemin alındı. Şimdi e-posta adresine gönderilen doğrulama bağlantısına
                        tıklaman gerekiyor. Doğrulama tamamlandıktan sonra hesabın yönetici onayına düşecek.
                    </p>
                </div>

                <div class="register-hero__features">
                    <div class="register-feature">
                        <h3>1. E-postanı kontrol et</h3>
                        <p>Kayıt sırasında girdiğin adrese gönderilen doğrulama bağlantısını bul.</p>
                    </div>

                    <div class="register-feature">
                        <h3>2. Bağlantıya tıkla</h3>
                        <p>Doğrulama işlemi tamamlandıktan sonra sistem hesabını doğrulanmış olarak işaretler.</p>
                    </div>

                    <div class="register-feature">
                        <h3>3. Yönetici onayını bekle</h3>
                        <p>Hesabın doğrulansa bile yönetici onayı olmadan tam erişim açılmayacak.</p>
                    </div>
                </div>
            </div>

            <div class="register-panel" data-reveal data-reveal-delay="120">
                <div class="register-card">
                    <h2 class="register-card__title">Doğrulama Bekleniyor</h2>
                    <p class="register-card__subtitle">
                        E-posta gelmediyse aşağıdaki butonla tekrar gönderebilirsin.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">
                            Doğrulama Bağlantısını Tekrar Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection