<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Подтверждение email - {{ config('app.name', 'Cliently') }}</title>

    <!-- Favicons links -->
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="CLIENTLY" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Логотип и название -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <!-- Логознак: мастер + клиент -->
                    <div class="relative flex h-12 w-12 items-center justify-center">
                        <!-- Левый круг (мастер) -->
                        <span class="absolute h-9 w-9 rounded-full border-2 border-indigo-600 left-0"></span>
                        <!-- Правый круг (клиент) -->
                        <span class="absolute h-9 w-9 rounded-full border-2 border-rose-500 right-0"></span>
                        <!-- Пересечение -->
                        <span class="absolute h-8 w-8 rounded-full bg-indigo-600/20"></span>
                    </div>
                </div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight mb-1">
                    cliently
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    онлайн‑записи и клиенты
                </p>
            </div>

            <!-- Форма подтверждения email -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6">
                    Подтверждение email
                </h2>

                <div class="space-y-5">
                    <!-- Информационное сообщение -->
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 dark:border-indigo-900 dark:bg-indigo-900/30 p-3 md:p-4">
                        <div class="flex items-start gap-2.5">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center">
                                    <i class="fa-solid fa-envelope text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs md:text-sm text-indigo-900 dark:text-indigo-100 leading-relaxed">
                                    {{ __('Спасибо за регистрацию! Прежде чем начать, не могли бы вы подтвердить свой адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам по электронной почте? Если вы не получили письмо, мы с радостью отправим вам другое.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Уведомление об отправке -->
                    @if (session('status') == 'verification-link-sent')
                        <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                <p class="text-xs md:text-sm text-emerald-700 dark:text-emerald-300">
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
                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2.5 text-xs md:text-sm font-medium text-white shadow-sm shadow-indigo-600/40 hover:bg-indigo-700 active:bg-indigo-800 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                            >
                                <span>Отправить письмо повторно</span>
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </form>

                        <!-- Разделитель -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs">
                                    Или
                                </span>
                            </div>
                        </div>

                        <!-- Форма выхода -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 dark:border-slate-700 px-4 py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                            >
                                <span>Выйти из аккаунта</span>
                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Переключатель темы -->
            <div class="mt-6 text-center">
                <button 
                    id="themeToggle"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 transition-colors"
                    aria-label="Переключить тему"
                >
                    <span>🌓</span>
                    <span>Сменить тему</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Переключение темы
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const currentTheme = savedTheme || systemTheme;

        if (currentTheme === 'dark') {
            html.classList.add('dark');
        }

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>
</body>
</html>
