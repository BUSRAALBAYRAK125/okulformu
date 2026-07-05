<aside class="forum-sidebar">
    <div class="forum-sidebar__inner">
        @if(auth()->check() && auth()->user()->user_type === 'admin')
            <div class="forum-sidebar__section">
                <div class="forum-sidebar__title">Admin</div>

                <nav class="forum-sidebar__nav">
                    <a
                        href="{{ route('admin.posts.pending') }}"
                        class="forum-sidebar__link {{ request()->routeIs('admin.posts.pending') ? 'forum-sidebar__link--active' : '' }}"
                    >
                        Gönderi Onayları
                    </a>

                    <a
                        href="{{ route('admin.approvals.index') }}"
                        class="forum-sidebar__link {{ request()->routeIs('admin.approvals.index') ? 'forum-sidebar__link--active' : '' }}"
                    >
                        Kullanıcı Onayları
                    </a>

                    @if(Route::has('admin.clubs.pending'))
                        <a
                            href="{{ route('admin.clubs.pending') }}"
                            class="forum-sidebar__link {{ request()->routeIs('admin.clubs.pending') ? 'forum-sidebar__link--active' : '' }}"
                        >
                            Kulüp Onayları
                        </a>
                    @endif
                </nav>
            </div>
        @endif

        <div class="forum-sidebar__section">
            <div class="forum-sidebar__title">Gezinme</div>

            <nav class="forum-sidebar__nav">
                <a
                    href="{{ route('home') }}"
                    class="forum-sidebar__link {{ request()->routeIs('home') ? 'forum-sidebar__link--active' : '' }}"
                >
                    Ana Sayfa
                </a>

                <a href="#" class="forum-sidebar__link">
                    Kategoriler
                </a>

                <a href="#" class="forum-sidebar__link">
                    Son Konular
                </a>

                <a href="#" class="forum-sidebar__link">
                    Popüler Başlıklar
                </a>
            </nav>
        </div>

        <div class="forum-sidebar__section">
            <div class="forum-sidebar__title">Hızlı Erişim</div>

            <nav class="forum-sidebar__nav">
                <a
                    href="{{ route('posts.create') }}"
                    class="forum-sidebar__link {{ request()->routeIs('posts.create') ? 'forum-sidebar__link--active' : '' }}"
                >
                    Yeni Konu Aç
                </a>

                <a
                    href="{{ route('posts.my') }}"
                    class="forum-sidebar__link {{ request()->routeIs('posts.my') ? 'forum-sidebar__link--active' : '' }}"
                >
                    Benim Konularım
                </a>

                <a
                    href="{{ route('posts.saved') }}"
                    class="forum-sidebar__link {{ request()->routeIs('posts.saved') ? 'forum-sidebar__link--active' : '' }}"
                >
                    Kaydettiğim Gönderiler
                </a>

                @auth
                    <a
                        href="{{ route('clubs.create') }}"
                        class="forum-sidebar__link {{ request()->routeIs('clubs.create') ? 'forum-sidebar__link--active' : '' }}"
                    >
                        Kulüp Oluştur
                    </a>
                @endauth
            </nav>
        </div>
    </div>
</aside>