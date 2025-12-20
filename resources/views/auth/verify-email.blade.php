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
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Логотип и название -->
            <div class="flex items-center justify-center gap-3 mb-6">
                <!-- Логознак: мастер + клиент -->
                <div class="relative flex h-10 w-10 md:h-12 md:w-12 items-center justify-center flex-shrink-0">
                    <!-- Левый круг (мастер) -->
                    <span class="absolute h-8 w-8 md:h-9 md:w-9 rounded-full border-2 border-[#6366F1] left-0"></span>
                    <!-- Правый круг (клиент) -->
                    <span class="absolute h-8 w-8 md:h-9 md:w-9 rounded-full border-2 border-[#FF6B6B] right-0"></span>
                    <!-- Пересечение -->
                    <span class="absolute h-7 w-7 md:h-8 md:w-8 rounded-full bg-[#6366F1]/20"></span>
                </div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white tracking-tight uppercase font-display">
                    CLIENTLY
                </h1>
            </div>

            <!-- Форма подтверждения email -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
                <div class="space-y-5">
                <!-- Информационное сообщение -->
                    <div class="rounded-lg border border-[#6366F1]/20 bg-[#6366F1]/10 dark:border-[#6366F1]/30 dark:bg-[#6366F1]/20 p-3 md:p-4">
                        <div class="flex items-start gap-2.5">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-[#6366F1] dark:bg-[#6366F1] flex items-center justify-center">
                                    <i class="fa-solid fa-envelope text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs md:text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
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
                                class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-xs md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
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
                                class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 dark:border-slate-700 px-4 py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                            >
                            <span>Выйти из аккаунта</span>
                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Переключатель темы в правом верхнем углу -->
    <div class="fixed top-4 right-4 z-10">
        <button 
            id="themeToggle"
            class="h-10 w-10 rounded-full text-sm flex items-center justify-center text-slate-700 hover:bg-white/80 hover:shadow-sm transition-colors dark:text-slate-300 dark:hover:bg-slate-800/80"
            aria-label="Переключить тему"
        >
            <i class="fa-solid fa-sun text-sm dark:hidden"></i>
            <i class="fa-solid fa-moon text-sm hidden dark:inline"></i>
        </button>
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
