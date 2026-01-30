<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cliently — простая CRM для самозанятых и мастеров. Управляйте клиентами, записями и напоминаниями без сложностей.">
    <title>Cliently — CRM для мастеров и самозанятых</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="CLIENTLY">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">

    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/welcome.js'])
    <x-theme-init />
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    @include('landing._header')

    <main>
        @include('landing._hero')
        @include('landing._features')
        @include('landing._how-it-works')
        @include('landing._pricing')
        @include('landing._faq')
        @include('landing._cta')
    </main>

    @include('landing._footer')

    <button id="landing-scroll-top" class="landing-scroll-top bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" aria-label="Вернуться наверх">
        <x-icon name="arrow-up" variant="outline" size="md" class="text-gray-600 dark:text-gray-300" />
    </button>
</body>
</html>
