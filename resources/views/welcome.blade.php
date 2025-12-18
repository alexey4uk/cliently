<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cliently - простая CRM для самозанятых и мастеров. Управляйте клиентами, записями и напоминаниями без сложностей.">
    <title>Cliently - CRM для мастеров и самозанятых</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap&subset=cyrillic" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="CLIENTLY" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --transition-base: 200ms ease;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* Mobile Menu */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
        .mobile-menu-overlay {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .mobile-menu-overlay.open {
            opacity: 1;
            visibility: visible;
        }
        
        /* FAQ */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            padding-top: 0;
            padding-bottom: 0;
            transition: max-height 0.3s ease, padding-top 0.3s ease, padding-bottom 0.3s ease;
        }
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding-top: 0.75rem;
            padding-bottom: 1.5rem;
        }
        .faq-item.active .fa-chevron-down {
            transform: rotate(180deg);
        }
        .faq-item.active {
            background: rgba(255, 255, 255, 0.8) !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.2), 0 0 0 1px rgba(99, 102, 241, 0.1);
        }
        .dark .faq-item.active {
            background: rgba(17, 24, 39, 0.7) !important;
            border-color: rgba(99, 102, 241, 0.3) !important;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3), 0 0 0 1px rgba(99, 102, 241, 0.2);
        }
        
        /* Smooth animations */
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Gradient text with animation */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        .dark .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #a78bfa 50%, #c084fc 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Enhanced Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .dark .glass {
            background: rgba(17, 24, 39, 0.6);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Glass card effect */
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
        
        /* Glass button effect */
        .glass-button {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .dark .glass-button {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Animated background gradient */
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }
        
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        /* Glow effect */
        .glow {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.3), 0 0 40px rgba(139, 92, 246, 0.2);
        }
        
        .glow:hover {
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.5), 0 0 60px rgba(139, 92, 246, 0.3);
        }
        
        /* Card hover effect */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .dark .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        
        /* Scroll to top button */
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 50;
            cursor: pointer;
        }
        
        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .scroll-to-top:hover {
            transform: translateY(-4px);
        }
        
        /* Button pulse effect */
        .btn-pulse {
            position: relative;
            overflow: hidden;
        }
        
        .btn-pulse::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-pulse:hover::before {
            width: 300px;
            height: 300px;
        }
        
        /* Floating animation */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .float {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Shine effect */
        .shine {
            position: relative;
            overflow: hidden;
        }
        
        .shine::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .shine:hover::after {
            left: 100%;
        }
        
        /* Feature card icon animation */
        .feature-icon {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Blob animation */
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-200/50 dark:border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 group">
                    <!-- Логознак: мастер + клиент -->
                    <div class="relative flex h-10 w-10 items-center justify-center flex-shrink-0">
                        <!-- Левый круг (мастер) -->
                        <span class="absolute h-7 w-7 rounded-full border-2 border-indigo-600 left-0"></span>
                        <!-- Правый круг (клиент) -->
                        <span class="absolute h-7 w-7 rounded-full border-2 border-rose-500 right-0"></span>
                        <!-- Пересечение -->
                        <span class="absolute h-6 w-6 rounded-full bg-indigo-600/20"></span>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">CLIENTLY</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">CRM для мастеров</div>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-1">
                    <a href="#features" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Возможности
                    </a>
                    <a href="#how-it-works" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Как это работает
                    </a>
                    <a href="#pricing" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Тарифы
                    </a>
                    <a href="#faq" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        FAQ
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Переключить тему">
                        <i class="fa-solid fa-sun text-base dark:hidden"></i>
                        <i class="fa-solid fa-moon text-base hidden dark:inline"></i>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            Панель управления
                        </a>
                        <a href="{{ route('dashboard') }}" class="sm:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Панель управления">
                            <i class="fas fa-tachometer-alt text-lg"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            Начать бесплатно
                        </a>
                        <a href="{{ route('login') }}" class="sm:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Войти">
                            <i class="fas fa-sign-in-alt text-lg"></i>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div id="mobile-menu-overlay" class="mobile-menu-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"></div>
    <div id="mobile-menu" class="mobile-menu fixed top-0 left-0 w-full sm:w-80 h-full glass shadow-2xl z-50 lg:hidden border-r border-white/20 dark:border-gray-700/30">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <!-- Логознак: мастер + клиент -->
                    <div class="relative flex h-10 w-10 items-center justify-center flex-shrink-0">
                        <!-- Левый круг (мастер) -->
                        <span class="absolute h-7 w-7 rounded-full border-2 border-indigo-600 left-0"></span>
                        <!-- Правый круг (клиент) -->
                        <span class="absolute h-7 w-7 rounded-full border-2 border-rose-500 right-0"></span>
                        <!-- Пересечение -->
                        <span class="absolute h-6 w-6 rounded-full bg-indigo-600/20"></span>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">CLIENTLY</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">CRM для мастеров</div>
                    </div>
                </div>
                <button id="mobile-menu-close" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <nav class="space-y-2 mb-6">
                <a href="#features" class="mobile-nav-link block px-4 py-3 glass-button text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-white/10 rounded-lg transition-all">
                    Возможности
                </a>
                <a href="#how-it-works" class="mobile-nav-link block px-4 py-3 glass-button text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-white/10 rounded-lg transition-all">
                    Как это работает
                </a>
                <a href="#pricing" class="mobile-nav-link block px-4 py-3 glass-button text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-white/10 rounded-lg transition-all">
                    Тарифы
                </a>
                <a href="#faq" class="mobile-nav-link block px-4 py-3 glass-button text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-white/10 rounded-lg transition-all">
                    FAQ
                </a>
            </nav>
            
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                @auth
                    <!-- Информация о пользователе -->
                    <div class="mb-4 p-3 glass-card rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-full h-full rounded-lg object-cover">
                                @else
                                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-lg mb-3 shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-tachometer-alt text-sm"></i>
                        <span>Панель управления</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mb-3 transition-colors">
                        <i class="fas fa-user text-sm"></i>
                        <span>Профиль</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                            <span>Выйти</span>
                        </button>
                    </form>
                @else
                    <div class="space-y-3">
                        <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-user-plus text-sm"></i>
                            <span>Начать бесплатно</span>
                        </a>
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sign-in-alt text-sm"></i>
                            <span>Войти в аккаунт</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="pt-20 pb-12 sm:pt-24 sm:pb-16 md:pt-32 md:pb-24 relative overflow-hidden min-h-screen flex items-center">
        <!-- Animated gradient background -->
        <div class="absolute inset-0 animated-gradient opacity-30 dark:opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-100/90 via-purple-100/90 to-pink-100/90 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"></div>
        
        <!-- Decorative glass blobs -->
        <div class="absolute top-20 left-10 w-96 h-96 bg-gradient-to-r from-purple-400/40 to-pink-400/40 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-blob"></div>
        <div class="absolute top-40 right-10 w-96 h-96 bg-gradient-to-r from-indigo-400/40 to-blue-400/40 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-96 h-96 bg-gradient-to-r from-yellow-400/40 to-orange-400/40 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-blob animation-delay-4000"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in">
                    <div class="inline-flex items-center px-4 py-2 glass-card rounded-full mb-6 shadow-xl">
                        <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">✨ Начните бесплатно, без карты</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6 leading-tight">
                        Управляйте клиентами
                        <span class="gradient-text">без сложностей</span>
                    </h1>
                    <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-6 sm:mb-8 leading-relaxed">
                        Cliently — простая CRM для мастеров и самозанятых. Записи, напоминания и история клиентов в одном месте.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-pulse shine inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-2xl hover:shadow-indigo-500/50 transition-all transform hover:scale-105">
                                <span class="relative z-10">Перейти в панель</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-pulse shine inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-2xl hover:shadow-indigo-500/50 transition-all transform hover:scale-105">
                                <span class="relative z-10">Начать бесплатно</span>
                            </a>
                        @endauth
                        <a href="#features" class="inline-flex items-center justify-center px-8 py-4 border-2 border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all transform hover:scale-105">
                            Узнать больше
                        </a>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2 text-sm"></i>
                            <span>Без кредитной карты</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2 text-sm"></i>
                            <span>Настройка за 5 минут</span>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block animate-fade-in float" style="animation-delay: 0.2s;">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-400/50 via-purple-500/50 to-pink-500/50 rounded-3xl blur-3xl opacity-40 animate-pulse"></div>
                        <div class="relative glass-card rounded-3xl shadow-2xl p-6 glow">
                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/20 dark:border-gray-700/50">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">Мои клиенты</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Сегодня, {{ \Carbon\Carbon::now()->locale('ru')->isoFormat('D MMMM') }}</p>
                                </div>
                                <div class="w-3 h-3 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 glass-card rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Анна К.</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Стрижка • 14:00</div>
                                    </div>
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="flex items-center justify-between p-3 glass-card rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Мария С.</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Маникюр • 16:30</div>
                                    </div>
                                    <div class="w-2 h-2 bg-green-500 rounded-full shadow-lg shadow-green-500/50"></div>
                                </div>
                                <div class="flex items-center justify-between p-3 glass-card rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Ирина П.</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Консультация • 18:00</div>
                                    </div>
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 sm:py-16 md:py-20 relative overflow-hidden">
        <!-- Glass background -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50/80 via-indigo-50/60 to-purple-50/60 dark:from-gray-900/90 dark:via-gray-800/90 dark:to-gray-900/90 backdrop-blur-sm"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 dark:bg-indigo-900/20 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-200/30 dark:bg-purple-900/20 rounded-full filter blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-10 sm:mb-12 md:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4">Всё необходимое для вашего бизнеса</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto px-4">
                    Мы сосредоточились на самом важном, чтобы вы могли работать эффективнее
                </p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- Feature 1 -->
                <div class="feature-card card-hover glass-card p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-xl">
                    <div class="feature-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-indigo-400 to-indigo-600 dark:from-indigo-500 dark:to-indigo-700 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg">
                        <i class="fas fa-address-book text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">База клиентов</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Храните контакты, заметки и историю обращений всех ваших клиентов в одном месте.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card card-hover glass-card p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-xl">
                    <div class="feature-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-400 to-green-600 dark:from-green-500 dark:to-green-700 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg">
                        <i class="fas fa-calendar-check text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Онлайн-запись</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Клиенты могут записываться самостоятельно через вашу ссылку. Вы получаете уведомления о новых записях.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card card-hover glass-card p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-xl">
                    <div class="feature-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-400 to-purple-600 dark:from-purple-500 dark:to-purple-700 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg">
                        <i class="fas fa-bell text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Напоминания</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Система напоминает о предстоящих встречах, чтобы вы ничего не забыли и клиенты были довольны.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card card-hover glass-card p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-xl">
                    <div class="feature-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 dark:from-yellow-500 dark:to-yellow-700 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg">
                        <i class="fas fa-chart-line text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Аналитика</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Отслеживайте статистику по клиентам, записям и доходам. Видите, что работает лучше всего.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card card-hover glass-card p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-xl">
                    <div class="feature-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-400 to-pink-600 dark:from-pink-500 dark:to-pink-700 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg">
                        <i class="fas fa-mobile-alt text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Мобильная версия</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Работайте с любого устройства. Полностью адаптированный интерфейс для смартфонов и планшетов.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card card-hover glass-card p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-xl">
                    <div class="feature-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-400 to-blue-600 dark:from-blue-500 dark:to-blue-700 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg">
                        <i class="fas fa-shield-alt text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Безопасность</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Ваши данные защищены. Регулярные резервные копии и шифрование информации.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-12 sm:py-16 md:py-20 relative overflow-hidden">
        <!-- Glass background -->
        <div class="absolute inset-0 bg-gradient-to-br from-white/60 via-indigo-50/40 to-purple-50/40 dark:from-gray-900/80 dark:via-gray-800/80 dark:to-gray-900/80 backdrop-blur-sm"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-10 sm:mb-12 md:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4">Как начать работать с Cliently</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto px-4">
                    Всего 3 простых шага до организованного бизнеса
                </p>
            </div>

            <div class="grid sm:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl font-bold text-white mx-auto mb-4 sm:mb-6 shadow-lg">
                        1
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Регистрация</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Создайте аккаунт за 2 минуты. Никаких сложных настроек и проверок.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl font-bold text-white mx-auto mb-4 sm:mb-6 shadow-lg">
                        2
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Настройка</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Добавьте информацию о бизнесе, услугах и локациях. Простой пошаговый процесс.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl font-bold text-white mx-auto mb-4 sm:mb-6 shadow-lg">
                        3
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 dark:text-white">Работайте</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Принимайте записи и управляйте клиентами эффективно. Всё готово к работе!
                    </p>
                </div>
            </div>

            <div class="text-center mt-8 sm:mt-12">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-2.5 sm:px-8 sm:py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        Перейти в панель
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2.5 sm:px-8 sm:py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        Начать бесплатно
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Pricing Section - ЗАКОММЕНТИРОВАНО (для будущей подписочной системы) -->
    {{--
    <section id="pricing" class="py-20 bg-gray-50 dark:bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Простые и понятные тарифы</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Начните бесплатно, платите только когда бизнес растет
                </p>
            </div>
    --}}

    <!-- Free Section (MVP - все бесплатно) -->
    <section id="pricing" class="py-12 sm:py-16 md:py-20 relative overflow-hidden min-h-[600px] flex items-center">
        <!-- Animated gradient background -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-100/90 via-purple-100/90 to-pink-100/90 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"></div>
        <div class="absolute inset-0 animated-gradient opacity-20 dark:opacity-10"></div>
        
        <!-- Decorative glass blobs -->
        <div class="absolute top-20 left-10 w-96 h-96 bg-gradient-to-r from-purple-400/30 to-pink-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-r from-indigo-400/30 to-blue-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="text-center mb-8 sm:mb-10 md:mb-12">
                <div class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 glass-card rounded-full mb-4 sm:mb-6 shadow-xl">
                    <i class="fas fa-gift text-xl sm:text-2xl text-green-600 dark:text-green-400 mr-2 sm:mr-3"></i>
                    <span class="text-sm sm:text-base md:text-lg font-bold text-green-700 dark:text-green-300">Абсолютно бесплатно</span>
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6 text-gray-900 dark:text-white px-4">
                    Все возможности <span class="gradient-text">бесплатно</span>
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed px-4">
                    Мы только начинаем и хотим помочь вам организовать бизнес. Используйте все функции без ограничений.
                </p>
            </div>

            <div class="glass-card rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-6 md:p-8 lg:p-12">
                <div class="text-center mb-6 sm:mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 bg-gradient-to-br from-indigo-500/80 via-purple-500/80 to-pink-500/80 backdrop-blur-xl rounded-2xl sm:rounded-3xl mb-4 sm:mb-6 shadow-2xl border border-white/30">
                        <i class="fas fa-infinity text-2xl sm:text-3xl md:text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Без ограничений</h3>
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 mb-6 sm:mb-8">
                        Все функции доступны каждому пользователю
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-3 sm:gap-4 md:gap-6 mb-6 sm:mb-8 md:mb-10">
                    <div class="flex items-start space-x-3 sm:space-x-4 p-3 sm:p-4 glass-card rounded-lg sm:rounded-xl">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-indigo-600 dark:text-indigo-400 text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white mb-1">Неограниченное количество клиентов</h4>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Добавляйте столько клиентов, сколько нужно</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 sm:space-x-4 p-3 sm:p-4 glass-card rounded-lg sm:rounded-xl">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-green-100/80 dark:bg-green-900/40 backdrop-blur-sm rounded-lg flex items-center justify-center border border-green-200/50 dark:border-green-800/30">
                            <i class="fas fa-calendar-check text-green-600 dark:text-green-400 text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white mb-1">Онлайн-запись</h4>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Клиенты записываются самостоятельно</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 sm:space-x-4 p-3 sm:p-4 glass-card rounded-lg sm:rounded-xl">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-purple-100/80 dark:bg-purple-900/40 backdrop-blur-sm rounded-lg flex items-center justify-center border border-purple-200/50 dark:border-purple-800/30">
                            <i class="fas fa-bell text-purple-600 dark:text-purple-400 text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white mb-1">Напоминания</h4>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Не забудьте о важных встречах</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3 sm:space-x-4 p-3 sm:p-4 glass-card rounded-lg sm:rounded-xl">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-yellow-100/80 dark:bg-yellow-900/40 backdrop-blur-sm rounded-lg flex items-center justify-center border border-yellow-200/50 dark:border-yellow-800/30">
                            <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white mb-1">Аналитика</h4>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Отслеживайте статистику бизнеса</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-pulse shine inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 md:px-10 md:py-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600 text-white text-sm sm:text-base md:text-lg font-bold rounded-xl shadow-2xl hover:shadow-indigo-500/50 transition-all transform hover:scale-105">
                            <span class="relative z-10">Перейти в панель</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-pulse shine inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 md:px-10 md:py-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600 text-white text-sm sm:text-base md:text-lg font-bold rounded-xl shadow-2xl hover:shadow-indigo-500/50 transition-all transform hover:scale-105">
                            <span class="relative z-10">Начать бесплатно</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Старый блок тарифов (закомментирован для будущей подписочной системы)
    <section id="pricing" class="py-20 bg-gray-50 dark:bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Простые и понятные тарифы</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Начните бесплатно, платите только когда бизнес растет
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Тариф 1: Старт -->
                <div class="card-hover bg-white dark:bg-gray-800 rounded-3xl shadow-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 dark:bg-green-900/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="p-8 relative z-10">
                        <div class="mb-6">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-full mb-4">
                                <span class="text-sm font-bold text-green-700 dark:text-green-300">БЕСПЛАТНО</span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Старт</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Для начала работы</p>
                        </div>
                        <div class="mb-6">
                            <div class="flex items-baseline">
                                <span class="text-5xl font-bold text-gray-900 dark:text-white">0</span>
                                <span class="text-lg text-gray-600 dark:text-gray-400 ml-2">BYN</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">навсегда</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">До 30 клиентов</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Онлайн-запись</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">SMS-напоминания (50/мес)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Базовая статистика</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-4 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 hover:from-gray-200 hover:to-gray-300 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-900 dark:text-white font-semibold rounded-xl transition-all transform hover:scale-105 shadow-md">
                            Начать бесплатно
                        </a>
                    </div>
                </div>

                <!-- Тариф 2: Профи -->
                <div class="card-hover bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-800 rounded-3xl shadow-2xl border-2 border-indigo-500 dark:border-indigo-600 relative overflow-hidden glow transform scale-105">
                    <div class="absolute top-0 right-0 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-xs font-bold px-5 py-2.5 rounded-bl-2xl shadow-lg">
                        ВЫГОДНО
                    </div>
                    <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-200 dark:bg-indigo-900/30 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                    <div class="p-8 relative z-10">
                        <div class="mb-6">
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Профи</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Для растущего бизнеса</p>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-baseline">
                                <span class="text-5xl font-bold text-gray-900 dark:text-white">29</span>
                                <span class="text-lg text-gray-600 dark:text-gray-400 ml-2">BYN</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">в месяц или 290 BYN за год</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Неограниченно клиентов</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Расширенная аналитика</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">SMS-напоминания (200/мес)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Приоритетная поддержка</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Интеграция с Telegram</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn-pulse shine block w-full text-center px-6 py-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-xl hover:shadow-2xl transition-all transform hover:scale-105">
                            <span class="relative z-10">Попробовать 14 дней</span>
                        </a>
                    </div>
                </div>

                <!-- Тариф 3: Премиум -->
                <div class="card-hover bg-white dark:bg-gray-800 rounded-3xl shadow-xl border-2 border-purple-200 dark:border-purple-800 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-100 dark:bg-purple-900/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="p-8 relative z-10">
                        <div class="mb-6">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-full mb-4">
                                <span class="text-sm font-bold text-purple-700 dark:text-purple-300">ДЛЯ ПРОФИ</span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Премиум</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Максимальные возможности</p>
                        </div>
                        <div class="mb-6">
                            <div class="flex items-baseline">
                                <span class="text-5xl font-bold text-gray-900 dark:text-white">49</span>
                                <span class="text-lg text-gray-600 dark:text-gray-400 ml-2">BYN</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">в месяц или 490 BYN за год</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Всё из Профи</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Неограниченные SMS</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Интеграция с Instagram</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">Персональный менеджер</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">API доступ</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-900 dark:from-gray-700 dark:to-gray-800 hover:from-gray-900 hover:to-black dark:hover:from-gray-600 dark:hover:to-gray-700 text-white font-semibold rounded-xl transition-all transform hover:scale-105 shadow-lg">
                            Попробовать 14 дней
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    --}}

    <!-- FAQ Section -->
    <section id="faq" class="py-12 sm:py-16 md:py-20 relative overflow-hidden">
        <!-- Glass background -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50/80 via-indigo-50/50 to-purple-50/50 dark:from-gray-900/90 dark:via-gray-800/90 dark:to-gray-900/90 backdrop-blur-sm"></div>
        <!-- Decorative glass blobs -->
        <div class="absolute top-10 right-20 w-96 h-96 bg-indigo-300/30 dark:bg-indigo-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob"></div>
        <div class="absolute bottom-10 left-20 w-96 h-96 bg-purple-300/30 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob animation-delay-2000"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-10 sm:mb-12 md:mb-16">
                <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl sm:rounded-2xl mb-4 sm:mb-6 shadow-lg">
                    <i class="fas fa-question-circle text-xl sm:text-2xl text-white"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 text-gray-900 dark:text-white px-4">
                    Частые <span class="gradient-text">вопросы</span>
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 px-4">
                    Ответы на самые популярные вопросы о Cliently
                </p>
            </div>

            <div class="space-y-3 sm:space-y-4">
                <!-- FAQ 1 -->
                <div class="faq-item card-hover glass-card rounded-xl sm:rounded-2xl overflow-hidden shadow-xl">
                    <button class="faq-question w-full flex items-center gap-3 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5 text-left hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-purple-50/50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all group">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-rocket text-sm sm:text-base text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex-1 pr-2 sm:pr-4">Сложно ли начать пользоваться?</h3>
                        <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/50 transition-colors">
                            <i class="fas fa-chevron-down text-indigo-600 dark:text-indigo-400 transition-transform text-xs sm:text-sm"></i>
                        </div>
                    </button>
                    <div class="faq-answer">
                        <div class="px-4 pb-4 sm:px-6 sm:pb-6 sm:pl-20 text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                            <p>Нет! Cliently создан специально для простоты. После регистрации вы сразу можете начать добавлять клиентов. Интерфейс интуитивно понятен и не требует обучения. Начните работать за 5 минут!</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item card-hover glass-card rounded-xl sm:rounded-2xl overflow-hidden shadow-xl">
                    <button class="faq-question w-full flex items-center gap-3 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5 text-left hover:bg-gradient-to-r hover:from-green-50/50 hover:to-emerald-50/50 dark:hover:from-green-900/20 dark:hover:to-emerald-900/20 transition-all group">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-infinity text-sm sm:text-base text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex-1 pr-2 sm:pr-4">Есть ли ограничения?</h3>
                        <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                            <i class="fas fa-chevron-down text-green-600 dark:text-green-400 transition-transform text-xs sm:text-sm"></i>
                        </div>
                    </button>
                    <div class="faq-answer">
                        <div class="px-4 pb-4 sm:px-6 sm:pb-6 sm:pl-20 text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                            <p>Нет! Все функции доступны абсолютно бесплатно. Вы можете добавлять неограниченное количество клиентов, использовать онлайн-запись, напоминания и аналитику без каких-либо ограничений.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item card-hover glass-card rounded-xl sm:rounded-2xl overflow-hidden shadow-xl">
                    <button class="faq-question w-full flex items-center gap-3 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5 text-left hover:bg-gradient-to-r hover:from-purple-50/50 hover:to-pink-50/50 dark:hover:from-purple-900/20 dark:hover:to-pink-900/20 transition-all group">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-database text-sm sm:text-base text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex-1 pr-2 sm:pr-4">Можно ли перенести данные при переходе с других сервисов?</h3>
                        <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                            <i class="fas fa-chevron-down text-purple-600 dark:text-purple-400 transition-transform text-xs sm:text-sm"></i>
                        </div>
                    </button>
                    <div class="faq-answer">
                        <div class="px-4 pb-4 sm:px-6 sm:pb-6 sm:pl-20 text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                            <p>Пока мы не поддерживаем автоматический импорт из других CRM, но вы можете легко добавить клиентов вручную или обратиться в поддержку за помощью с переносом данных. Мы поможем вам мигрировать!</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item card-hover glass-card rounded-xl sm:rounded-2xl overflow-hidden shadow-xl">
                    <button class="faq-question w-full flex items-center gap-3 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5 text-left hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-cyan-50/50 dark:hover:from-blue-900/20 dark:hover:to-cyan-900/20 transition-all group">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-mobile-alt text-sm sm:text-base text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex-1 pr-2 sm:pr-4">Есть ли мобильное приложение?</h3>
                        <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                            <i class="fas fa-chevron-down text-blue-600 dark:text-blue-400 transition-transform text-xs sm:text-sm"></i>
                        </div>
                    </button>
                    <div class="faq-answer">
                        <div class="px-4 pb-4 sm:px-6 sm:pb-6 sm:pl-20 text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                            <p>Пока мы работаем над мобильным приложением. Наш сайт полностью адаптирован для мобильных устройств и работает как приложение. Вы можете добавить его на главный экран вашего смартфона для быстрого доступа.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="faq-item card-hover glass-card rounded-xl sm:rounded-2xl overflow-hidden shadow-xl">
                    <button class="faq-question w-full flex items-center gap-3 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5 text-left hover:bg-gradient-to-r hover:from-yellow-50/50 hover:to-orange-50/50 dark:hover:from-yellow-900/20 dark:hover:to-orange-900/20 transition-all group">
                        <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-headset text-sm sm:text-base text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex-1 pr-2 sm:pr-4">Как получить помощь?</h3>
                        <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 dark:group-hover:bg-yellow-800/50 transition-colors">
                            <i class="fas fa-chevron-down text-yellow-600 dark:text-yellow-400 transition-transform text-xs sm:text-sm"></i>
                        </div>
                    </button>
                    <div class="faq-answer">
                        <div class="px-4 pb-4 sm:px-6 sm:pb-6 sm:pl-20 text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                            <p>Мы всегда готовы помочь! Напишите нам на <a href="mailto:support@cliently.by" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">support@cliently.by</a> или свяжитесь через форму обратной связи. Мы отвечаем в течение 24 часов.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500 text-white relative overflow-hidden">
        <div class="absolute inset-0 animated-gradient opacity-20"></div>
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 sm:mb-6 px-4">Готовы организовать свой бизнес?</h2>
            <p class="text-base sm:text-lg md:text-xl mb-6 sm:mb-8 opacity-90 px-4">
                Присоединяйтесь к мастерам, которые уже работают эффективнее с Cliently
            </p>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-pulse shine inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 md:px-10 md:py-5 bg-white text-indigo-600 hover:bg-gray-100 font-bold rounded-xl sm:rounded-2xl shadow-2xl hover:shadow-white/50 transition-all text-sm sm:text-base md:text-lg transform hover:scale-105">
                    <span class="relative z-10">Перейти в панель управления</span>
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-pulse shine inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 md:px-10 md:py-5 bg-white text-indigo-600 hover:bg-gray-100 font-bold rounded-xl sm:rounded-2xl shadow-2xl hover:shadow-white/50 transition-all text-sm sm:text-base md:text-lg transform hover:scale-105">
                    <span class="relative z-10">Начать бесплатно</span>
                </a>
                <p class="mt-4 sm:mt-6 text-xs sm:text-sm opacity-90 font-medium">Никаких платежных данных не требуется</p>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="sm:col-span-2 md:col-span-1">
                    <div class="flex items-center space-x-2 mb-3 sm:mb-4">
                        <!-- Логознак: мастер + клиент -->
                        <div class="relative flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center flex-shrink-0">
                            <!-- Левый круг (мастер) -->
                            <span class="absolute h-6 w-6 sm:h-7 sm:w-7 rounded-full border-2 border-indigo-400 left-0"></span>
                            <!-- Правый круг (клиент) -->
                            <span class="absolute h-6 w-6 sm:h-7 sm:w-7 rounded-full border-2 border-rose-400 right-0"></span>
                            <!-- Пересечение -->
                            <span class="absolute h-5 w-5 sm:h-6 sm:w-6 rounded-full bg-indigo-400/20"></span>
                        </div>
                        <span class="text-lg sm:text-xl font-bold text-white">CLIENTLY</span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-400 mb-3 sm:mb-4">
                        Простая CRM для самозанятых и мастеров. Организуйте клиентов и записи без сложностей.
                    </p>
                    <div class="flex space-x-3 sm:space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-telegram text-base sm:text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-base sm:text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-vk text-base sm:text-lg"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white text-sm sm:text-base font-semibold mb-3 sm:mb-4">Сервис</h3>
                    <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Возможности</a></li>
                        <li><a href="#how-it-works" class="hover:text-white transition-colors">Как это работает</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">Тарифы</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white text-sm sm:text-base font-semibold mb-3 sm:mb-4">Поддержка</h3>
                    <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm">
                        <li><a href="mailto:support@cliently.by" class="hover:text-white transition-colors">Помощь</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Инструкции</a></li>
                        <li><a href="mailto:hello@cliently.by" class="hover:text-white transition-colors">Контакты</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white text-sm sm:text-base font-semibold mb-3 sm:mb-4">Контакты</h3>
                    <ul class="space-y-1.5 sm:space-y-2 text-xs sm:text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-phone text-gray-400 mr-2 sm:mr-3 mt-0.5 sm:mt-1 text-xs"></i>
                            <span class="break-all">+375291234567</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-gray-400 mr-2 sm:mr-3 mt-0.5 sm:mt-1 text-xs"></i>
                            <span class="break-all">hello@cliently.by</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 sm:pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs sm:text-sm text-gray-400 text-center md:text-left">© 2024 Cliently.by. Все права защищены.</p>
                <div class="flex space-x-4 sm:space-x-6 text-xs sm:text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Оферта</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Конфиденциальность</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" class="scroll-to-top glass-card shadow-2xl" aria-label="Вернуться наверх">
        <i class="fas fa-arrow-up text-indigo-600 dark:text-indigo-400 text-lg"></i>
    </button>

    <script>
        // Welcome App JavaScript
        // Apply theme immediately (before DOMContentLoaded)
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (prefersDark ? 'dark' : 'light');
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        class WelcomeApp {
            constructor() {
                this.init();
            }

            init() {
                // If DOM is already loaded, run immediately
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        this.initTheme();
                        this.initMobileMenu();
                        this.initFAQ();
                        this.initSmoothScroll();
                        this.initScrollToTop();
                    });
                } else {
                    // DOM already loaded
                    this.initTheme();
                    this.initMobileMenu();
                    this.initFAQ();
                    this.initSmoothScroll();
                    this.initScrollToTop();
                }
            }

            // Theme Management
            initTheme() {
                const themeToggle = document.getElementById('theme-toggle');
                if (themeToggle) {
                    themeToggle.addEventListener('click', () => this.toggleTheme());
                }
            }

            setTheme(theme) {
                const html = document.documentElement;
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
                localStorage.setItem('theme', theme);
            }

            toggleTheme() {
                const html = document.documentElement;
                const isDark = html.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';
                this.setTheme(newTheme);
            }

            // Mobile Menu Management
            initMobileMenu() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenuClose = document.getElementById('mobile-menu-close');
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
                const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

                if (mobileMenuButton) {
                    mobileMenuButton.addEventListener('click', () => this.openMobileMenu());
                }

                if (mobileMenuClose) {
                    mobileMenuClose.addEventListener('click', () => this.closeMobileMenu());
                }

                if (mobileMenuOverlay) {
                    mobileMenuOverlay.addEventListener('click', () => this.closeMobileMenu());
                }

                mobileNavLinks.forEach(link => {
                    link.addEventListener('click', () => this.closeMobileMenu());
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('open')) {
                        this.closeMobileMenu();
                    }
                });
            }

            openMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
                
                if (mobileMenu) mobileMenu.classList.add('open');
                if (mobileMenuOverlay) mobileMenuOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            closeMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
                
                if (mobileMenu) mobileMenu.classList.remove('open');
                if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            // FAQ Management
            initFAQ() {
                const faqQuestions = document.querySelectorAll('.faq-question');

                faqQuestions.forEach(question => {
                    question.addEventListener('click', () => this.toggleFAQItem(question));
                });
            }

            toggleFAQItem(clickedQuestion) {
                const faqItem = clickedQuestion.closest('.faq-item');
                if (!faqItem) return;

                const isActive = faqItem.classList.contains('active');

                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });

                if (!isActive) {
                    faqItem.classList.add('active');
                }
            }

            // Smooth Scroll Management
            initSmoothScroll() {
                const scrollAnchors = document.querySelectorAll('a[href^="#"]');

                scrollAnchors.forEach(anchor => {
                    anchor.addEventListener('click', (e) => {
                        const targetId = anchor.getAttribute('href');
                        
                        if (targetId === '#' || !targetId) return;

                        e.preventDefault();
                        const target = document.querySelector(targetId);

                        if (target) {
                            const headerOffset = 80;
                            const elementPosition = target.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });

                            history.pushState(null, null, targetId);
                        }
                    });
                });
            }

            // Scroll to Top Button
            initScrollToTop() {
                const scrollToTopButton = document.getElementById('scroll-to-top');
                
                if (!scrollToTopButton) return;

                // Show/hide button based on scroll position
                const toggleScrollButton = () => {
                    if (window.pageYOffset > 300) {
                        scrollToTopButton.classList.add('visible');
                    } else {
                        scrollToTopButton.classList.remove('visible');
                    }
                };

                // Initial check
                toggleScrollButton();

                // Listen to scroll events
                window.addEventListener('scroll', toggleScrollButton, { passive: true });

                // Scroll to top on click
                scrollToTopButton.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        }

        // Initialize the app
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new WelcomeApp();
            });
        } else {
            new WelcomeApp();
        }
    </script>
</body>
</html>
