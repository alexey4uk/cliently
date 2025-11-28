<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Подтверждение email - {{ config('app.name', 'Cliently') }}</title>
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.svg') }}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .verification-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .dark .verification-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #7e22ce 100%);
        }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
<div class="min-h-screen flex relative">
    <!-- Левая часть - форма -->
    <div class="flex-1 flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-16 xl:px-20">
        <!-- Кнопка переключения темы -->
        <div class="absolute top-4 right-4 z-50">
            <button id="theme-toggle-desktop" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 focus:outline-none bg-white dark:bg-gray-800 shadow-md">
                <svg id="theme-light-icon-desktop" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-dark-icon-desktop" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <div class="mx-auto w-full max-w-md">
            <!-- Заголовок -->
            <div class="text-center lg:text-left mb-6">
                <!-- Логотип -->
                <div class="flex items-center justify-center lg:justify-start space-x-3 mb-4">
                    <div class="bg-blue-600 text-white p-2 rounded-lg">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">CLIENTLY</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">CRM для мастеров</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Подтверждение email
                </h2>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    Завершите регистрацию вашего аккаунта
                </p>
            </div>

            <!-- Форма -->
            <div class="space-y-4">
                <!-- Информационное сообщение -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
                        <div class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed">
                            {{ __('Спасибо за регистрацию! Прежде чем начать, не могли бы вы подтвердить свой адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам по электронной почте? Если вы не получили письмо, мы с радостью отправим вам другое.') }}
                        </div>
                    </div>
                </div>

                <!-- Уведомление об отправке -->
                @if (session('status') == 'verification-link-sent')
                    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500 text-sm"></i>
                            <p class="text-sm text-green-600 dark:text-green-400">
                                {{ __('Новая ссылка для подтверждения была отправлена на указанный вами адрес электронной почты.') }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Кнопки -->
                <div class="space-y-3">
                    <!-- Форма повторной отправки -->
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="group relative w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-paper-plane text-sm"></i>
                            <span>Отправить письмо повторно</span>
                        </button>
                    </form>

                    <!-- Разделитель -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs">
                                Или
                            </span>
                        </div>
                    </div>

                    <!-- Форма выхода -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-gray-300 dark:border-gray-600 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 bg-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                            <span>Выйти из аккаунта</span>
                        </button>
                    </form>
                </div>

                <!-- Дополнительная информация -->
                <div class="text-center pt-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Проверьте папку "Спам", если не видите письмо
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Правая часть - баннер -->
    <div class="hidden lg:flex flex-1 verification-bg relative">
        <div class="absolute inset-0 bg-black/10 dark:bg-black/20"></div>
        <div class="relative flex flex-col justify-center items-center px-8 text-white text-center">
            <!-- Иконка -->
            <div class="mb-6">
                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-envelope-open-text text-3xl text-white"></i>
                </div>
            </div>

            <!-- Заголовок -->
            <h3 class="text-3xl font-bold mb-4">
                Проверьте почту!
            </h3>

            <!-- Описание -->
            <p class="text-lg opacity-90 mb-6 max-w-md">
                Мы отправили вам письмо со ссылкой для подтверждения вашего email адреса
            </p>

            <!-- Шаги подтверждения -->
            <div class="space-y-3 text-left max-w-sm">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-white/30 rounded-full flex items-center justify-center text-xs font-bold">1</div>
                    <span class="text-white/90 text-sm">Откройте вашу почту</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-white/30 rounded-full flex items-center justify-center text-xs font-bold">2</div>
                    <span class="text-white/90 text-sm">Найдите письмо от Cliently</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-white/30 rounded-full flex items-center justify-center text-xs font-bold">3</div>
                    <span class="text-white/90 text-sm">Нажмите на ссылку подтверждения</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-white/30 rounded-full flex items-center justify-center text-xs font-bold">4</div>
                    <span class="text-white/90 text-sm">Начните использовать сервис</span>
                </div>
            </div>

            <!-- Подсказка -->
            <div class="mt-6 p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                <p class="text-white/80 text-sm">
                    <i class="fas fa-lightbulb mr-1"></i>
                    Не видите письмо? Проверьте папку "Спам"
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Скрипт для улучшения UX -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Улучшение UX формы
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn) {
            form.addEventListener('submit', function() {
                if (submitBtn.querySelector('.fa-paper-plane')) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i><span> Отправка...</span>';
                    submitBtn.disabled = true;
                }
            });
        }

        // Автоматическое обновление страницы при успешной отправке
        @if (session('status') == 'verification-link-sent')
        setTimeout(() => {
            // Можно добавить плавное исчезновение уведомления
            const notification = document.querySelector('.bg-green-50');
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }, 5000);
        @endif
    });
</script>
</body>
</html>
