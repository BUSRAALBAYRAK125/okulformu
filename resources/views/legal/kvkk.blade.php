@extends('layouts.legal')

@section('title', 'KVKK ve Açık Rıza Metni | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')
@section('legal_eyebrow', 'Yasal Metin')
@section('legal_title', 'KVKK ve Açık Rıza Metni')
@section('legal_description', 'Bu metin taslak olarak hazırlanmıştır. Nihai içerik, hukuki değerlendirme sonrasında güncellenmelidir.')

@section('legal_body')
    <div class="legal-content">
        <h2>1. Metnin Amacı</h2>
        <p>
            Bu metin, platform kapsamında gerçekleştirilebilecek belirli veri işleme faaliyetleri için
            kullanıcıdan açık rıza alınması gereken durumlarda kullanılmak üzere taslak olarak hazırlanmıştır.
            Platformun nihai işleyişine göre bu metnin kapsamı daraltılabilir, genişletilebilir veya tamamen değiştirilebilir.
        </p>

        <h2>2. Açık Rıza Gerektirebilecek İşlemler</h2>
        <p>
            Platformun yapısına göre aşağıdaki işlemler açık rıza kapsamına girebilir:
        </p>
        <p>
            Kullanıcıya bilgilendirme ve duyuru e-postaları gönderilmesi, isteğe bağlı topluluk bildirimlerinin iletilmesi,
            kullanıcı deneyimini geliştirmeye yönelik analiz çalışmalarının yürütülmesi, zorunlu olmayan çerezlerin kullanılması
            ve ileride platforma eklenebilecek benzeri işlevler.
        </p>

        <h2>3. Açık Rızanın Kapsamı</h2>
        <p>
            Kullanıcı, gerekli görülen durumlarda, kendisine sunulan ilgili onay alanı veya tercih ekranı aracılığıyla
            belirli veri işleme faaliyetlerine açık rıza verebilir. Açık rıza verilmemesi halinde,
            açık rızaya dayanan ilgili özellik veya hizmet kullanıcıya sunulmayabilir.
        </p>

        <h2>4. Açık Rızanın Geri Alınması</h2>
        <p>
            Kullanıcı, daha önce vermiş olduğu açık rızayı dilediği zaman geri alabilir.
            Açık rızanın geri alınması, geri alma işleminden önce gerçekleştirilen veri işleme faaliyetlerinin hukuka uygunluğunu etkilemez.
        </p>

        <h2>5. Zorunlu ve Zorunlu Olmayan İşlemler Ayrımı</h2>
        <p>
            Platformun çalışması için zorunlu olan veri işleme faaliyetleri ile kullanıcının tercihine bağlı olarak yürütülebilecek
            veri işleme faaliyetleri birbirinden ayrı değerlendirilecektir. Açık rıza yalnızca gerçekten gerekli olan durumlarda istenecektir.
        </p>

        <h2>6. Kullanıcı Beyanı</h2>
        <p>
            Kullanıcı, gerekli görülen ilgili alanlarda onay vermesi halinde; kendisine sunulan metni okuduğunu,
            hangi veri işleme faaliyeti için onay verdiğini anladığını ve bu onayı özgür iradesiyle verdiğini kabul eder.
        </p>

        <h2>7. Güncelleme</h2>
        <p>
            Bu metin taslak niteliğindedir. Platformun teknik yapısı, iletişim yöntemleri ve hukuki değerlendirmeler doğrultusunda
            güncellenebilir. Güncel metin bu sayfa üzerinden yayımlanır.
        </p>
    </div>
@endsection