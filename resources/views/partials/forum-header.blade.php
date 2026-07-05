<header class="forum-header">
    <div class="site-container">
        <div class="forum-header__inner">
            <div class="forum-header__left">
                <a href="{{ route('home') }}" class="site-logo forum-header__brand">
                    <span class="site-logo__mark">M</span>
                    <div class="site-logo__text">
                        <strong>Bölüm Forumu</strong>
                        <span>Marmara Üniversitesi BÖTE topluluk alanı</span>
                    </div>
                </a>

                <form action="{{ route('home') }}" method="GET" class="forum-search">
                    <input
                        type="text"
                        name="q"
                        class="forum-search__input"
                        placeholder="Forum içinde ara..."
                    >
                    <button type="submit" class="forum-search__button">Ara</button>
                </form>
            </div>

            <div class="forum-header__right">
               @php
    $headerUnreadNotifications = auth()->check()
        ? auth()->user()->unreadNotifications()->count()
        : 0;

    $headerLatestNotifications = auth()->check()
        ? auth()->user()->notifications()->take(8)->get()
        : collect();
@endphp

<div class="forum-notification-menu">
    <button type="button" class="forum-header__icon-btn forum-notification-menu__trigger" aria-label="Bildirimler">
        Bildirimler

     @if($headerUnreadNotifications > 0)
    <span class="forum-notification-menu__badge"></span>
@endif
    </button>

    <div class="forum-notification-menu__dropdown">
        <div class="forum-notification-menu__header">
            <strong>Bildirimler</strong>
            <span>{{ $headerUnreadNotifications }} okunmamış</span>
        </div>

        <div class="forum-notification-menu__list">
           @forelse($headerLatestNotifications as $notification)
    @php
        $relatedConnection = null;
        $canRespondToConnection = false;

        if (
            $notification->type === 'connection_request' &&
            $notification->related_type === 'user_connection' &&
            !empty($notification->related_id)
        ) {
            $relatedConnection = \App\Models\UserConnection::find($notification->related_id);

            $canRespondToConnection = $relatedConnection
                && $relatedConnection->status === 'pending'
                && $relatedConnection->receiver_user_id === auth()->id();
        }
    @endphp

    @if($canRespondToConnection)
        <div class="forum-notification-menu__item {{ !$notification->is_read ? 'forum-notification-menu__item--unread' : '' }}">
            <div class="forum-notification-menu__item-top">
                <strong>{{ $notification->title }}</strong>
                <span>{{ $notification->created_at?->diffForHumans() }}</span>
            </div>

            @if(!empty($notification->body))
                <div class="forum-notification-menu__item-body">
                    {{ $notification->body }}
                </div>
            @endif

            <div class="forum-notification-menu__item-type">
                {{ $notification->type }}
            </div>

            <div style="display:flex; gap:8px; margin-top:10px; flex-wrap:wrap;">
                <form method="POST" action="{{ route('connections.accept', $relatedConnection) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                        Kabul Et
                    </button>
                </form>

                <form method="POST" action="{{ route('connections.reject', $relatedConnection) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                        Reddet
                    </button>
                </form>
            </div>
        </div>
    @else
        <a
    href="{{ route('notifications.go', $notification) }}"
    class="forum-notification-menu__item {{ !$notification->is_read ? 'forum-notification-menu__item--unread' : '' }}"
>
            <div class="forum-notification-menu__item-top">
                <strong>{{ $notification->title }}</strong>
                <span>{{ $notification->created_at?->diffForHumans() }}</span>
            </div>

            @if(!empty($notification->body))
                <div class="forum-notification-menu__item-body">
                    {{ $notification->body }}
                </div>
            @endif

            <div class="forum-notification-menu__item-type">
                {{ $notification->type }}
            </div>
        </a>
    @endif
@empty
                <div class="forum-notification-menu__empty">
                    Henüz bildirimin yok.
                </div>
            @endforelse
        </div>
        <div class="forum-notification-menu__footer">
    <a href="{{ route('notifications.index') }}" class="forum-notification-menu__view-all">
        Tümünü Gör
    </a>
</div>
    </div>
</div>

                <div class="forum-user-menu">
                    <div class="forum-user-menu__trigger">
                      <div class="forum-user-menu__avatar">
    @if(!empty(auth()->user()->profile?->photo))
        <img
            src="{{ asset(auth()->user()->profile->photo) }}"
            alt="{{ auth()->user()->name }}"
            style="width:100%; height:100%; object-fit:cover; display:block;"
        >
    @else
        {{ mb_substr(auth()->user()->name, 0, 1) }}
    @endif
</div>

                        <div class="forum-user-menu__meta">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>{{ auth()->user()->email }}</span>
                        </div>
                    </div>

                    <div class="forum-user-menu__dropdown">
                        <div class="forum-user-menu__section">
                            <span class="forum-user-menu__label">Hesap</span>

                            <a href="{{ route('home') }}" class="forum-user-menu__link">
                                Forum Anasayfa
                            </a>

                            <a href="{{ route('profile.show', auth()->user()) }}" class="forum-user-menu__link">
                                Profilim
                            </a>

                            <a href="{{ route('profile.edit') }}" class="forum-user-menu__link">
                                Profil Ayarları
                            </a>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="forum-user-menu__form">
                            @csrf
                            <button type="submit" class="forum-user-menu__logout">
                                Çıkış Yap
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>