@extends('layouts.guest')

@section('title', 'Yönetici Onayı Bekleniyor | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="register-page">
        <div class="register-layout">
            <div class="register-hero" data-reveal data-reveal-delay="0">
                <div>
                    <div class="register-hero__badge">
                        Yönetici Onayı
                    </div>

                    <h1 class="register-hero__title">
                        Hesabın oluşturuldu, şimdi yönetici onayı bekleniyor
                    </h1>

                    <p class="register-hero__text">
                        E-posta doğrulaman tamamlandıysa hesabın inceleme sırasına alınır.
                        Yönetici onayı verildikten sonra platformun iç alanlarına erişebileceksin.
                    </p>
                </div>

                <div class="register-hero__features">
                    <div class="register-feature">
                        <h3>Durum</h3>
                        <p>Hesabın henüz aktif değil.</p>
                    </div>

                    <div class="register-feature">
                        <h3>Ne olacak?</h3>
                        <p>Yönetici hesabını kontrol edip uygun görürse onaylayacak.</p>
                    </div>

                    <div class="register-feature">
                        <h3>Sonraki adım</h3>
                        <p>Onay sonrası giriş yaparak platformu kullanabileceksin.</p>
                    </div>
                </div>
            </div>

            <div class="register-panel" data-reveal data-reveal-delay="120">
                <div class="register-card">
                    <h2 class="register-card__title">Onay Bekleniyor</h2>
                    <p class="register-card__subtitle">
                        Şu an hesabın yönetici onayı aşamasında. Bu süreç tamamlanana kadar iç sayfalara erişim açılmayacaktır.
                    </p>

                    <a href="{{ route('home') }}" class="btn btn-primary">
                        Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection