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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Минимальные стили для функциональности, которую нельзя реализовать только через Tailwind */
        html, body {
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
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-50 font-sans overflow-x-hidden">
    <!-- Header секция -->
    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 overflow-x-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 w-full min-w-0">
            <!-- Логотип и переключатель темы -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <x-logo size="md" />
                    <span class="text-xl font-bold text-slate-900 dark:text-white uppercase font-display">{{ $business->name }}</span>
                </div>
                <!-- Переключатель темы -->
                <button id="theme-toggle" 
                        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        aria-label="Переключить тему">
                    <x-icon name="sun" size="md" class="hidden dark:block" />
                    <x-icon name="moon" size="md" class="block dark:hidden" />
                </button>
            </div>

            <!-- Индикатор прогресса -->
            @if(isset($currentStep))
            <div class="flex items-center justify-between w-full min-w-0">
                @php
                    $steps = [
                        1 => ['label' => 'Локация', 'icon' => 'fa-map-marker-alt'],
                        2 => ['label' => 'Услуга', 'icon' => 'fa-spa'],
                        3 => ['label' => 'Мастер', 'icon' => 'fa-user-tie'],
                        4 => ['label' => 'Время', 'icon' => 'fa-clock'],
                    ];
                @endphp
                @foreach($steps as $stepNum => $step)
                    <div class="flex flex-col items-center flex-1 relative">
                        <div class="relative flex items-center justify-center w-full">
                            <!-- Номер шага -->
                            <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center font-medium text-xs transition-colors
                                @if($currentStep > $stepNum)
                                    bg-green-500 text-white
                                @elseif($currentStep == $stepNum)
                                    bg-indigo-600 text-white
                                @else
                                    bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400
                                @endif">
                                @if($currentStep > $stepNum)
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                @else
                                    {{ $stepNum }}
                                @endif
                            </div>
                            <!-- Линия между шагами -->
                            @if($stepNum < count($steps))
                                <div class="absolute left-1/2 top-1/2 h-0.5 -translate-y-1/2 transition-colors
                                    @if($currentStep > $stepNum)
                                        bg-green-500
                                    @else
                                        bg-slate-200 dark:bg-slate-700
                                    @endif" style="width: calc(100% - 2rem);"></div>
                            @endif
                        </div>
                        <!-- Название шага -->
                        <span class="mt-1.5 text-[10px] font-medium text-center transition-colors
                            @if($currentStep >= $stepNum)
                                text-indigo-600 dark:text-indigo-400
                            @else
                                text-slate-500 dark:text-slate-400
                            @endif">
                            <span class="hidden sm:inline">{{ $step['label'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </header>

    <!-- Контентная область -->
    <main class="min-h-screen py-4 px-4 sm:px-6 lg:px-8 overflow-x-hidden">
        <div class="max-w-4xl mx-auto w-full min-w-0">
            @yield('content')
        </div>
    </main>
    
    @stack('scripts')
</body>
</html>
