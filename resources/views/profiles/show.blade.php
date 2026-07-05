@extends('layouts.forum')

@section('title', $user->name . ' ' . $user->surname . ' | Profil')

@section('content')
@php
    $fullName = trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));
    $displayName = $fullName !== '' ? $fullName : 'Kullanıcı';
    $isOwnProfile = auth()->check() && auth()->id() === $user->id;

    $profile = $user->profile;
    $privacy = $user->privacySetting;

    $interestTags = $user->items()
        ->where('type', 'interest')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->pluck('value')
        ->filter()
        ->take(8);

    if ($interestTags->isEmpty()) {
        $interestTags = $posts->pluck('category.name')
            ->merge($posts->pluck('course.name'))
            ->filter()
            ->unique()
            ->take(8);
    }

    $postCount = $posts->count();
    $commentCount = $user->comments()->count();
    $savedCount = $user->savedPosts()->count();

    $profilePhoto = !empty($profile?->photo) ? asset($profile->photo) : null;
    $coverPhoto = !empty($profile?->cover_photo) ? asset($profile->cover_photo) : null;

    $connectionRecord = null;
    $connectionStatus = null;
    $connectionDirection = null;
    $isConnected = false;

    if (auth()->check() && !$isOwnProfile) {
        $authUserId = auth()->id();

        $connectionRecord = \App\Models\UserConnection::query()
            ->where(function ($query) use ($authUserId, $user) {
                $query->where('sender_user_id', $authUserId)
                    ->where('receiver_user_id', $user->id);
            })
            ->orWhere(function ($query) use ($authUserId, $user) {
                $query->where('sender_user_id', $user->id)
                    ->where('receiver_user_id', $authUserId);
            })
            ->first();

        if ($connectionRecord) {
            $connectionStatus = $connectionRecord->status;
            $connectionDirection = $connectionRecord->sender_user_id === $authUserId ? 'sent' : 'received';
            $isConnected = $connectionRecord->status === 'accepted';
        }
    }

    $canSeeByVisibility = function (?string $visibility) use ($isOwnProfile, $isConnected) {
        $visibility = $visibility ?? 'private';

        if ($isOwnProfile) {
            return true;
        }

        if ($visibility === 'public') {
            return true;
        }

        if ($visibility === 'connections' && $isConnected) {
            return true;
        }

        return false;
    };

    $showEmail = $canSeeByVisibility($privacy?->email_visibility);
    $showSocialLinks = $canSeeByVisibility($privacy?->social_links_visibility);
    $showAbout = $canSeeByVisibility($privacy?->profile_visibility);
    $showClubs = $canSeeByVisibility($privacy?->clubs_visibility);

    $clubs = collect();

    $streamPosts = match ($activeTab ?? 'overview') {
        'posts' => $posts,
        'comments' => $posts->filter(fn ($post) => $post->comments_count > 0)->values(),
        default => $posts->take(5),
    };

    $streamTitle = match ($activeTab ?? 'overview') {
        'posts' => 'Paylaşımlar',
        'comments' => 'Yorum Etkileşimi Olan Gönderiler',
        default => 'Genel Bakış',
    };

    $streamEmptyText = match ($activeTab ?? 'overview') {
        'posts' => 'Bu kullanıcı henüz yayınlanmış gönderi paylaşmamış.',
        'comments' => 'Henüz yorum etkileşimi olan bir gönderi görünmüyor.',
        default => 'Gösterilecek içerik bulunamadı.',
    };
@endphp

    <section class="forum-feed-page profile-page">
        <div class="profile-shell profile-shell--reddit">
            <div class="profile-main">
                @if(session('success'))
                    <div class="profile-empty-state" style="margin-bottom: 14px; border-style: solid; border-color: #bbf7d0; background: #f0fdf4; color: #166534;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->has('connection'))
                    <div class="profile-empty-state" style="margin-bottom: 14px; border-style: solid; border-color: #fecaca; background: #fef2f2; color: #991b1b;">
                        {{ $errors->first('connection') }}
                    </div>
                @endif

                <div class="profile-reddit-header">
                    <div
                        class="profile-reddit-cover"
                        @if($coverPhoto)
                            style="background-image: linear-gradient(rgba(9,18,43,.15), rgba(9,18,43,.15)), url('{{ $coverPhoto }}'); background-size: cover; background-position: center;"
                        @endif
                    ></div>

                    <div class="profile-reddit-bar">
                        <div class="profile-reddit-identity">
                            <div class="profile-reddit-avatar">
                                @if($profilePhoto)
                                    <img src="{{ $profilePhoto }}" alt="{{ $displayName }}">
                                @else
                                    {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                @endif
                            </div>

                            <div class="profile-reddit-meta">
                                <h1 class="profile-reddit-name">{{ $displayName }}</h1>

                                <div class="profile-reddit-sub">
                                    <span class="profile-badge">
                                        {{ ucfirst($user->user_type ?? 'kullanıcı') }}
                                    </span>

                                    @if(!empty($user->department))
                                        <span>{{ $user->department }}</span>
                                    @endif

                                    @if(!empty($profile?->headline))
                                        <span>•</span>
                                        <span>{{ $profile->headline }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="profile-reddit-actions">
                            @if($showSocialLinks && !empty($profile?->linkedin_url))
                                <a href="{{ $profile->linkedin_url }}" target="_blank" class="forum-post-card__action forum-post-card__action--icon">
                                    LinkedIn
                                </a>
                            @endif

                            @if($showSocialLinks && !empty($profile?->github_url))
                                <a href="{{ $profile->github_url }}" target="_blank" class="forum-post-card__action forum-post-card__action--icon">
                                    GitHub
                                </a>
                            @endif

                            @if($showSocialLinks && !empty($profile?->website_url))
                                <a href="{{ $profile->website_url }}" target="_blank" class="forum-post-card__action forum-post-card__action--icon">
                                    Website
                                </a>
                            @endif

                            @if($isOwnProfile)
                                <a href="{{ route('profile.edit') }}" class="forum-post-card__action forum-post-card__action--icon forum-post-card__action--primary">
                                    Profili Düzenle
                                </a>
                            @elseif(auth()->check())
                                @if(!$connectionRecord)
                                    <form method="POST" action="{{ route('connections.send', $user) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="forum-post-card__action forum-post-card__action--icon forum-post-card__action--primary">
                                            Bağlantı Ekle
                                        </button>
                                    </form>
                                @elseif($connectionStatus === 'pending' && $connectionDirection === 'sent')
                                <form method="POST" action="{{ route('connections.cancel', $connectionRecord) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                                        İsteği Geri Çek
                                    </button>
                                </form>
                                @elseif($connectionStatus === 'pending' && $connectionDirection === 'received')
                                    <button type="button" class="forum-post-card__action forum-post-card__action--icon" disabled>
                                        Sana İstek Gönderdi
                                    </button>
                              @elseif($connectionStatus === 'accepted')
    <form method="POST" action="{{ route('connections.disconnect', $connectionRecord) }}" style="margin:0;">
        @csrf
        <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
            Bağlantıyı Kes
        </button>
    </form>
                               @elseif($connectionStatus === 'blocked')
    <button type="button" class="forum-post-card__action forum-post-card__action--icon" disabled>
        Engellendi
    </button>
@elseif($connectionStatus === 'rejected')
    <form method="POST" action="{{ route('connections.send', $user) }}" style="margin:0;">
        @csrf
        <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
            Tekrar İstek Gönder
        </button>
    </form>
@else
    <button type="button" class="forum-post-card__action forum-post-card__action--icon" disabled>
        Bağlantı Kaydı Var
    </button>
@endif
                            @endif
                        </div>
                    </div>

                    <div class="profile-reddit-bio">
                        @if(!empty($profile?->bio))
                            {{ $profile->bio }}
                        @else
                            {{ $displayName }} bölüm forumunda paylaşımlar yapan bir kullanıcı. Burada açtığı konular, ilgi alanları ve öne çıkan içerikleri görüntülenir.
                        @endif
                    </div>

                    <div class="profile-reddit-stats">
                        <span><strong>{{ $postCount }}</strong> Gönderi</span>
                        <span><strong>{{ $commentCount }}</strong> Yorum</span>
                        <span><strong>{{ $savedCount }}</strong> Kaydedilen</span>
                        <span>• {{ $user->created_at?->format('F Y') }} tarihinde katıldı</span>
                    </div>

                    <div class="profile-reddit-tabs">
                        <a
                            href="{{ route('profile.show', ['user' => $user->id, 'tab' => 'overview']) }}"
                            class="profile-reddit-tab {{ ($activeTab ?? 'overview') === 'overview' ? 'profile-reddit-tab--active' : '' }}"
                        >
                            Genel Bakış
                        </a>

                        <a
                            href="{{ route('profile.show', ['user' => $user->id, 'tab' => 'posts']) }}"
                            class="profile-reddit-tab {{ ($activeTab ?? 'overview') === 'posts' ? 'profile-reddit-tab--active' : '' }}"
                        >
                            Paylaşımlar
                        </a>

                        <a
                            href="{{ route('profile.show', ['user' => $user->id, 'tab' => 'comments']) }}"
                            class="profile-reddit-tab {{ ($activeTab ?? 'overview') === 'comments' ? 'profile-reddit-tab--active' : '' }}"
                        >
                            Yorumlar
                        </a>
                    </div>
                </div>

                @if(($activeTab ?? 'overview') === 'comments')
                    <div class="profile-empty-state" style="margin-top: 14px;">
                        Şimdilik yorumların kendisi listelenmiyor. Bu sekmede yalnızca yorum etkileşimi olan gönderiler gösteriliyor.
                    </div>
                @endif

                <div class="profile-reddit-stream">
                    @forelse($streamPosts as $post)
                        @php
                            $isOfficial = in_array($post->user?->user_type, ['admin', 'academic']);
                            $isSaved = auth()->check()
                                ? auth()->user()->savedPosts()->where('post_id', $post->id)->exists()
                                : false;
                        @endphp

                        <article class="forum-post-card forum-post-card--reddit {{ $isOfficial ? 'forum-post-card--official' : '' }} {{ $post->is_pinned ? 'forum-post-card--pinned' : '' }}">
                            <div class="forum-post-card__content">
                                @if($post->is_pinned)
                                    <div class="forum-post-card__official-badge forum-post-card__official-badge--pinned">
                                        Sabitlenmiş Gönderi
                                    </div>
                                @endif

                                @if($isOfficial)
                                    <div class="forum-post-card__official-badge">
                                        {{ $post->user?->user_type === 'admin' ? 'Yönetim Gönderisi' : 'Akademik Gönderi' }}
                                    </div>
                                @endif

                                <h3 class="forum-post-card__title">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="forum-post-card__title-link">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <div class="forum-post-card__author-line" style="margin-bottom: 12px;">
                                    @if($post->category)
                                        <span>{{ $post->category->name }}</span>
                                    @endif

                                    @if($post->course)
                                        <span class="forum-post-card__dot">•</span>
                                        <span>{{ $post->course->name }}</span>
                                    @endif

                                    <span class="forum-post-card__dot">•</span>
                                    <span>{{ $post->published_at?->diffForHumans() ?? $post->created_at->diffForHumans() }}</span>
                                </div>

                                @if($post->attachments->count())
                                    <div class="forum-post-attachments">
                                        @foreach($post->attachments as $attachment)
                                            <a
                                                href="{{ asset('storage/' . $attachment->file_path) }}"
                                                target="_blank"
                                                class="forum-post-attachment forum-post-attachment--{{ $attachment->type }}"
                                            >
                                                {{ $attachment->original_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="forum-post-card__text">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}
                                </div>
                            </div>

                            <div class="forum-post-card__actions forum-post-card__actions--reddit">
                                <span class="forum-post-card__action forum-post-card__action--icon" title="Görüntülenme">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2 12C3.8 8.6 7.4 6 12 6s8.2 2.6 10 6c-1.8 3.4-5.4 6-10 6s-8.2-2.6-10-6Z" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    <span>{{ $post->view_count }}</span>
                                </span>

                                @auth
                                    <button
                                        type="button"
                                        class="forum-post-card__action forum-post-card__action--icon js-profile-comment-toggle"
                                        data-target="profile-comment-form-{{ $post->id }}"
                                        title="Yorumlar"
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        </svg>
                                        <span>{{ $post->comments_count }}</span>
                                    </button>

                                    <form action="{{ route('posts.toggleSave', $post) }}" method="POST" style="display:inline-flex;">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="forum-post-card__action forum-post-card__action--icon {{ $isSaved ? 'forum-post-card__action--saved' : '' }}"
                                            title="{{ $isSaved ? 'Kaydı Kaldır' : 'Kaydet' }}"
                                        >
                                            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                                                <path
                                                    d="M7 4h10a1 1 0 0 1 1 1v15l-6-4-6 4V5a1 1 0 0 1 1-1Z"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linejoin="round"
                                                    fill="{{ $isSaved ? 'currentColor' : 'none' }}"
                                                />
                                            </svg>
                                            <span>{{ $isSaved ? 'Kaydedildi' : 'Kaydet' }}</span>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('posts.show', $post->slug) }}" class="forum-post-card__action forum-post-card__action--icon" title="Yorumlar">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        </svg>
                                        <span>{{ $post->comments_count }}</span>
                                    </a>
                                @endauth
                            </div>

                            @auth
                                <div id="profile-comment-form-{{ $post->id }}" class="profile-comment-inline-form" hidden>
                                    <form action="{{ route('comments.store', $post) }}" method="POST" class="forum-comment-form">
                                        @csrf

                                        <div class="form-group">
                                            <textarea
                                                name="body"
                                                rows="3"
                                                class="form-control"
                                                placeholder="Yorumunu yaz..."
                                            ></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            Yorumu Gönder
                                        </button>
                                    </form>
                                </div>
                            @endauth
                        </article>
                    @empty
                        <div class="profile-empty-state">
                            {{ $streamEmptyText }}
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="profile-side">
                @if($showAbout)
                    <section class="forum-post-card forum-post-card--reddit profile-side-card">
                        <h2 class="profile-side-title">Hakkında</h2>

                        <div class="profile-about-list">
                            <div>
                                <div class="profile-about-item-label">Kullanıcı tipi</div>
                                <div class="profile-about-item-value">{{ ucfirst($user->user_type ?? 'kullanıcı') }}</div>
                            </div>

                            @if(!empty($user->department))
                                <div>
                                    <div class="profile-about-item-label">Alan / Bölüm</div>
                                    <div class="profile-about-item-value">{{ $user->department }}</div>
                                </div>
                            @endif

                            @if(!empty($profile?->city))
                                <div>
                                    <div class="profile-about-item-label">Şehir</div>
                                    <div class="profile-about-item-value">{{ $profile->city }}</div>
                                </div>
                            @endif

                            @if($showEmail)
                                <div>
                                    <div class="profile-about-item-label">E-posta</div>
                                    <div class="profile-about-item-value">{{ $user->email }}</div>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="forum-post-card forum-post-card--reddit profile-side-card">
                    <h2 class="profile-side-title">İlgimi Çeken Konular</h2>

                    <div class="profile-tags">
                        @forelse($interestTags as $tag)
                            <span class="profile-tag">#{{ $tag }}</span>
                        @empty
                            <span class="profile-empty">Henüz ilgi alanı oluşacak kadar paylaşım yok.</span>
                        @endforelse
                    </div>
                </section>

                @if($showClubs)
                    <section class="forum-post-card forum-post-card--reddit profile-side-card">
                        <h2 class="profile-side-title">Katıldığı Kulüpler</h2>

                        @if($clubs->count())
                            <div class="profile-tags">
                                @foreach($clubs as $club)
                                    <span class="profile-tag">{{ $club->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="profile-empty">Henüz kulüp bilgisi görünmüyor.</span>
                        @endif
                    </section>
                @endif

                @if($showSocialLinks)
                    <section class="forum-post-card forum-post-card--reddit profile-side-card">
                        <h2 class="profile-side-title">Bağlantılar</h2>

                        <div class="profile-links">
                            @if(!empty($profile?->linkedin_url))
                                <a href="{{ $profile->linkedin_url }}" target="_blank" class="forum-post-card__action forum-post-card__action--icon">
                                    LinkedIn profiline git
                                </a>
                            @endif

                            @if(!empty($profile?->github_url))
                                <a href="{{ $profile->github_url }}" target="_blank" class="forum-post-card__action forum-post-card__action--icon">
                                    GitHub profiline git
                                </a>
                            @endif

                            @if(!empty($profile?->website_url))
                                <a href="{{ $profile->website_url }}" target="_blank" class="forum-post-card__action forum-post-card__action--icon">
                                    Kişisel sitesine git
                                </a>
                            @endif

                            @if(empty($profile?->linkedin_url) && empty($profile?->github_url) && empty($profile?->website_url))
                                <span class="profile-empty">Henüz sosyal bağlantı eklenmemiş.</span>
                            @endif
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.js-profile-comment-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    const targetId = button.getAttribute('data-target');
                    const target = document.getElementById(targetId);

                    if (!target) return;

                    target.hidden = !target.hidden;
                });
            });
        });
    </script>
@endsection