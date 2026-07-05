@extends('layouts.forum')

@section('title', $club->name)

@section('content')
    <section class="forum-feed-page profile-page">
        <div class="forum-feed">
            <div class="profile-shell profile-shell--reddit">
                <div class="profile-main">
                    <div class="profile-reddit-header">
                        <div
                            class="profile-reddit-cover"
                            @if($club->cover_image)
                                style="background-image: url('{{ asset('storage/' . $club->cover_image) }}');"
                            @endif
                        ></div>

                        <div class="profile-reddit-bar">
                            <div class="profile-reddit-identity">
                                <div class="profile-reddit-avatar">
                                    @if($club->logo)
                                        <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}">
                                    @else
                                        {{ $clubInitial }}
                                    @endif
                                </div>

                                <div class="profile-reddit-meta">
                                    <h1 class="profile-reddit-name">{{ $club->name }}</h1>

                                    <div class="profile-reddit-sub">
                                        <span class="profile-badge">
                                            {{ $clubStatusLabel }}
                                        </span>

                                        <span>
                                            Kurucu:
                                            <strong>{{ $creatorDisplayName }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-reddit-actions">
                                @if($canPostInClub)
                                    <a href="{{ route('posts.create', ['club_id' => $club->id]) }}" class="forum-post-card__action forum-post-card__action--primary">
                                        Kulüp İçinde Paylaşım Yap
                                    </a>
                                @endif

                                @if($canManageClub)
                                    <a href="{{ route('clubs.edit', $club) }}" class="forum-post-card__action forum-post-card__action--primary">
                                        Kulübü Düzenle
                                    </a>
                                @elseif($joinAction['type'] === 'join' || $joinAction['type'] === 'rejoin')
                                    <form action="{{ route('clubs.join-request', $club) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="forum-post-card__action forum-post-card__action--primary">
                                            {{ $joinAction['label'] }}
                                        </button>
                                    </form>
                                @elseif($joinAction['type'] === 'pending' || $joinAction['type'] === 'approved')
                                    <button type="button" class="forum-post-card__action forum-post-card__action--primary" disabled>
                                        {{ $joinAction['label'] }}
                                    </button>
                                @elseif($joinAction['type'] === 'login')
                                    <a href="{{ route('login') }}" class="forum-post-card__action forum-post-card__action--primary">
                                        {{ $joinAction['label'] }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($club->short_description)
                            <div class="profile-reddit-bio">
                                {{ $club->short_description }}
                            </div>
                        @endif

                        <div class="profile-reddit-stats">
                            <span><strong>{{ $memberCount }}</strong> Üye</span>
                            <span><strong>{{ $pendingCount }}</strong> Bekleyen İstek</span>
                            <span><strong>{{ $foundedAtLabel }}</strong> Kuruluş</span>
                        </div>

                        <div class="profile-reddit-tabs">
                            <button type="button" class="profile-reddit-tab profile-reddit-tab--active">Genel Bakış</button>
                        </div>
                    </div>

                    <div class="profile-reddit-stream">
                        <article class="forum-post-card">
                            <div class="forum-post-card__content">
                                <h2 class="forum-post-card__title">Kulüp Hakkında</h2>

                                @if($club->description)
                                    <div class="forum-post-card__text" style="white-space: pre-line;">
                                        {{ $club->description }}
                                    </div>
                                @else
                                    <div class="profile-empty">
                                        Bu kulüp için henüz detaylı bir açıklama eklenmemiş.
                                    </div>
                                @endif
                            </div>
<h2 class="forum-post-card__title" style="margin: 6px 0 14px;">
    Kulüp Gönderileri
</h2>

@if(!$canViewClubPosts)
    <article class="forum-post-card">
        <div class="forum-post-card__content">
            <div class="profile-empty">
                Kulüp içi gönderileri görmek için bu kulübün onaylı üyesi olman gerekiyor.
            </div>
        </div>
    </article>
@elseif($clubPosts && $clubPosts->count())
    <div class="forum-post-list">
        @foreach($clubPosts as $post)
            @include('posts.partials.post-card', [
                'post' => $post,
                'postBadgeText' => null,
                'postStatusText' => null,
                'postRejectionReason' => null,
                'disablePostLink' => false,
                'hideSaveAction' => false,
                'showCommentForm' => true,
                'showPinAction' => auth()->check() && auth()->user()->user_type === 'admin',
                'showToggleActiveAction' => auth()->check() && (auth()->id() === $post->user_id || auth()->user()->user_type === 'admin'),
            ])
        @endforeach
    </div>

    <div style="margin-top: 18px;">
        {{ $clubPosts->links() }}
    </div>
@else
    <article class="forum-post-card">
        <div class="forum-post-card__content">
            <div class="profile-empty">
                Bu kulüpte henüz yayınlanmış gönderi yok.
            </div>
        </div>
    </article>
@endif

                        @if($canManageClub)
                            <article class="forum-post-card">
                                <div class="forum-post-card__content">
                                    <h2 class="forum-post-card__title">Gelen İstekler</h2>

                                    @if(count($pendingMemberships))
                                        <div class="profile-links">
                                            @foreach($pendingMemberships as $membership)
                                                <div class="profile-side-card">
                                                    <div class="profile-about-item-value">
                                                        {{ $membership['name'] }}
                                                    </div>

                                                    <div class="profile-about-item-label" style="margin-top: 6px;">
                                                        Üyelik isteği bekliyor
                                                    </div>

                                                    <div class="forum-post-card__actions" style="margin-top: 14px;">
                                                        <form action="{{ route('clubs.members.approve', [$club, $membership['id']]) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="forum-post-card__action">
                                                                Onayla
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('clubs.members.reject', [$club, $membership['id']]) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="rejection_reason" value="Kulüp yöneticisi tarafından reddedildi.">
                                                            <button type="submit" class="forum-post-card__action">
                                                                Reddet
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="profile-empty">
                                            Bekleyen üyelik isteği yok.
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endif

                        @if($club->status === 'rejected' && $club->rejection_reason)
                            <article class="forum-post-card">
                                <div class="forum-post-card__content">
                                    <h2 class="forum-post-card__title">Red Sebebi</h2>

                                    <div class="forum-post-card__text" style="white-space: pre-line;">
                                        {{ $club->rejection_reason }}
                                    </div>
                                </div>
                            </article>
                        @endif
                    </div>
                </div>

                <aside class="profile-side">
                    <div class="profile-side-card">
                        <h3 class="profile-side-title">Başkanlar</h3>

                        @if(count($founders) || count($presidents) || count($managers))
                            <div class="profile-links">
                                @foreach($founders as $membership)
                                    <div>
                                        <div class="profile-about-item-value">{{ $membership['name'] }}</div>
                                        <div class="profile-about-item-label">{{ $membership['role_label'] }}</div>
                                    </div>
                                @endforeach

                                @foreach($presidents as $membership)
                                    <div>
                                        <div class="profile-about-item-value">{{ $membership['name'] }}</div>
                                        <div class="profile-about-item-label">{{ $membership['role_label'] }}</div>
                                    </div>
                                @endforeach

                                @foreach($managers as $membership)
                                    <div>
                                        <div class="profile-about-item-value">{{ $membership['name'] }}</div>
                                        <div class="profile-about-item-label">{{ $membership['role_label'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="profile-empty">Henüz başkan bilgisi yok.</div>
                        @endif
                    </div>

                    <div class="profile-side-card">
                        <h3 class="profile-side-title">Üyeler</h3>

                        @if(count($approvedMemberships))
                            <div class="profile-links">
                                @foreach($approvedMemberships as $membership)
                                    <div>
                                        <div class="profile-about-item-value">{{ $membership['name'] }}</div>
                                        <div class="profile-about-item-label">{{ $membership['role_label'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="profile-empty">Bu kulüpte henüz onaylı üye bulunmuyor.</div>
                        @endif
                    </div>

                    <div class="profile-side-card">
                        <h3 class="profile-side-title">Kulüp Bilgileri</h3>

                        <div class="profile-about-list">
                            <div>
                                <div class="profile-about-item-label">Durum</div>
                                <div class="profile-about-item-value">{{ $clubStatusLabel }}</div>
                            </div>

                            <div>
                                <div class="profile-about-item-label">Kuruluş Tarihi</div>
                                <div class="profile-about-item-value">{{ $foundedAtLabel }}</div>
                            </div>

                            <div>
                                <div class="profile-about-item-label">Kulüp İçi Gönderi</div>
                                <div class="profile-about-item-value">
                                    @if($clubPosts)
                                        {{ $clubPosts->total() }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="profile-about-item-label">Oluşturulma</div>
                                <div class="profile-about-item-value">{{ $createdAtLabel }}</div>
                            </div>

                            <div>
                                <div class="profile-about-item-label">İnceleme Tarihi</div>
                                <div class="profile-about-item-value">{{ $reviewedAtLabel }}</div>
                            </div>

                            <div>
                                <div class="profile-about-item-label">Yayın Tarihi</div>
                                <div class="profile-about-item-value">{{ $publishedAtLabel }}</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection