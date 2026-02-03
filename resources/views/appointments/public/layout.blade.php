<!DOCTYPE html>
<html lang="ru" class="h-full scroll-smooth overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Онлайн запись') - {{ $business->name }}</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

    <!-- Theme initialization (must be before styles) -->
    <x-theme-init />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Минимальные стили для функциональности, которую нельзя реализовать только через Tailwind */
        html,
        body {
            font-family: "Onest", sans-serif;
            overflow-x: hidden !important;
            max-width: 100vw;
            width: 100%;
        }

        /* Скрытие scrollbar для горизонтального скролла */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Ограничение ширины для скроллируемых контейнеров */
        #week-dates-wrapper,
        #time-slots-wrapper {
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            position: relative;
        }

        /* Внутренние элементы скролла могут быть шире контейнера */
        #week-dates,
        #time-slots-container {
            box-sizing: border-box;
            display: flex;
        }

        /* Стили для drag скролла */
        #week-dates-wrapper.dragging,
        #time-slots-wrapper.dragging {
            scroll-behavior: auto !important;
            scroll-snap-type: none !important;
        }

        #week-dates-wrapper.dragging *,
        #time-slots-wrapper.dragging * {
            pointer-events: none;
            user-select: none;
            -webkit-user-select: none;
        }

        /* Предотвращение скролла body при открытом модальном окне */
        body.modal-open {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
            height: 100% !important;
        }

        /* Анимации для модального окна */
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0.8;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 640px) {
            #calendar-modal:not(.hidden) .calendar-dialog {
                animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }

        @media (min-width: 641px) {
            #calendar-modal:not(.hidden) .calendar-dialog {
                animation: fadeInScale 0.3s ease-out;
            }
        }

        /* Улучшение для touch-интерфейса */
        .touch-manipulation {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body
    class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-50 font-sans antialiased flex flex-col transition-colors duration-300">

    <!-- Шапка сайта (Sticky & Glassmorphism) -->
    <header
        class="sticky top-0 z-50 w-full bg-white/60 dark:bg-slate-950/60 backdrop-blur-2xl border-b border-slate-200/40 dark:border-slate-800/40 transition-all duration-300">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Группа Логотипа и Названия -->
            <div class="flex items-center gap-4 group cursor-default">
                @if (isset($business->logo) && $business->logo)
                    <a href="{{ route('public.appointments.show', $business->slug) }}"
                        class="relative flex-shrink-0 group/logo">
                        <!-- Мягкое свечение -->
                        <div
                            class="absolute -inset-2 bg-indigo-500/5 dark:bg-indigo-400/5 rounded-full blur-xl opacity-0 group-hover/logo:opacity-100 transition-opacity duration-700">
                        </div>

                        <!-- Контейнер логотипа -->
                        <div
                            class="relative h-10 w-10 sm:h-11 sm:w-11 rounded-[12px] bg-white dark:bg-slate-900 shadow-[0_4px_12px_rgba(0,0,0,0.05)] ring-1 ring-slate-200/50 dark:ring-slate-800 overflow-hidden transition-all duration-500 group-hover/logo:shadow-indigo-500/10 group-hover/logo:-translate-y-0.5">
                            <div class="w-full h-full p-[2px] flex items-center justify-center">
                                <img src="{{ asset('storage/' . $business->logo) }}" alt="{{ $business->name }}"
                                    class="w-full h-full aspect-square object-contain sm:object-cover rounded-[10px]">
                            </div>
                        </div>
                    </a>
                @endif


                <div class="flex flex-col">
                    <h1
                        class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tighter leading-none transition-all duration-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        {{ $business->name }}
                    </h1>
                </div>
            </div>

            <!-- Кнопка темы: Минимализм 2026 -->
            <button id="theme-toggle"
                class="relative h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-800/50 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden">
                <x-icon name="sun" size="sm"
                    class="hidden dark:block animate-in fade-in zoom-in duration-300" />
                <x-icon name="moon" size="sm"
                    class="block dark:hidden animate-in fade-in zoom-in duration-300" />
            </button>
        </div>
    </header>







    <!-- Основная область -->
    <main class="flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="min-w-0">
            @yield('content')
        </div>
    </main>

    <!-- Финальный футер 2026 -->
    <footer class="mt-auto py-8">
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex flex-col items-center gap-4">
                <!-- Разделитель-точка -->
                <div class="w-8 h-[1px] bg-slate-200 dark:bg-slate-800"></div>

                <!-- Платформа -->
                <a href="https://cliently.by" target="_blank" rel="noopener" class="flex items-center gap-2 group">
                    <span
                        class="text-[9px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest transition-colors group-hover:text-slate-400">Powered
                        by</span>
                    <span
                        class="text-[10px] font-black text-slate-400 dark:text-slate-500 tracking-[0.2em] group-hover:text-indigo-500 transition-colors">
                        CLIENTLY
                    </span>
                </a>
            </div>
        </div>
    </footer>



    @stack('scripts')
</body>



</html>
