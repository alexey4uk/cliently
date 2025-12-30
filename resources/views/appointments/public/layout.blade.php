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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
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
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-50 font-sans overflow-x-hidden flex flex-col">
    <!-- Обычный header -->
    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-4xl lg:max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <!-- Логотип и название -->
                <div class="flex items-center gap-3">
                    <x-logo size="md" />
                    <span class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white font-sans">
                        {{ $business->name }}
                    </span>
                </div>

                <!-- Переключатель темы -->
                <button id="theme-toggle"
                        class="h-10 w-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-all duration-200"
                        aria-label="Переключить тему">
                    <x-icon name="sun" size="md" class="hidden dark:block" />
                    <x-icon name="moon" size="md" class="block dark:hidden" />
                </button>
            </div>
        </div>
    </header>

    <!-- Контентная область -->
    <main class="flex-1 py-8 sm:py-12 lg:py-8 px-4 sm:px-6 lg:px-8 overflow-x-hidden">
        <div class="max-w-4xl lg:max-w-4xl mx-auto w-full min-w-0">
            <!-- Прогресс интегрирован в начало контента -->
            @if(isset($currentStep))
            <div class="mb-8 sm:mb-12 lg:mb-8">
                @php
                    $steps = [
                        1 => ['label' => 'Локация', 'icon' => 'fa-map-marker-alt'],
                        2 => ['label' => 'Услуга', 'icon' => 'fa-spa'],
                        3 => ['label' => 'Мастер', 'icon' => 'fa-user-tie'],
                        4 => ['label' => 'Время', 'icon' => 'fa-clock'],
                    ];
                    $totalSteps = count($steps);
                @endphp
                
                <!-- Вертикальный прогресс-бар -->
                <div class="flex items-center gap-3 sm:gap-4 lg:gap-3 mb-6 lg:mb-4">
                    @foreach($steps as $stepNum => $step)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full h-2 lg:h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden mb-2 lg:mb-1.5 relative">
                                @if($currentStep >= $stepNum)
                                    <div class="absolute top-0 left-0 h-full bg-indigo-600 rounded-full transition-all duration-500"
                                         style="width: {{ $currentStep == $stepNum ? '100%' : '100%' }}"></div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 lg:gap-1.5 mt-2 lg:mt-1.5">
                                <div class="w-6 h-6 lg:w-5 lg:h-5 rounded-full flex items-center justify-center text-[10px] lg:text-[9px] transition-all duration-300
                                    @if($currentStep > $stepNum)
                                        bg-emerald-500 text-white
                                    @elseif($currentStep == $stepNum)
                                        bg-indigo-600 text-white scale-110
                                    @else
                                        bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400
                                    @endif">
                                    @if($currentStep > $stepNum)
                                        <i class="fa-solid fa-check"></i>
                                    @else
                                        <i class="fa-solid {{ $step['icon'] }}"></i>
                                    @endif
                                </div>
                                <span class="text-xs sm:text-sm lg:text-[10px] font-medium transition-colors duration-200 hidden sm:inline
                                    @if($currentStep >= $stepNum)
                                        text-indigo-600 dark:text-indigo-400
                                    @else
                                        text-slate-500 dark:text-slate-400
                                    @endif">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @yield('content')
        </div>
    </main>
    
    <!-- Футер -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 mt-16 sm:mt-20 lg:mt-12">
        <div class="max-w-4xl lg:max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 lg:gap-6">
                <!-- Информация о бизнесе -->
                <div class="space-y-4 lg:space-y-3">
                    <div class="flex items-center gap-3 lg:gap-2.5">
                        <x-logo size="sm" />
                        <h3 class="text-lg lg:text-base font-bold text-slate-900 dark:text-white font-sans">
                            {{ $business->name }}
                        </h3>
                    </div>
                    
                    @if($business->description)
                        <p class="text-sm lg:text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $business->description }}
                        </p>
                    @endif
                </div>
                
                <!-- Контакты -->
                <div class="space-y-4 lg:space-y-3">
                    <h4 class="text-sm lg:text-xs font-semibold text-slate-900 dark:text-white uppercase tracking-wider">
                        Контакты
                    </h4>
                    
                    @if($business->phone)
                        <a href="tel:{{ $business->phone }}" 
                           class="flex items-center gap-3 lg:gap-2.5 text-sm lg:text-xs text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <i class="fa-solid fa-phone w-5 lg:w-4 text-center"></i>
                            <span>{{ $business->phone }}</span>
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Копирайт -->
            <div class="mt-8 lg:mt-6 pt-8 lg:pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs lg:text-[10px] text-slate-500 dark:text-slate-400">
                    © {{ date('Y') }} {{ $business->name }}. Все права защищены.
                </p>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>
