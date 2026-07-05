<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>@yield('title', 'Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Öğretmenliği Bölümü')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        @yield('content')
        @include('partials.feedback-modal')
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>