@extends('layouts.forum')

@section('title', 'Profil Ayarları | Forum')

@section('content')
@php
    $profile = $user->profile;
    $privacy = $user->privacySetting;
    $notificationSetting = $user->notificationSetting;
    $interestItems = $user->items->where('type', 'interest')->pluck('value')->filter()->values();

    $profilePhoto = !empty($profile?->photo) ? asset($profile->photo) : null;
    $coverPhoto = !empty($profile?->cover_photo) ? asset($profile->cover_photo) : null;

    $interestSeed = old('interests', $interestItems->implode(', '));

    $checkedSetting = function (string $field, bool $default = false) use ($notificationSetting) {
        $value = old($field, data_get($notificationSetting, $field, $default ? 1 : 0));

        if (is_bool($value)) {
            return $value;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    };
@endphp

<section class="forum-feed-page profile-edit-page">
    <div class="forum-feed forum-feed--detail profile-edit-layout">
        <div class="forum-post-card forum-post-card--reddit profile-edit-card">
            <div class="profile-edit-hero">
                <div class="profile-edit-hero__content">
                    <div>
                        <h1 class="profile-edit-hero__title">Profil Ayarları</h1>
                        <p class="profile-edit-hero__text">
                            Profil görünümünü, bağlantılarını ve gizlilik tercihlerini buradan yönet.
                        </p>
                    </div>

                    <a href="{{ route('profile.show', $user) }}"
                       class="forum-post-card__action forum-post-card__action--icon profile-edit-hero__back">
                        Profile Dön
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="profile-edit-alert profile-edit-alert--success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any() && !$errors->has('password_reset'))
                <div class="profile-edit-alert profile-edit-alert--error">
                    <strong>Formda hata var.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            @if($error !== $errors->first('password_reset'))
                                <li>{{ $error }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
<div class="profile-edit-tabs">
    <a href="{{ route('profile.edit', ['tab' => 'general']) }}"
       class="forum-post-card__action forum-post-card__action--icon {{ $activeEditTab === 'general' ? 'forum-post-card__action--primary' : '' }}">
        Genel
    </a>

    <a href="{{ route('profile.edit', ['tab' => 'media']) }}"
       class="forum-post-card__action forum-post-card__action--icon {{ $activeEditTab === 'media' ? 'forum-post-card__action--primary' : '' }}">
        Görseller
    </a>

    <a href="{{ route('profile.edit', ['tab' => 'privacy']) }}"
       class="forum-post-card__action forum-post-card__action--icon {{ $activeEditTab === 'privacy' ? 'forum-post-card__action--primary' : '' }}">
        Gizlilik
    </a>

    <a href="{{ route('profile.edit', ['tab' => 'notifications']) }}"
       class="forum-post-card__action forum-post-card__action--icon {{ $activeEditTab === 'notifications' ? 'forum-post-card__action--primary' : '' }}">
        Bildirimler
    </a>
</div>

                <div class="profile-edit-content">
                    <div data-edit-tab-panel="general" {{ $activeEditTab !== 'general' ? 'hidden' : '' }}>
                        <div class="profile-edit-grid">
                            <section class="profile-edit-section">
                                <div class="profile-edit-section__head">
                                    <h2>Temel Bilgiler</h2>
                                </div>

                                <div class="profile-edit-section__body profile-edit-form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Ad</label>
                                        <input type="text" class="form-control" value="{{ old('name', $user->name) }}" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Soyad</label>
                                        <input type="text" class="form-control" value="{{ old('surname', $user->surname) }}" disabled>
                                    </div>

                                    <div class="form-group profile-edit-col-full">
                                        <label class="form-label">Başlık / Kısa Tanıtım</label>
                                        <input
                                            type="text"
                                            name="headline"
                                            class="form-control"
                                            value="{{ old('headline', $profile?->headline) }}"
                                            placeholder="Örn: Web geliştirme ve eğitim teknolojileri ile ilgileniyorum"
                                        >
                                    </div>

                                    <div class="form-group profile-edit-col-full">
                                        <label class="form-label">Biyografi</label>
                                        <textarea
                                            name="bio"
                                            rows="6"
                                            class="form-control"
                                            placeholder="Kendinden kısaca bahset..."
                                        >{{ old('bio', $profile?->bio) }}</textarea>
                                    </div>
                                </div>
                            </section>

                            <section class="profile-edit-section">
                                <div class="profile-edit-section__head">
                                    <h2>İlgi Alanları ve Bağlantılar</h2>
                                </div>

                                <div class="profile-edit-section__body profile-edit-stack">
                                    <div class="form-group">
                                        <label class="form-label">İlgi Alanları</label>

                                        <input type="hidden" name="interests" id="interests-hidden-input" value="{{ $interestSeed }}">

                                        <div class="profile-interest-box">
                                            <div id="interest-chip-list" class="profile-interest-chip-list"></div>

                                            <div class="profile-interest-input-row">
                                                <input
                                                    type="text"
                                                    id="interest-chip-input"
                                                    class="form-control"
                                                    placeholder="#laravel, #yapayzeka, #egitimteknolojileri"
                                                >

                                                <button
                                                    type="button"
                                                    id="interest-chip-add-btn"
                                                    class="forum-post-card__action forum-post-card__action--icon forum-post-card__action--primary">
                                                    + Ekle
                                                </button>
                                            </div>

                                            <small class="profile-edit-help">
                                                Etiketi yazıp ekle. Kaydedince filtreleme ve arama için kullanılacak.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="profile-edit-form-grid">
                                        <div class="form-group">
                                            <label class="form-label">LinkedIn</label>
                                            <input
                                                type="url"
                                                name="linkedin_url"
                                                class="form-control"
                                                value="{{ old('linkedin_url', $profile?->linkedin_url) }}"
                                                placeholder="https://linkedin.com/in/..."
                                            >
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">GitHub</label>
                                            <input
                                                type="url"
                                                name="github_url"
                                                class="form-control"
                                                value="{{ old('github_url', $profile?->github_url) }}"
                                                placeholder="https://github.com/..."
                                            >
                                        </div>

                                        <div class="form-group profile-edit-col-full">
                                            <label class="form-label">Website</label>
                                            <input
                                                type="url"
                                                name="website_url"
                                                class="form-control"
                                                value="{{ old('website_url', $profile?->website_url) }}"
                                                placeholder="https://..."
                                            >
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div data-edit-tab-panel="media" {{ $activeEditTab !== 'media' ? 'hidden' : '' }}>
                        <section class="profile-edit-section">
                            <div class="profile-edit-section__head">
                                <h2>Görseller</h2>
                            </div>

                            <div class="profile-edit-section__body profile-edit-grid">
                                <div>
                                    <label class="form-label">Mevcut Profil Fotoğrafı</label>

                                    <div class="profile-media-card">
                                        <div class="profile-media-card__avatar">
                                            @if($profilePhoto)
                                                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}">
                                            @else
                                                {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                            @endif
                                        </div>

                                        <div>
                                            <div class="profile-media-card__title">
                                                {{ $profilePhoto ? 'Yüklü profil fotoğrafı var' : 'Henüz profil fotoğrafı yok' }}
                                            </div>
                                            <div class="profile-media-card__text">
                                                Yeni dosya seçersen mevcut fotoğraf değişir.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group profile-edit-mt">
                                        <input type="file" name="photo" class="form-control">
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Mevcut Kapak Fotoğrafı</label>

                                    <div class="profile-cover-card">
                                        <div class="profile-cover-card__preview"
                                             @if($coverPhoto)
                                                style="background-image:linear-gradient(rgba(9,18,43,.18), rgba(9,18,43,.18)), url('{{ $coverPhoto }}')"
                                             @endif></div>

                                        <div class="profile-media-card__text profile-edit-mt-sm">
                                            {{ $coverPhoto ? 'Yüklü kapak fotoğrafı var. Yeni dosya seçersen değişir.' : 'Henüz kapak fotoğrafı yok.' }}
                                        </div>
                                    </div>

                                    <div class="form-group profile-edit-mt">
                                        <input type="file" name="cover_photo" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div data-edit-tab-panel="privacy" {{ $activeEditTab !== 'privacy' ? 'hidden' : '' }}>
                        <section class="profile-edit-section">
                            <div class="profile-edit-section__head">
                                <h2>Gizlilik Ayarları</h2>
                            </div>

                            <div class="profile-edit-section__body profile-edit-form-grid">
                                <div class="form-group">
                                    <label class="form-label">Profil Görünürlüğü</label>
                                    <select name="profile_visibility" class="form-control">
                                        <option value="public" {{ old('profile_visibility', $privacy?->profile_visibility) === 'public' ? 'selected' : '' }}>Herkese Açık</option>
                                        <option value="connections" {{ old('profile_visibility', $privacy?->profile_visibility) === 'connections' ? 'selected' : '' }}>Sadece Bağlantılar</option>
                                        <option value="private" {{ old('profile_visibility', $privacy?->profile_visibility) === 'private' ? 'selected' : '' }}>Gizli</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">E-posta Görünürlüğü</label>
                                    <select name="email_visibility" class="form-control">
                                        <option value="public" {{ old('email_visibility', $privacy?->email_visibility) === 'public' ? 'selected' : '' }}>Herkese Açık</option>
                                        <option value="connections" {{ old('email_visibility', $privacy?->email_visibility) === 'connections' ? 'selected' : '' }}>Sadece Bağlantılar</option>
                                        <option value="private" {{ old('email_visibility', $privacy?->email_visibility) === 'private' ? 'selected' : '' }}>Gizli</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Sosyal Linkler</label>
                                    <select name="social_links_visibility" class="form-control">
                                        <option value="public" {{ old('social_links_visibility', $privacy?->social_links_visibility) === 'public' ? 'selected' : '' }}>Herkese Açık</option>
                                        <option value="connections" {{ old('social_links_visibility', $privacy?->social_links_visibility) === 'connections' ? 'selected' : '' }}>Sadece Bağlantılar</option>
                                        <option value="private" {{ old('social_links_visibility', $privacy?->social_links_visibility) === 'private' ? 'selected' : '' }}>Gizli</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Kulüpler</label>
                                    <select name="clubs_visibility" class="form-control">
                                        <option value="public" {{ old('clubs_visibility', $privacy?->clubs_visibility) === 'public' ? 'selected' : '' }}>Herkese Açık</option>
                                        <option value="connections" {{ old('clubs_visibility', $privacy?->clubs_visibility) === 'connections' ? 'selected' : '' }}>Sadece Bağlantılar</option>
                                        <option value="private" {{ old('clubs_visibility', $privacy?->clubs_visibility) === 'private' ? 'selected' : '' }}>Gizli</option>
                                    </select>
                                </div>

                                <div class="form-group profile-edit-col-full">
                                    <label class="form-label">Bağlantı Listesi</label>
                                    <select name="connections_visibility" class="form-control">
                                        <option value="public" {{ old('connections_visibility', $privacy?->connections_visibility) === 'public' ? 'selected' : '' }}>Herkese Açık</option>
                                        <option value="connections" {{ old('connections_visibility', $privacy?->connections_visibility) === 'connections' ? 'selected' : '' }}>Sadece Bağlantılar</option>
                                        <option value="private" {{ old('connections_visibility', $privacy?->connections_visibility) === 'private' ? 'selected' : '' }}>Gizli</option>
                                    </select>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div data-edit-tab-panel="notifications" {{ $activeEditTab !== 'notifications' ? 'hidden' : '' }}>
                        <section class="profile-edit-section">
                            <div class="profile-edit-section__head">
                                <h2>Bildirim Tercihleri</h2>
                            </div>

                            <div class="profile-edit-section__body profile-edit-form-grid">
                                <label class="profile-toggle-card">
                                    <span>Bağlantı istekleri</span>
                                    <span>
                                        <input type="hidden" name="connection_request_enabled" value="0">
                                        <input type="checkbox" name="connection_request_enabled" value="1" {{ $checkedSetting('connection_request_enabled', true) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Yorum bildirimleri</span>
                                    <span>
                                        <input type="hidden" name="comment_enabled" value="0">
                                        <input type="checkbox" name="comment_enabled" value="1" {{ $checkedSetting('comment_enabled', true) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Etkinlik bildirimleri</span>
                                    <span>
                                        <input type="hidden" name="event_enabled" value="0">
                                        <input type="checkbox" name="event_enabled" value="1" {{ $checkedSetting('event_enabled', true) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Duyuru bildirimleri</span>
                                    <span>
                                        <input type="hidden" name="announcement_enabled" value="0">
                                        <input type="checkbox" name="announcement_enabled" value="1" {{ $checkedSetting('announcement_enabled', true) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Bağlantı isteği için e-posta</span>
                                    <span>
                                        <input type="hidden" name="email_connection_request_enabled" value="0">
                                        <input type="checkbox" name="email_connection_request_enabled" value="1" {{ $checkedSetting('email_connection_request_enabled', false) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Yorum için e-posta</span>
                                    <span>
                                        <input type="hidden" name="email_comment_enabled" value="0">
                                        <input type="checkbox" name="email_comment_enabled" value="1" {{ $checkedSetting('email_comment_enabled', false) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Etkinlik için e-posta</span>
                                    <span>
                                        <input type="hidden" name="email_event_enabled" value="0">
                                        <input type="checkbox" name="email_event_enabled" value="1" {{ $checkedSetting('email_event_enabled', false) ? 'checked' : '' }}>
                                    </span>
                                </label>

                                <label class="profile-toggle-card">
                                    <span>Duyuru için e-posta</span>
                                    <span>
                                        <input type="hidden" name="email_announcement_enabled" value="0">
                                        <input type="checkbox" name="email_announcement_enabled" value="1" {{ $checkedSetting('email_announcement_enabled', false) ? 'checked' : '' }}>
                                    </span>
                                </label>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="profile-edit-actions">
                    <a href="{{ route('profile.show', $user) }}" class="forum-post-card__action forum-post-card__action--icon">
                        Vazgeç
                    </a>

                    <button type="submit" class="forum-post-card__action forum-post-card__action--icon forum-post-card__action--primary">
                        Kaydet
                    </button>
                </div>
            </form>

            <section class="profile-edit-password">
                <div class="profile-edit-section__head">
                    <h2>Şifre</h2>
                </div>

                <div class="profile-edit-section__body">
                    @if($errors->has('password_reset'))
                        <div class="profile-edit-alert profile-edit-alert--error profile-edit-alert--inline">
                            {{ $errors->first('password_reset') }}
                        </div>
                    @endif

                    <div class="profile-password-box">
                        <div>
                            <div class="profile-password-box__title">
                                Şifre sıfırlama bağlantısı gönder
                            </div>

                            <div class="profile-password-box__text">
                                Hesabındaki e-posta adresine bir bağlantı gider. Yeni şifreni maildeki bağlantıdan belirlersin.
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.password.reset.link') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="forum-post-card__action forum-post-card__action--icon forum-post-card__action--primary">
                                Mailime Bağlantı Gönder
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
@endsection