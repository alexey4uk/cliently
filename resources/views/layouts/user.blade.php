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
    <header
        class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-gradient-to-r dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        @include('header')
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 pb-20 md:pb-6 space-y-6">
        @include('alerts')

        @yield('content')
    </main>

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
