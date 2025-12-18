<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Сброс пароля - {{ config('app.name', 'Cliently') }}</title>

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

            <!-- Форма сброса пароля -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6">
                    Сброс пароля
                </h2>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="passwordResetForm">
                    @csrf

                    <!-- Уведомление об успешной отправке -->
                    @if (session('status'))
                        <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                <p class="text-base md:text-sm text-emerald-700 dark:text-emerald-300">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Email -->
                    <div>
                        <label for="email" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400 text-xs"></i>
                            <span>Email адрес*</span>
                        </label>
                            <input
                            type="email" 
                                id="email"
                                name="email"
                            value="{{ old('email') }}"
                            required
                                autocomplete="email"
                            autofocus
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                placeholder="your@email.com"
                        />
                        @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Кнопка отправки -->
                    <div class="pt-2">
                        <button 
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-indigo-600/40 hover:bg-indigo-700 active:bg-indigo-800 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                        >
                            <span>Отправить ссылку</span>
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </div>
                </form>

                    <!-- Разделитель -->
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-center text-base md:text-sm text-slate-600 dark:text-slate-400">
                                Вспомнили пароль?
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors font-medium">
                            Войти
                        </a>
                    </p>
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

        // Улучшение UX формы
        const form = document.getElementById('passwordResetForm');
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span> Отправка...</span>';
            submitBtn.disabled = true;
        });

        // Валидация в реальном времени
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                } else {
                    this.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                }
            });
    });
</script>
</body>
</html>
