<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Forum | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="forum-body forum-sidebar-open">
    <div class="forum-shell">
        @includeIf('partials.forum-header')

        <button
            type="button"
            class="forum-sidebar-overlay"
            id="forumSidebarOverlay"
            aria-label="Menüyü kapat"
        ></button>

        <aside class="forum-sidebar-panel" id="forumSidebar">
            <div class="forum-sidebar-panel__top">
                <strong>Menü</strong>

                <button
                    type="button"
                    class="forum-sidebar-panel__close"
                    id="forumSidebarClose"
                    aria-label="Menüyü kapat"
                >
                    ×
                </button>
            </div>

            @includeIf('partials.forum-sidebar')
        </aside>
        <button
    type="button"
    class="forum-sidebar-reopen"
    id="forumSidebarReopen"
    aria-label="Menüyü aç"
>
    ☰
</button>

        <main class="forum-main forum-main--full" id="forumMain">
            @yield('content')
        </main>

        @include('partials.feedback-modal')
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>