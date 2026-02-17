<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', 'CLIENTLY - CRM и онлайн-запись для сферы услуг')">

    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="CLIENTLY">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/welcome.js'])
    <x-theme-init />
    <x-yandex-metrics />
</head>
<body class="landing-page bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    @include('landing._header')

    <main class="pt-20 sm:pt-24 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @include('landing._footer')

    <button id="landing-scroll-top" class="landing-scroll-top bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" aria-label="Вернуться наверх">
        <x-icon name="arrow-up" variant="outline" size="md" class="text-gray-600 dark:text-gray-300" />
    </button>
</body>
</html>
