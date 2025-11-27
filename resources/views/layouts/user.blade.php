<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Мастерская CRM')</title>
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
<div class="flex h-full">
    <!-- Sidebar для десктопа -->
    @include('sidebar')

    <!-- Основной контент -->
    <div class="flex flex-col min-w-0 flex-1 overflow-hidden">
        <!-- Навигация для мобильных -->
        @include('mobile-menu')

        <!-- Навигация для десктопа -->
        @include('header')

        <!-- Основной контент -->
        <div class="flex-1 relative overflow-y-auto focus:outline-none">
            <div class="py-6">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
