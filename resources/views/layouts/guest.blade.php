<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Öğretmenliği Bölümü')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.site-header')

    <main class="site-main">
        @yield('content')
    </main>

    @include('partials.site-footer')
    @include('partials.feedback-modal')

    @stack('modals')
    @stack('scripts')
</body>
</html>