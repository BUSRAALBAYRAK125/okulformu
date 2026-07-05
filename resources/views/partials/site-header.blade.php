<header class="site-header">
    <div class="site-container">
        <div class="site-header__inner">
            <a href="/" class="site-logo">
                <span class="site-logo__mark">M</span>
                <div class="site-logo__text">
                    <strong>Marmara Üniversitesi</strong>
                    <span>Bilgisayar ve Öğretim Teknolojileri Öğretmenliği Bölümü</span>
                </div>
            </a>

          <nav class="site-nav">
            <a href="{{ route('home') }}#anasayfa">Ana Sayfa</a>
            <a href="{{ route('home') }}#bolum-hakkinda">Bölüm Hakkında</a>
            <a href="{{ route('home') }}#duyurular">Duyurular</a>
            <a href="{{ route('home') }}#iletisim">İletişim</a>
          </nav>
          
        <div class="site-header__actions">
            <a href="{{ route('login') }}" class="btn btn-secondary site-header__btn">Giriş Yap</a>
            <a href="{{ route('register') }}" class="btn btn-primary site-header__btn">Kayıt Ol</a>
        </div>
        </div>
    </div>
</header>