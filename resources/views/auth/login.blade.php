<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход в аккаунт - {{ config('app.name', 'Cliently') }}</title>

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
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .dark .login-bg {
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
                    Вход в аккаунт
                </h2>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    С возвращением! Продолжайте управлять вашими клиентами
                </p>
            </div>

            <!-- Форма -->
            <div class="space-y-4">
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email адрес *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('email') border-red-500 ring-2 ring-red-500/20 @enderror"
                                placeholder="your@email.com"
                                value="{{ old('email') }}"
                            >
                        </div>
                        @error('email')
                        <div class="mt-1 flex items-center space-x-1">
                            <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!-- Пароль -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Пароль *
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors duration-300">
                                    Забыли пароль?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('password') border-red-500 ring-2 ring-red-500/20 @enderror"
                                placeholder="Введите ваш пароль"
                            >
                        </div>
                        @error('password')
                        <div class="mt-1 flex items-center space-x-1">
                            <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!-- Запомнить меня -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500/20 border-gray-300 dark:border-gray-600 rounded transition-colors duration-300"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Запомнить меня на этом устройстве
                        </label>
                    </div>

                    <!-- Общие ошибки -->
                    @error('login')
                    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror

                    <!-- Кнопка входа -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-sign-in-alt text-sm"></i>
                            <span>Войти в аккаунт</span>
                        </button>
                    </div>

                    <!-- Разделитель -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs">
                                Еще нет аккаунта?
                            </span>
                        </div>
                    </div>

                    <!-- Ссылка на регистрацию -->
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="group w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-gray-300 dark:border-gray-600 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 bg-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-user-plus text-sm"></i>
                            <span>Создать новый аккаунт</span>
                        </a>
                    </div>
                </form>

                <!-- Дополнительная информация -->
                <div class="text-center pt-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Защищено современными методами шифрования
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Правая часть - баннер -->
    <div class="hidden lg:flex flex-1 login-bg relative">
        <div class="absolute inset-0 bg-black/10 dark:bg-black/20"></div>
        <div class="relative flex flex-col justify-center items-center px-8 text-white text-center">
            <!-- Иконка -->
            <div class="mb-6">
                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-users text-3xl text-white"></i>
                </div>
            </div>

            <!-- Заголовок -->
            <h3 class="text-3xl font-bold mb-4">
                Снова вместе!
            </h3>

            <!-- Описание -->
            <p class="text-lg opacity-90 mb-6 max-w-md">
                Продолжайте эффективно управлять вашими клиентами и записями в простой и удобной CRM
            </p>

            <!-- Преимущества -->
            <div class="space-y-3 text-left max-w-sm">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-white/80"></i>
                    <span class="text-white/90 text-sm">Управление клиентами</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-white/80"></i>
                    <span class="text-white/90 text-sm">Онлайн-запись 24/7</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-white/80"></i>
                    <span class="text-white/90 text-sm">Автоматические напоминания</span>
                </div>
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

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i><span> Вход...</span>';
            submitBtn.disabled = true;
        });

        // Валидация в реальном времени
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.classList.add('border-red-500', 'ring-2', 'ring-red-500/20');
                } else {
                    this.classList.remove('border-red-500', 'ring-2', 'ring-red-500/20');
                }
            });
        });
    });
</script>
</body>
</html>
