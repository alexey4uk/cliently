<!DOCTYPE html>
<html lang="ru" class="h-full scroll-smooth overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Онлайн запись') - {{ $business->name }}</title>

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

<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-50 font-sans antialiased selection:bg-indigo-100 dark:selection:bg-indigo-500/30 flex flex-col transition-colors duration-300">
    
    <!-- Шапка сайта -->
    <header class="sticky top-0 z-40 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Логотип и название -->
            <div class="flex items-center gap-3">
                @if (isset($business->logo) && $business->logo)
                    <a href="/" class="group relative flex-shrink-0">
                        <div class="absolute -inset-1 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-xl opacity-0 group-hover:opacity-20 transition duration-300"></div>
                        <img src="{{ asset('storage/' . $business->logo) }}" 
                             alt="{{ $business->name }}"
                             class="relative h-11 w-11 object-cover rounded-xl shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                    </a>
                @endif
                <div class="flex flex-col">
                    <span class="text-lg font-black text-slate-900 dark:text-white leading-none tracking-tight">
                        {{ $business->name }}
                    </span>
                    <span class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mt-1">Онлайн-запись</span>
                </div>
            </div>

            <!-- Кнопки управления -->
            <div class="flex items-center gap-2">
                <button id="theme-toggle"
                    class="h-10 w-10 bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300"
                    aria-label="Переключить тему">
                    <x-icon name="sun" size="md" class="hidden dark:block" />
                    <x-icon name="moon" size="md" class="block dark:hidden" />
                </button>
            </div>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="min-w-0">
            @yield('content')
        </div>

        <!-- Копирайт платформы -->
        <div class="mt-12 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Powered by</span>
                <a href="https://cliently.by" target="_blank" rel="noopener" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 font-bold transition-colors">
                    CLIENTLY
                </a>
            </div>
        </div>
    </main>

    <!-- Футер -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- О компании -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-widest">
                        О компании
                    </h3>
                    @if ($business->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                            {{ $business->description }}
                        </p>
                    @endif
                </div>

                <!-- Быстрые контакты -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-widest">
                        Связь с нами
                    </h4>
                    <div class="flex flex-col gap-3">
                        @if ($business->phone)
                            <a href="tel:{{ $business->phone }}"
                                class="group flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </div>
                                <span class="font-medium">{{ $business->phone }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Нижняя плашка футера -->
            <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-800/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-[11px] text-slate-400 uppercase tracking-widest font-medium">
                    &copy; {{ date('2026') }} {{ $business->name }}
                </p>
                <div class="flex items-center gap-6">
                    <span class="text-[11px] text-slate-400 uppercase tracking-widest font-medium">Все права защищены</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>


</html>
