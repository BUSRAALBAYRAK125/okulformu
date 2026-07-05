@extends('layouts.guest')

@section('title', 'Kayıt Ol | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="register-page">
        <div class="register-layout">
            <div class="register-hero" data-reveal data-reveal-delay="0">
                <div>
                    <div class="register-hero__badge">
                        Marmara Üniversitesi
                    </div>

                    <h1 class="register-hero__title">
                        Bölüm topluluğuna katıl,
                        bilgi paylaşımının parçası ol
                    </h1>

                    <p class="register-hero__text">
                        Bu platform; öğrenciler, mezunlar ve akademisyenler arasında
                        akademik paylaşımı, duyuruları ve topluluk etkileşimini
                        daha düzenli ve erişilebilir hale getirmek için oluşturulmuştur.
                    </p>
                </div>

                <div class="register-hero__features">
                    <div class="register-feature">
                        <h3>Öğrenciler</h3>
                        <p>Dersler, projeler, sınav süreçleri ve bölüm içi paylaşımlar için ortak alan.</p>
                    </div>

                    <div class="register-feature">
                        <h3>Mezunlar</h3>
                        <p>Deneyim aktarımı, kariyer rehberliği ve toplulukla bağın sürdürülmesi.</p>
                    </div>

                    <div class="register-feature">
                        <h3>Akademisyenler</h3>
                        <p>Duyurular, akademik bilgilendirmeler ve bölüm iletişimi için düzenli yapı.</p>
                    </div>
                </div>
            </div>

            <div class="register-panel" data-reveal data-reveal-delay="120">
                <div class="register-card">
                    <h2 class="register-card__title">Kayıt Oluştur</h2>
                    <p class="register-card__subtitle">
                        Kayıt sonrası e-posta doğrulaması gerekir. Hesabın daha sonra yönetici onayıyla aktif edilir.
                    </p>

                    <form
                        id="register-form"
                        method="POST"
                        action="{{ route('register.store') }}"
                        data-ajax-form="register"
                        novalidate
                    >
                        @csrf

                        <div class="form-alert" data-form-error @if (!$errors->has('form')) hidden @endif>
                            {{ $errors->first('form') }}
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="name">Ad</label>
                                <input
                                    class="form-control @error('name') is-invalid @enderror"
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Adını gir"
                                    required
                                >
                                <div class="error-text" data-error-for="name">
                                    @error('name'){{ $message }}@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="surname">Soyad</label>
                                <input
                                    class="form-control @error('surname') is-invalid @enderror"
                                    type="text"
                                    id="surname"
                                    name="surname"
                                    value="{{ old('surname') }}"
                                    placeholder="Soyadını gir"
                                    required
                                >
                                <div class="error-text" data-error-for="surname">
                                    @error('surname'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

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
                            <div class="info-text">Geçerli bir e-posta adresi girmen gerekiyor.</div>
                            <div class="error-text" data-error-for="email">
                                @error('email'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="user_type">Kullanıcı Tipi</label>
                                <select
                                    class="form-control @error('user_type') is-invalid @enderror"
                                    id="user_type"
                                    name="user_type"
                                    required
                                >
                                    <option value="">Seçiniz</option>
                                    <option value="student" {{ old('user_type') === 'student' ? 'selected' : '' }}>
                                        Öğrenci
                                    </option>
                                    <option value="graduate" {{ old('user_type') === 'graduate' ? 'selected' : '' }}>
                                        Mezun
                                    </option>
                                    <option value="academic" {{ old('user_type') === 'academic' ? 'selected' : '' }}>
                                        Akademisyen
                                    </option>
                                </select>
                                <div class="error-text" data-error-for="user_type">
                                    @error('user_type'){{ $message }}@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="student_no">Öğrenci Numarası</label>
                                <input
                                    class="form-control @error('student_no') is-invalid @enderror"
                                    type="text"
                                    id="student_no"
                                    name="student_no"
                                    value="{{ old('student_no') }}"
                                    placeholder="Varsa gir"
                                >
                                <div class="info-text">Öğrenci değilsen boş bırakabilirsin.</div>
                                <div class="error-text" data-error-for="student_no">
                                    @error('student_no'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="graduation_year">Mezuniyet Yılı</label>
                            <input
                                class="form-control @error('graduation_year') is-invalid @enderror"
                                type="number"
                                id="graduation_year"
                                name="graduation_year"
                                value="{{ old('graduation_year') }}"
                                placeholder="Örn: 2024"
                            >
                            <div class="info-text">Sadece mezunsan doldur.</div>
                            <div class="error-text" data-error-for="graduation_year">
                                @error('graduation_year'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="password">Şifre</label>
                                <input
                                    class="form-control @error('password') is-invalid @enderror"
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Şifreni oluştur"
                                    required
                                >
                                <div class="error-text" data-error-for="password">
                                    @error('password'){{ $message }}@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="password_confirmation">Şifre Tekrar</label>
                                <input
                                    class="form-control"
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Şifreni tekrar gir"
                                    required
                                >
                            </div>
                        </div>

                        <div class="register-check-group">
                            <label class="register-check @error('privacy_notice_approved') is-invalid @enderror">
                                <input
                                    type="checkbox"
                                    name="privacy_notice_approved"
                                    value="1"
                                    {{ old('privacy_notice_approved') ? 'checked' : '' }}
                                >
                                <span>
                                    <a href="{{ route('legal.aydinlatma') }}" target="_blank">Aydınlatma Metni</a>'ni okudum.
                                </span>
                            </label>
                            <div class="error-text" data-error-for="privacy_notice_approved">
                                @error('privacy_notice_approved'){{ $message }}@enderror
                            </div>

                            <label class="register-check @error('legal_texts_reviewed') is-invalid @enderror">
                                <input
                                    type="checkbox"
                                    name="legal_texts_reviewed"
                                    value="1"
                                    {{ old('legal_texts_reviewed') ? 'checked' : '' }}
                                >
                                <span>
                                    <a href="{{ route('legal.kvkk') }}" target="_blank">KVKK ve Açık Rıza Metni</a> ile
                                    <a href="{{ route('legal.cerez') }}" target="_blank">Çerez Politikası</a>
                                    sayfalarını inceledim, kabul ediyorum.
                                </span>
                            </label>
                            <div class="error-text" data-error-for="legal_texts_reviewed">
                                @error('legal_texts_reviewed'){{ $message }}@enderror
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit" data-submit-button>
                            Kaydı Tamamla
                        </button>

                        <div class="register-legal-note">
                            Yasal metinler taslak olarak hazırlanmıştır. Nihai içerikler daha sonra güncellenebilir.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection