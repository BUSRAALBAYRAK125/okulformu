@extends('layouts.guest')

@section('title', 'Ana Sayfa | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="home-hero" id="anasayfa">
        <div class="site-container">
            <div class="home-hero__layout">
                <div class="home-hero__content" data-reveal data-reveal-delay="0">
                    <span class="home-hero__badge">Akademik Topluluk Platformu</span>

                    <h1 class="home-hero__title">
                        Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü için
                        ortak paylaşım ve duyuru alanı
                    </h1>

                    <p class="home-hero__text">
                        Öğrenciler, mezunlar ve akademisyenler arasında bilgi paylaşımını güçlendiren,
                        bölüm duyurularını görünür kılan ve topluluk etkileşimini destekleyen
                        dijital bir buluşma alanı.
                    </p>

                    <div class="home-hero__actions">
                        <a href="{{ route('register') }}" class="btn btn-primary home-hero__btn">
                            Topluluğa Katıl
                        </a>
                    </div>
                </div>

                <div class="home-hero__panel" data-reveal data-reveal-delay="120">
                    <div class="home-hero__card">
                        <h2>Bu platformda neler var?</h2>

                        <div class="home-hero__items">
                            <div class="home-hero__item">
                                <strong>Duyurular</strong>
                                <span>Bölümle ilgili güncel bilgilendirmeler ve önemli gelişmeler.</span>
                            </div>

                            <div class="home-hero__item">
                                <strong>Akademik Paylaşım</strong>
                                <span>Dersler, projeler, sınav süreçleri ve bölüm içi içerikler.</span>
                            </div>

                            <div class="home-hero__item">
                                <strong>Topluluk Desteği</strong>
                                <span>Öğrenci, mezun ve akademisyen etkileşimini güçlendiren yapı.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-about" id="bolum-hakkinda">
        <div class="site-container">
            <div class="section-heading" data-reveal data-reveal-delay="0">
                <div>
                    <span class="section-heading__eyebrow">Bölüm Hakkında</span>
                    <h2 class="section-heading__title">
                        Bilgisayar ve Öğretim Teknolojileri Bölümü topluluğunu bir araya getiren dijital alan
                    </h2>
                    <p class="section-heading__text">
                        Bu platform; bölüm öğrencileri, mezunları ve akademisyenleri arasında bilgi paylaşımını,
                        akademik iletişimi ve topluluk kültürünü güçlendirmek amacıyla geliştirilmektedir.
                    </p>
                </div>
            </div>

            <div class="about-grid">
                <div class="about-card" data-reveal data-reveal-delay="0">
                    <h3>Akademik Paylaşım</h3>
                    <p>Ders, proje, sınav ve bölüm içi içeriklerin daha düzenli şekilde paylaşılması hedeflenir.</p>
                </div>

                <div class="about-card" data-reveal data-reveal-delay="120">
                    <h3>Mezun Katkısı</h3>
                    <p>Mezunların deneyimlerini aktardığı, öğrencilere yol gösterdiği sürdürülebilir bir yapı amaçlanır.</p>
                </div>

                <div class="about-card" data-reveal data-reveal-delay="240">
                    <h3>Bölüm Duyuruları</h3>
                    <p>Genel bilgilendirmeler ve öne çıkan gelişmelerin giriş yapmadan da görüntülenebilmesi planlanır.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="home-announcements" id="duyurular">
        <div class="site-container">
            <div class="section-heading" data-reveal data-reveal-delay="0">
                <div>
                    <span class="section-heading__eyebrow">Genel Duyurular</span>
                    <h2 class="section-heading__title">Bölümden öne çıkan paylaşımlar</h2>
                    <p class="section-heading__text">
                        Giriş yapmadan görüntülenebilen duyuru ve bilgilendirmeler.
                    </p>
                </div>
            </div>

            <div class="announcement-grid">
                <article class="announcement-card" data-reveal data-reveal-delay="0">
                    <span class="announcement-card__tag">Duyuru</span>
                    <h3 class="announcement-card__title">Bölüm forumu yayına hazırlanıyor</h3>
                    <p class="announcement-card__text">
                        Öğrenci, mezun ve akademisyenleri bir araya getiren yeni bölüm platformu için çalışmalar devam ediyor.
                    </p>
                    <div class="announcement-card__meta">12 Nisan 2026</div>
                </article>

                <article class="announcement-card" data-reveal data-reveal-delay="120">
                    <span class="announcement-card__tag">Etkinlik</span>
                    <h3 class="announcement-card__title">Akademik paylaşım ve proje alanları eklenecek</h3>
                    <p class="announcement-card__text">
                        Ders, proje, staj ve mezun deneyim paylaşımları için yeni içerik alanları planlanıyor.
                    </p>
                    <div class="announcement-card__meta">15 Nisan 2026</div>
                </article>

                <article class="announcement-card" data-reveal data-reveal-delay="240">
                    <span class="announcement-card__tag">Bilgilendirme</span>
                    <h3 class="announcement-card__title">Topluluk odaklı açık bir bölüm sayfası oluşturuluyor</h3>
                    <p class="announcement-card__text">
                        Bölüm dışından gelen ziyaretçiler de genel duyuruları ve bölüm tanıtım içeriklerini görüntüleyebilecek.
                    </p>
                    <div class="announcement-card__meta">18 Nisan 2026</div>
                </article>
            </div>
        </div>
    </section>

    <section class="home-contact" id="iletisim">
        <div class="site-container">
            <div class="section-heading" data-reveal data-reveal-delay="0">
                <div>
                    <span class="section-heading__eyebrow">İletişim</span>
                    <h2 class="section-heading__title">Bölümle ve platformla iletişim</h2>
                    <p class="section-heading__text">
                        Bölümle ilgili genel bilgilendirmeler, platform önerileri ve topluluk geri bildirimleri için
                        bu alan kullanılacaktır.
                    </p>
                </div>
            </div>

            <div class="contact-card" data-reveal data-reveal-delay="120">
                <div class="contact-card__item">
                    <strong>E-posta</strong>
                    <span>botef@marmara.edu.tr</span>
                </div>

                <div class="contact-card__item">
                    <strong>Konum</strong>
                    <span>Marmara Üniversitesi</span>
                </div>

                <div class="contact-card__item">
                    <strong>Platform Notu</strong>
                    <span>Bu platform Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü öğrencileri tarafından geliştirilmektedir.</span>
                </div>
            </div>
        </div>
    </section>
@endsection