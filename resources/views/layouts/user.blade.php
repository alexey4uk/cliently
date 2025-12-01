<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cliently - CRM для мастеров')</title>

    <!-- Favicons links -->
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicons/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/favicons/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicons/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="CLIENTLY" />
    <link rel="manifest" href="{{ Vite::asset('resources/images/favicons/site.webmanifest') }}" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Плавные переходы для всех элементов */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Кастомный скроллбар */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
<div class="flex h-full">
    <!-- Sidebar для десктопа -->
    <div class="hidden lg:flex lg:flex-shrink-0">
        @include('sidebar')
    </div>

    <!-- Основной контент -->
    <div class="flex flex-col min-w-0 flex-1 overflow-hidden">
        <!-- Навигация для десктопа -->
        @include('header')

        <!-- Навигация для мобильных -->
        @include('mobile-menu')

        <!-- Основной контент -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none" id="main-content">
            <div class="py-6 px-4 sm:px-6 lg:px-8">
                <!-- Хлебные крошки (опционально) -->
                @hasSection('breadcrumbs')
                    <div class="mb-6">
                        @yield('breadcrumbs')
                    </div>
                @endif

                <!-- Заголовок страницы (опционально) -->
                @hasSection('page-header')
                    <div class="mb-8">
                        @yield('page-header')
                    </div>
                @endif

                <!-- Уведомления и алерты -->
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            <p class="text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                            <p class="text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>
                            <p class="text-yellow-700 dark:text-yellow-300 font-medium">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                            <p class="text-blue-700 dark:text-blue-300 font-medium">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Основной контент страницы -->
                <div class="space-y-6">
                    @yield('content')
                </div>

                <!-- Футер страницы (опционально) -->
                @hasSection('page-footer')
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        @yield('page-footer')
                    </div>
                @endif
            </div>
        </main>

        <!-- Глобальный футер (опционально) -->
        @hasSection('global-footer')
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    @yield('global-footer')
                </div>
            </footer>
        @endif
    </div>
</div>

<!-- Скрипты для улучшения UX -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Плавная прокрутка к якорям
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Автоскрытие уведомлений через 5 секунд
        setTimeout(() => {
            document.querySelectorAll('[class*="bg-"] .fa-check-circle, [class*="bg-"] .fa-info-circle').forEach(alert => {
                const alertContainer = alert.closest('[class*="bg-"]');
                if (alertContainer && !alertContainer.classList.contains('bg-red-50') && !alertContainer.classList.contains('bg-yellow-50')) {
                    alertContainer.style.opacity = '0';
                    alertContainer.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alertContainer.remove();
                    }, 500);
                }
            });
        }, 5000);

        // Улучшение доступности для фокуса
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            mainContent.setAttribute('tabindex', '-1');
            mainContent.focus();
        }

        // Обработка нажатия Escape для закрытия модальных окон
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Закрытие модальных окон, dropdown и т.д.
                const openModals = document.querySelectorAll('[data-modal-open="true"]');
                openModals.forEach(modal => {
                    const closeEvent = new Event('close');
                    modal.dispatchEvent(closeEvent);
                });
            }
        });
    });

    // Функция для показа загрузки
    function showLoading() {
        const loader = document.createElement('div');
        loader.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loader.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 flex items-center space-x-3">
                <i class="fas fa-spinner fa-spin text-blue-600 text-xl"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Загрузка...</span>
            </div>
        `;
        document.body.appendChild(loader);
        return loader;
    }

    // Функция для скрытия загрузки
    function hideLoading(loader) {
        if (loader) {
            loader.remove();
        }
    }
</script>

<!-- Дополнительные скрипты страницы -->
@yield('scripts')
</body>
</html>
