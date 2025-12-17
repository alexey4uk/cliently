<!DOCTYPE html>
<html lang="ru" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cliently - CRM для мастеров')</title>

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
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar (скрыт на мобильных, виден на lg+) -->
        @include('sidebar')

        <!-- Основной контент -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Верхний header -->
            <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                    <!-- Логотип и заголовок (мобильная версия) -->
                    <div class="lg:hidden flex items-center gap-3">
                        <!-- Логознак: мастер + клиент -->
                        <div class="relative flex h-8 w-8 items-center justify-center flex-shrink-0">
                            <!-- Левый круг (мастер) -->
                            <span class="absolute h-6 w-6 rounded-full border-2 border-[#6366F1] left-0"></span>
                            <!-- Правый круг (клиент) -->
                            <span class="absolute h-6 w-6 rounded-full border-2 border-[#FF6B6B] right-0"></span>
                            <!-- Пересечение -->
                            <span class="absolute h-5 w-5 rounded-full bg-[#6366F1]/20"></span>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-slate-900 dark:text-white">
                                @yield('page-title', 'cliently')
                            </h1>
                        </div>
                    </div>

                    <!-- Действия в header -->
                    <div class="flex items-center gap-3 ml-auto">
                        <!-- Кнопка "Новая запись" -->
                        <button
                            class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-[#6366F1] px-3.5 py-1.5 text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:bg-[#4F46E5] active:bg-[#4338CA] transition-colors">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/10">
                                +
                            </span>
                            <span>Новая запись</span>
                        </button>

                        <!-- Переключатель темы -->
                        <button id="themeToggle"
                            class="h-8 w-8 rounded-full border border-slate-300 bg-white text-xs flex items-center justify-center text-slate-700 hover:bg-slate-100 transition-colors dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                            aria-label="Переключить тему">
                            🌓
                        </button>

                        <!-- Профиль пользователя -->
                        <div class="relative">
                            <button
                                class="menu-trigger h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-800 border border-slate-300 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors"
                                aria-label="Профиль">
                                АМ
                            </button>
                            <div
                                class="menu-panel z-[100] hidden w-48 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-user"></i></span>
                                    <span>Профиль</span>
                                </a>
                                <button
                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-cog"></i></span>
                                    <span>Настройки</span>
                                </button>
                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                        <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                class="fa-solid fa-right-from-bracket"></i></span>
                                        <span>Выйти</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Основной контент -->
            <main class="flex-1 overflow-y-auto">
                <div class="px-4 py-6 lg:px-8 lg:py-8 pb-20 lg:pb-8">
                    @include('alerts')

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Мобильное меню (только на мобильных) -->
    @include('mobile-menu')

    <script>
        const htmlEl = document.documentElement;
        const toggleBtn = document.getElementById('themeToggle');


        const prefersDark = window.matchMedia &&
            window.matchMedia('(prefers-color-scheme: dark)').matches;


        const savedTheme = localStorage.getItem('theme');


        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            htmlEl.classList.add('dark');
        } else {
            htmlEl.classList.remove('dark');
        }


        toggleBtn.addEventListener('click', () => {
            const isDark = htmlEl.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>
    
    <script>
        // dropdown для кнопок "⋯"
        document.addEventListener('click', (event) => {
            const triggers = document.querySelectorAll('.menu-trigger');
            const panels = document.querySelectorAll('.menu-panel');

            const trigger = event.target.closest('.menu-trigger');
            const panel = event.target.closest('.menu-panel');

            // клик по триггеру: переключаем только его меню
            if (trigger) {
                const container = trigger.closest('.relative');
                const currentPanel = container ? container.querySelector('.menu-panel') : null;

                if (currentPanel) {
                    const isOpen = !currentPanel.classList.contains('hidden');

                    // закрыть все
                    panels.forEach(p => {
                        p.classList.add('hidden');
                        p.style.position = '';
                        p.style.top = '';
                        p.style.right = '';
                        p.style.bottom = '';
                    });

                    // открыть / закрыть текущее
                    if (!isOpen) {
                        // Используем fixed позиционирование для выхода за границы overflow
                        const rect = trigger.getBoundingClientRect();
                        const panelHeight = 150; // примерная высота меню
                        const spaceBelow = window.innerHeight - rect.bottom;
                        const spaceAbove = rect.top;

                        currentPanel.style.position = 'fixed';
                        currentPanel.style.right = `${window.innerWidth - rect.right}px`;

                        // Если не хватает места снизу, открываем сверху
                        if (spaceBelow < panelHeight && spaceAbove > spaceBelow) {
                            currentPanel.style.bottom = `${window.innerHeight - rect.top + 4}px`;
                            currentPanel.style.top = 'auto';
                        } else {
                            currentPanel.style.top = `${rect.bottom + 4}px`;
                            currentPanel.style.bottom = 'auto';
                        }

                        currentPanel.classList.remove('hidden');
                    }
                }

                return;
            }

            // клик внутри меню — оставить открытым (здесь потом повесишь логику по кнопкам)
            //if (panel) return;

            // клик вне — закрыть все меню
            panels.forEach(p => {
                p.classList.add('hidden');
                p.style.position = '';
                p.style.top = '';
                p.style.right = '';
                p.style.bottom = '';
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
