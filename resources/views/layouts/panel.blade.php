<!DOCTYPE html>
<html lang="ru" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Панель управления - Cliently')</title>

    <!-- Theme initialization (must be before styles) -->
    <x-theme-init />
    
    <!-- Sidebar initialization (must be before styles) -->
    <x-sidebar-init />

    <!-- Favicons links -->
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="CLIENTLY" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-50 font-sans overflow-x-hidden">
    <div class="flex min-h-screen lg:h-screen overflow-x-hidden lg:overflow-hidden">
        <!-- Sidebar (скрыт на мобильных, виден на lg+) -->
        @include('sidebar')

        <!-- Основной контент -->
        <div class="main-content flex flex-col flex-1 overflow-x-hidden lg:overflow-hidden lg:ml-64">
            <!-- Верхний header -->
            <x-header 
                :pageTitle="__('Панель управления')"
                :pageDescription="null"
                :showRoleBadge="true"
                :showProfile="true"
                :showMobileMenu="true"
                :showNotificationsDropdown="true"
            />

            <!-- Основной контент -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-slate-50 dark:bg-slate-950">
                <div class="px-4 py-6 md:px-6 md:py-8 lg:px-8 lg:py-10">
                    <div class="w-full">
                        @stack('breadcrumbs')
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    @include('alerts')
    
    <!-- Мобильное меню (вынесено за пределы header для корректного отображения) -->
    @include('mobile-menu-portal')

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toast-notification').forEach((notification) => {
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            });
        });
    </script>
    @livewireScripts
</body>

</html>
