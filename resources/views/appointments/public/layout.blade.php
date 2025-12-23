<!DOCTYPE html>
<html lang="ru" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Онлайн запись') - {{ $business->name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Glass card effect - минималистичный */
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
        }
        
        .dark .glass-card {
            background: rgba(17, 24, 39, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
        
        /* Простой hover эффект - только цвет */
        .hover-border {
            transition: border-color 0.2s ease;
        }
        
        .hover-border:hover {
            border-color: rgb(99, 102, 241);
        }
        
        .dark .hover-border:hover {
            border-color: rgb(129, 140, 248);
        }
        
        /* Индикатор прогресса - простой */
        .progress-step {
            transition: color 0.2s ease;
        }
        
        .progress-step.active {
            color: rgb(99, 102, 241);
            font-weight: 600;
        }
        
        .dark .progress-step.active {
            color: rgb(129, 140, 248);
        }
        
        /* Горизонтальный скролл - скрыть scrollbar на мобильных */
        .scroll-hide::-webkit-scrollbar {
            display: none;
        }
        
        .scroll-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Touch-friendly скроллинг */
        .scroll-smooth-x {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Модальное окно - backdrop */
        .modal-backdrop {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        /* Улучшение для модального окна на мобильных */
        @media (max-width: 768px) {
            #calendar-content {
                max-height: 92vh;
            }
            
            /* Улучшение touch-интерфейса для календаря */
            .calendar-day {
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }
            
            /* Предотвращение выделения текста при тапе */
            #calendar-content {
                -webkit-user-select: none;
                user-select: none;
            }
        }
        
        /* Touch-friendly стили */
        .touch-manipulation {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 text-slate-900 dark:text-slate-50 font-sans">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-white mb-2">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 mr-2"></i>
                    Онлайн запись
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-base md:text-sm">{{ $business->name }}</p>
            </div>

            <!-- Индикатор прогресса -->
            @if(isset($currentStep))
            <div class="mb-6">
                <div class="flex items-center justify-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <span class="progress-step {{ $currentStep >= 1 ? 'active' : '' }}">Локация</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="progress-step {{ $currentStep >= 2 ? 'active' : '' }}">Услуга</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="progress-step {{ $currentStep >= 3 ? 'active' : '' }}">Мастер</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="progress-step {{ $currentStep >= 4 ? 'active' : '' }}">Время</span>
                </div>
            </div>
            @endif

            <!-- Контент -->
            @yield('content')
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>

