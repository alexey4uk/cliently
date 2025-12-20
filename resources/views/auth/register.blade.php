<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Регистрация - {{ config('app.name', 'Cliently') }}</title>

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

            <!-- Форма регистрации -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
                <form method="POST" action="{{ route('register') }}" class="space-y-5" id="registerForm">
                    @csrf

                        <!-- Имя -->
                        <div>
                        <label for="name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-user text-[#6366F1] dark:text-[#818CF8] text-xs"></i>
                            <span>Имя*</span>
                            </label>
                                <input
                                    type="text"
                            id="name" 
                            name="name"
                            value="{{ old('name') }}"
                                    required
                            autocomplete="name"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        />
                        @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-envelope text-[#6366F1] dark:text-[#818CF8] text-xs"></i>
                            <span>Email адрес*</span>
                        </label>
                            <input
                            type="email" 
                                id="email"
                                name="email"
                            value="{{ old('email') }}"
                            required
                                autocomplete="email"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        />
                        @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Пароль -->
                    <div>
                        <label for="password" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-lock text-[#6366F1] dark:text-[#818CF8] text-xs"></i>
                            <span>Пароль*</span>
                        </label>
                            <input
                            type="password" 
                            id="password" 
                            name="password"
                                required
                            autocomplete="new-password"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        />
                            @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Подтверждение пароля -->
                        <div>
                        <label for="password_confirmation" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-lock text-[#6366F1] dark:text-[#818CF8] text-xs"></i>
                            <span>Подтверждение пароля*</span>
                            </label>
                                <input
                            type="password" 
                                    id="password_confirmation"
                                    name="password_confirmation"
                            required
                                    autocomplete="new-password"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent transition-colors"
                        />
                    </div>

                    <!-- Согласие с пользовательским соглашением -->
                    <div>
                        <label class="flex items-start gap-2 text-base md:text-sm">
                        <input
                                type="checkbox" 
                            name="terms"
                            required
                                class="mt-0.5 w-4 h-4 text-[#6366F1] border-slate-300 rounded focus:ring-[#6366F1] focus:ring-2 dark:border-slate-700 dark:bg-slate-800"
                            />
                            <span class="text-slate-600 dark:text-slate-400">
                                Я согласен с 
                                <a href="#" class="text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors font-medium underline">
                                    пользовательским соглашением
                            </a>
                            </span>
                        </label>
                    @error('terms')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    </div>

                    <!-- Кнопка регистрации -->
                    <div class="pt-2">
                        <button 
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                        >
                            <span>Зарегистрироваться</span>
                            <i class="fa-solid fa-user-plus text-xs"></i>
                        </button>
                    </div>
                </form>

                    <!-- Разделитель -->
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-center text-base md:text-sm text-slate-600 dark:text-slate-400">
                                Уже есть аккаунт?
                        <a href="{{ route('login') }}" class="text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors font-medium">
                            Войти
                        </a>
                    </p>
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

        // Проверяем сохраненную тему или системные настройки
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
        const form = document.getElementById('registerForm');
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span> Регистрация...</span>';
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
