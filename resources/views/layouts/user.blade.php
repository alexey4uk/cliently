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
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
                <div class="max-w-7xl mx-auto px-4 py-3 lg:px-6">
                    <div class="flex items-center justify-between">
                        <!-- Левая часть: Логотип/Заголовок -->
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <!-- Логотип (только мобильные) -->
                            <div class="lg:hidden flex items-center gap-3 flex-shrink-0">
                                <x-logo size="sm" />
                            </div>
                            
                            <!-- Заголовок страницы -->
                            <div class="min-w-0 flex-1">
                                <h1 class="text-lg md:text-xl font-semibold text-slate-900 dark:text-white truncate">
                                    @yield('page-title', 'cliently')
                                </h1>
                                @hasSection('page-description')
                                    <p class="hidden md:block text-sm text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                        @yield('page-description')
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Правая часть: Действия -->
                        <div class="flex items-center gap-2 md:gap-3 flex-shrink-0 ml-4">
                        <!-- Кнопка "Новая запись" (только на определенных страницах) -->
                        @hasSection('show-new-button')
                            <button
                                class="hidden sm:inline-flex items-center gap-1.5 rounded-md bg-[#6366F1] px-3 py-1.5 text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:bg-[#4F46E5] active:bg-[#4338CA] transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Новая запись</span>
                            </button>
                        @endif

                        <!-- Переключатель темы -->
                        <button id="themeToggle"
                            class="h-8 w-8 rounded-full flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="Переключить тему">
                            <i class="fa-solid fa-sun text-sm dark:hidden"></i>
                            <i class="fa-solid fa-moon text-sm hidden dark:inline"></i>
                        </button>

                        <!-- Уведомления -->
                        <div class="relative">
                            <button
                                class="h-8 w-8 rounded-full flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative"
                                aria-label="Уведомления">
                                <i class="fa-solid fa-bell text-sm"></i>
                                <!-- Индикатор новых уведомлений -->
                                <span class="absolute top-1 right-1 h-2 w-2 bg-rose-500 rounded-full"></span>
                            </button>
                        </div>

                        <!-- Профиль пользователя -->
                        <div class="relative">
                            @auth
                                <button
                                    class="menu-trigger h-9 w-9 rounded-md flex items-center justify-center text-sm font-semibold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors overflow-hidden"
                                    aria-label="Профиль">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                             alt="{{ Auth::user()->name }}" 
                                             class="w-full h-full object-cover rounded-md">
                                    @else
                                        <span class="h-full w-full rounded-md bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
                                            {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </button>
                            @else
                                <button
                                    class="menu-trigger h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-800 border border-slate-300 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors"
                                    aria-label="Профиль">
                                    АМ
                                </button>
                            @endauth
                            <div
                                class="menu-panel z-[100] hidden w-56 rounded-md border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900 shadow-lg">
                                <!-- Информация о пользователе -->
                                @auth
                                    <div class="px-3 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                        <div class="flex items-center gap-2.5">
                                            <div class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-xs font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-slate-700 overflow-hidden flex-shrink-0">
                                                @if(Auth::user()->avatar)
                                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                                         alt="{{ Auth::user()->name }}" 
                                                         class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                                    {{ Auth::user()->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                    {{ Auth::user()->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endauth
                                
                                <!-- Действия -->
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        <i class="fa-solid fa-user w-4 text-[#6366F1] dark:text-[#818CF8] text-xs"></i>
                                        <span>Профиль</span>
                                    </a>
                                    <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                            <i class="fa-solid fa-right-from-bracket w-4 text-[#6366F1] dark:text-[#818CF8] text-xs"></i>
                                            <span>Выйти</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Основной контент -->
            <main class="flex-1 overflow-y-auto">
                <div class="px-4 py-6 md:px-6 md:py-8 lg:px-8 lg:py-10 pb-20 lg:pb-10">
                    <div class="max-w-4xl mx-auto">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Мобильное меню (только на мобильных) -->
    @include('mobile-menu')

    <!-- Toast уведомления -->
    @include('alerts')

    <script>
        const htmlEl = document.documentElement;
        const toggleBtn = document.getElementById('themeToggle');

        if (toggleBtn) {
            const prefersDark = window.matchMedia &&
                window.matchMedia('(prefers-color-scheme: dark)').matches;

            const savedTheme = localStorage.getItem('theme');
            const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

            if (shouldBeDark) {
                htmlEl.classList.add('dark');
            } else {
                htmlEl.classList.remove('dark');
            }

            toggleBtn.addEventListener('click', () => {
                const isDark = htmlEl.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }
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
