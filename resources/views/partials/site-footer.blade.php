<footer class="site-footer">
    <div class="site-container">
        <div class="site-footer__inner">
           <div class="site-footer__brand">
                <strong>Marmara Üniversitesi</strong>
                <span>Bilgisayar ve Öğretim Teknolojileri Bölümü öğrencileri tarafından yapılmıştır.</span>
            </div>

            <div class="site-footer__links">
                <a href="{{ route('home') }}#anasayfa">Ana Sayfa</a>
            <a href="{{ route('home') }}#bolum-hakkinda">Bölüm Hakkında</a>
            <a href="{{ route('home') }}#duyurular">Duyurular</a>
            <a href="{{ route('home') }}#iletisim">İletişim</a>
            </div>
            <div class="site-footer__legal">
                <a href="{{ route('legal.kvkk') }}">KVKK ve Açık Rıza Metni</a>
                <a href="{{ route('legal.aydinlatma') }}">Aydınlatma Metni</a>
                <a href="{{ route('legal.cerez') }}">Çerez Politikası</a>
            </div>
        </div>
    </div>
</footer>