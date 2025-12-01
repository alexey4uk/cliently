<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Простая CRM для самозанятых и мастеров</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicons links -->
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicons/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/favicons/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicons/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="CLIENTLY" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .faq-answer {
            display: none;
        }
        .faq-item.active .faq-answer {
            display: block;
        }
        .faq-item.active .fa-chevron-down {
            transform: rotate(180deg);
        }

        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
        .mobile-menu-overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }
        .mobile-menu-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body class="font-sans bg-gradient-to-br bg-gray-50 dark:bg-gray-900 from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
<!-- Header -->
<header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-sm sticky top-0 z-40 border-b border-gray-200/50 dark:border-gray-700/50">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Логотип и бренд -->
            <div class="flex items-center space-x-4">
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white p-2 rounded-xl group-hover:from-blue-600 group-hover:to-purple-700 dark:group-hover:from-blue-700 dark:group-hover:to-purple-800 transition-all duration-300 shadow-md">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent leading-tight">CLIENTLY</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">CRM для мастеров</span>
                    </div>
                </a>
            </div>

            <!-- Основная навигация -->
            <nav class="hidden lg:flex items-center space-x-1">
                <a href="#features" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-2 px-4 rounded-xl transition-all duration-300">
                    <i class="fas fa-star text-sm w-5 text-center"></i>
                    <span>Возможности</span>
                </a>

                <a href="#how-it-works" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-2 px-4 rounded-xl transition-all duration-300">
                    <i class="fas fa-play-circle text-sm w-5 text-center"></i>
                    <span>Как работает</span>
                </a>

                <a href="#pricing" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-2 px-4 rounded-xl transition-all duration-300">
                    <i class="fas fa-tag text-sm w-5 text-center"></i>
                    <span>Тарифы</span>
                </a>

                <a href="#faq" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-2 px-4 rounded-xl transition-all duration-300">
                    <i class="fas fa-question-circle text-sm w-5 text-center"></i>
                    <span>Вопросы</span>
                </a>
            </nav>

            <!-- Правая часть: действия и кнопки -->
            <div class="flex items-center space-x-3">
                <!-- Переключение темы -->
                <button id="theme-toggle-desktop" class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-all duration-300">
                    <svg id="theme-light-icon-desktop" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-dark-icon-desktop" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                @auth
                    <!-- Для авторизованных пользователей -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Информация о пользователе -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white rounded-full flex items-center justify-center text-sm font-semibold shadow-md">
                                {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                            </div>
                            <div class="hidden lg:flex flex-col">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 leading-tight">
                                    {{ Auth::user()->email }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Аккаунт активен</span>
                            </div>
                        </div>

                        <!-- Разделитель -->
                        <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

                        <!-- Кнопки действий -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white font-medium py-2 px-4 rounded-xl transition-all duration-300 shadow-md">
                                <i class="fas fa-tachometer-alt text-sm"></i>
                                <span class="hidden sm:inline">Панель управления</span>
                                <span class="sm:hidden">Панель</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="hidden xl:block">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium py-2 px-3 rounded-xl transition-all duration-300">
                                    <i class="fas fa-sign-out-alt text-sm"></i>
                                    <span>Выйти</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Мобильный вариант для авторизованных -->
                    <div class="md:hidden flex items-center space-x-2">
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white font-medium py-2 px-3 rounded-xl transition-all duration-300 shadow-md">
                            <i class="fas fa-tachometer-alt"></i>
                        </a>
                    </div>

                @else
                    <!-- Для гостей -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-2 px-4 rounded-xl transition-all duration-300">
                            <i class="fas fa-sign-in-alt text-sm"></i>
                            <span>Войти</span>
                        </a>

                        <a href="{{ route('register') }}" class="flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white font-medium py-2 px-4 rounded-xl transition-all duration-300 shadow-md">
                            <i class="fas fa-user-plus text-sm"></i>
                            <span>Регистрация</span>
                        </a>
                    </div>

                    <!-- Мобильный вариант для гостей -->
                    <div class="md:hidden flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium p-2 rounded-xl transition-all duration-300">
                            <i class="fas fa-sign-in-alt text-lg"></i>
                        </a>
                    </div>
                @endauth

                <!-- Кнопка мобильного меню -->
                <button class="lg:hidden text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-xl transition-all duration-300" id="mobileMenuButton">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="mobileMenuOverlay" class="mobile-menu-overlay fixed inset-0 bg-black bg-opacity-50 z-50 md:hidden"></div>

<div id="mobileMenu" class="mobile-menu fixed top-0 left-0 w-80 h-full bg-white dark:bg-gray-800 shadow-xl z-50 md:hidden">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <!-- Заголовок меню -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white p-2 rounded-xl">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <span class="text-xl font-semibold text-gray-800 dark:text-white block">CLIENTLY</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 block">CRM для мастеров</span>
                </div>
            </div>
            <button id="mobileMenuClose" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Основная навигация -->
        <nav class="space-y-2 mb-6">
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 px-2">
                Навигация
            </div>

            <a href="#features" class="flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link">
                <i class="fas fa-star text-gray-400 w-5 text-center"></i>
                <span>Возможности</span>
            </a>

            <a href="#how-it-works" class="flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link">
                <i class="fas fa-play-circle text-gray-400 w-5 text-center"></i>
                <span>Как работает</span>
            </a>

            <a href="#pricing" class="flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link">
                <i class="fas fa-tag text-gray-400 w-5 text-center"></i>
                <span>Тарифы</span>
            </a>

            <a href="#faq" class="flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link">
                <i class="fas fa-question-circle text-gray-400 w-5 text-center"></i>
                <span>Вопросы</span>
            </a>
        </nav>

        <!-- Аккаунт пользователя -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 px-2">
                Аккаунт
            </div>

            @auth
                <!-- Информация о пользователе -->
                <div class="flex items-center space-x-3 mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white rounded-full flex items-center justify-center font-semibold shadow-md">
                        {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 dark:text-white truncate">
                            {{ Auth::user()->email }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Аккаунт активен
                        </div>
                    </div>
                </div>

                <!-- Действия для авторизованного пользователя -->
                <div class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link">
                        <i class="fas fa-tachometer-alt text-current w-5 text-center"></i>
                        <span>Панель управления</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 text-left">
                            <i class="fas fa-sign-out-alt text-current w-5 text-center"></i>
                            <span>Выйти из аккаунта</span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Действия для гостя -->
                <div class="space-y-3">
                    <a href="{{ route('login') }}" class="flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link">
                        <i class="fas fa-sign-in-alt text-gray-400 w-5 text-center"></i>
                        <span>Войти в аккаунт</span>
                    </a>

                    <a href="{{ route('register') }}" class="flex items-center space-x-3 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 font-medium py-3 px-4 rounded-xl transition-all duration-300 mobile-nav-link justify-center shadow-md">
                        <i class="fas fa-user-plus text-current w-5 text-center"></i>
                        <span>Создать аккаунт</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 dark:from-blue-700 dark:via-purple-800 dark:to-indigo-900 text-white py-16 md:py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center relative z-10">
        <div class="md:w-1/2 mb-10 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Простая CRM для самозанятых и мастеров</h1>
            <p class="text-xl mb-8 opacity-90">Cliently помогает организовать клиентов и записи без сложностей. Начните работать эффективнее уже сегодня.</p>

            <div class="mb-8 space-y-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-yellow-300 mr-3"></i>
                    <span>Учет клиентов и истории обращений</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-yellow-300 mr-3"></i>
                    <span>Онлайн-запись от клиентов</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-yellow-300 mr-3"></i>
                    <span>Напоминания о встречах</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-yellow-300 mr-3"></i>
                    <span>Простота и удобство</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-6 py-3 rounded-xl font-bold text-center transition-all duration-300 shadow-lg hover:shadow-xl">
                        Перейти в панель
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-6 py-3 rounded-xl font-bold text-center transition-all duration-300 shadow-lg hover:shadow-xl">
                        Попробовать бесплатно
                    </a>
                @endauth
                <a href="#features" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-white px-6 py-3 rounded-xl font-medium text-center transition-all duration-300">
                    Узнать больше
                </a>
            </div>
        </div>

        <div class="md:w-1/2 flex justify-center">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-4 transform rotate-1 max-w-md border border-white/20">
                <div class="bg-gray-800 dark:bg-gray-900 text-white p-3 rounded-t-lg flex justify-between items-center">
                    <div class="flex space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <span class="text-sm ml-28">Мои клиенты</span>
                </div>
                <div class="p-4 bg-gray-100/10 backdrop-blur-lg rounded-b-lg">
                    <div class="bg-white/20 backdrop-blur-lg p-4 rounded-xl shadow mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-bold text-white">Сегодня</div>
                            <div class="text-yellow-300 text-sm">3 записи</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-white/20">
                                <div>
                                    <div class="font-medium text-white">Анна К.</div>
                                    <div class="text-gray-200 text-xs">Стрижка • 14:00</div>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-white/20">
                                <div>
                                    <div class="font-medium text-white">Мария С.</div>
                                    <div class="text-gray-200 text-xs">Маникюр • 16:30</div>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <div>
                                    <div class="font-medium text-white">Ирина П.</div>
                                    <div class="text-gray-200 text-xs">Консультация • 18:00</div>
                                </div>
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/20 backdrop-blur-lg p-3 rounded-xl shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-bold text-white">Новые клиенты</div>
                            <div class="text-yellow-300 text-sm">+2</div>
                        </div>
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full border-2 border-white/50"></div>
                            <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full border-2 border-white/50"></div>
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full border-2 border-white/50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- For Whom Section -->
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800 dark:text-white">Cliently создан специально для</h2>

        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="text-center group">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-300 shadow-lg">
                    <i class="fas fa-scissors text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Парикмахеры</h3>
                <p class="text-gray-600 dark:text-gray-300">Управляйте записями клиентов и их предпочтениями</p>
            </div>

            <div class="text-center group">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 dark:from-green-600 dark:to-teal-700 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-300 shadow-lg">
                    <i class="fas fa-spa text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Косметологи</h3>
                <p class="text-gray-600 dark:text-gray-300">Отслеживайте процедуры и историю посещений</p>
            </div>

            <div class="text-center group">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 dark:from-purple-600 dark:to-pink-700 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-300 shadow-lg">
                    <i class="fas fa-hands text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Мастера маникюра</h3>
                <p class="text-gray-600 dark:text-gray-300">Организуйте записи и предпочтения клиентов</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-16 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4 text-gray-800 dark:text-white">Все необходимое для вашего бизнеса</h2>
        <p class="text-gray-600 dark:text-gray-400 text-center max-w-2xl mx-auto mb-12">Мы сосредоточились на самом важном, чтобы вы могли работать эффективнее</p>

        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start">
                    <div class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 p-3 rounded-xl mr-5 group-hover:scale-110 transition-all duration-300">
                        <i class="fas fa-address-book text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">База клиентов</h3>
                        <p class="text-gray-600 dark:text-gray-300">Храните контакты, заметки и историю обращений всех ваших клиентов в одном месте. Больше не нужно искать в чатах и заметках.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start">
                    <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 p-3 rounded-xl mr-5 group-hover:scale-110 transition-all duration-300">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Онлайн-запись</h3>
                        <p class="text-gray-600 dark:text-gray-300">Клиенты могут записываться самостоятельно через вашу ссылку. Вы получаете уведомления о новых записях.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start">
                    <div class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 p-3 rounded-xl mr-5 group-hover:scale-110 transition-all duration-300">
                        <i class="fas fa-bell text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Напоминания</h3>
                        <p class="text-gray-600 dark:text-gray-300">Система напоминает о предстоящих встречах, чтобы вы ничего не забыли и клиенты были довольны.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start">
                    <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 p-3 rounded-xl mr-5 group-hover:scale-110 transition-all duration-300">
                        <i class="fas fa-mobile-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Простота использования</h3>
                        <p class="text-gray-600 dark:text-gray-300">Интуитивно понятный интерфейс, который не требует обучения. Начните работать за 5 минут после регистрации.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-16 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4 text-gray-800 dark:text-white">Как начать работать с Cliently</h2>
        <p class="text-gray-600 dark:text-gray-400 text-center max-w-2xl mx-auto mb-12">Всего 3 простых шага до организованного бизнеса</p>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Step 1 -->
            <div class="text-center group">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4 group-hover:scale-110 transition-all duration-300 shadow-lg">1</div>
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Регистрация</h3>
                <p class="text-gray-600 dark:text-gray-400">Создайте аккаунт за 2 минуты. Никаких сложных настроек.</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center group">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4 group-hover:scale-110 transition-all duration-300 shadow-lg">2</div>
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Добавьте клиентов</h3>
                <p class="text-gray-600 dark:text-gray-400">Перенесите контакты из телефона или добавляйте постепенно.</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center group">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4 group-hover:scale-110 transition-all duration-300 shadow-lg">3</div>
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Работайте</h3>
                <p class="text-gray-600 dark:text-gray-400">Принимайте записи и управляйте клиентами эффективно.</p>
            </div>
        </div>

        <div class="text-center mt-12">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                    Перейти в панель
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                    Начать бесплатно
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-16 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4 text-gray-800 dark:text-white">Простые и понятные тарифы</h2>
        <p class="text-gray-600 dark:text-gray-400 text-center max-w-2xl mx-auto mb-12">Начните бесплатно, платите только когда бизнес растет. Все тарифы включают 14 дней бесплатного пробного периода.</p>

        <div class="grid md:grid-cols-3 gap-6 max-w-6xl mx-auto">
            <!-- Тариф 1: Бесплатный -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                <div class="p-8 border-b border-gray-200 dark:border-gray-700 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Старт</h3>
                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium px-3 py-1 rounded-full">БЕСПЛАТНО</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Для начала работы и тестирования</p>

                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-800 dark:text-white">0</span>
                        <span class="text-gray-600 dark:text-gray-400">BYN/месяц</span>
                    </div>

                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-center py-3 rounded-xl font-medium transition-all duration-300">
                            Использую
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-center py-3 rounded-xl font-medium transition-all duration-300">
                            Начать бесплатно
                        </a>
                    @endauth
                </div>

                <div class="p-8 pt-0">
                    <h4 class="font-semibold text-gray-800 dark:text-white mb-4">Что включено:</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">До 30 клиентов в базе</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Онлайн-запись от клиентов</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">SMS-напоминания (до 50/мес)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Базовая статистика</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Тариф 2: Профи (Популярный) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-blue-500 dark:border-blue-600 relative overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                <div class="absolute top-0 right-0 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white text-xs font-bold px-4 py-2 rounded-bl-lg">
                    ВЫГОДНО
                </div>
                <div class="p-8 border-b border-gray-200 dark:border-gray-700 flex-grow">
                    <h3 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">Профи</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Для растущего бизнеса</p>

                    <div class="mb-4">
                        <span class="text-4xl font-bold text-gray-800 dark:text-white">29</span>
                        <span class="text-gray-600 dark:text-gray-400">BYN/месяц</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">или 290 BYN за год (экономия 58 BYN)</p>

                    @auth
                        <a href="#" class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white text-center py-3 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                            Перейти на Профи
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 text-white text-center py-3 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                            Попробовать 14 дней бесплатно
                        </a>
                    @endauth
                </div>

                <div class="p-8 pt-0">
                    <h4 class="font-semibold text-gray-800 dark:text-white mb-4">Все в тарифе Старт +:</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Неограниченное количество клиентов</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Расширенная аналитика и отчеты</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">SMS-напоминания (до 200/мес)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Приоритетная поддержка</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Интеграция с телеграм</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Тариф 3: Премиум -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                <div class="p-8 border-b border-gray-200 dark:border-gray-700 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Премиум</h3>
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs font-medium px-3 py-1 rounded-full">ДЛЯ ПРОФИ</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Максимальные возможности</p>

                    <div class="mb-4">
                        <span class="text-4xl font-bold text-gray-800 dark:text-white">49</span>
                        <span class="text-gray-600 dark:text-gray-400">BYN/месяц</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">или 490 BYN за год (экономия 98 BYN)</p>

                    @auth
                        <a href="#" class="block w-full bg-gray-800 dark:bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-600 text-white text-center py-3 rounded-xl font-medium transition-all duration-300">
                            Выбрать Премиум
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-gray-800 dark:bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-600 text-white text-center py-3 rounded-xl font-medium transition-all duration-300">
                            Попробовать 14 дней бесплатно
                        </a>
                    @endauth
                </div>

                <div class="p-8 pt-0">
                    <h4 class="font-semibold text-gray-800 dark:text-white mb-4">Все в тарифе Профи +:</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Неограниченные SMS-напоминания</span>
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
                            <span class="text-gray-700 dark:text-gray-300">Кастомные отчеты</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">API доступ</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Остались вопросы по тарифам?</p>
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium inline-flex items-center">
                Свяжитесь с нами
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-16 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4 text-gray-800 dark:text-white">Частые вопросы</h2>
        <p class="text-gray-600 dark:text-gray-400 text-center max-w-2xl mx-auto mb-12">Ответы на самые популярные вопросы о Cliently</p>

        <div class="max-w-3xl mx-auto">
            <!-- FAQ 1 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 mb-6 hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Сложно ли начать пользоваться?</h3>
                    <i class="fas fa-chevron-down text-blue-500 dark:text-blue-400 transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Нет! Cliently создан специально для простоты. После регистрации вы сразу можете начать добавлять клиентов. Интерфейс интуитивно понятен и не требует обучения.</p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 mb-6 hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Что будет, когда клиентов станет больше 30?</h3>
                    <i class="fas fa-chevron-down text-blue-500 dark:text-blue-400 transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Вы сможете продолжить работу с существующими клиентами, но для добавления новых потребуется перейти на тариф "Профи". Мы заранее уведомим вас о приближении к лимиту.</p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 mb-6 hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Можно ли перенести данные при переходе с других сервисов?</h3>
                    <i class="fas fa-chevron-down text-blue-500 dark:text-blue-400 transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Пока мы не поддерживаем автоматический импорт из других CRM, но вы можете легко добавить клиентов вручную или обратиться в поддержку за помощью с переносом данных.</p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Есть ли мобильное приложение?</h3>
                    <i class="fas fa-chevron-down text-blue-500 dark:text-blue-400 transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Пока мы работаем над мобильным приложением. Наш сайт полностью адаптирован для мобильных устройств и работает как приложение. Вы можете добавить его на главный экран вашего смартфона.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-500 via-purple-600 to-indigo-700 dark:from-blue-700 dark:via-purple-800 dark:to-indigo-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl font-bold mb-6">Готовы организовать свой бизнес?</h2>
        <p class="text-xl max-w-2xl mx-auto mb-8 opacity-90">Присоединяйтесь к мастерам, которые уже работают эффективнее с Cliently</p>
        @auth
            <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-8 py-3 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                Перейти в панель управления
            </a>
        @else
            <a href="{{ route('register') }}" class="bg-white text-blue-600 dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-8 py-3 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                Начать бесплатно 14 дней
            </a>
            <p class="mt-4 text-sm opacity-80">Никаких платежных данных не требуется</p>
        @endauth
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Column 1 -->
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white p-2 rounded-xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-xl font-bold">Cliently</span>
                </div>
                <p class="text-gray-400 mb-4">Простая CRM для самозанятых и мастеров. Организуйте клиентов и записи без сложностей.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <i class="fab fa-vk"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2 -->
            <div>
                <h3 class="text-lg font-bold mb-4">Сервис</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-gray-400 hover:text-white transition-colors duration-300">Возможности</a></li>
                    <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition-colors duration-300">Как работает</a></li>
                    <li><a href="#pricing" class="text-gray-400 hover:text-white transition-colors duration-300">Тарифы</a></li>
                    <li><a href="#faq" class="text-gray-400 hover:text-white transition-colors duration-300">Вопросы</a></li>
                </ul>
            </div>

            <!-- Column 3 -->
            <div>
                <h3 class="text-lg font-bold mb-4">Поддержка</h3>
                <ul class="space-y-2">
                    <li><a href="mailto:support@cliently.by" class="text-gray-400 hover:text-white transition-colors duration-300">Помощь</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Инструкции</a></li>
                    <li><a href="mailto:hello@cliently.by" class="text-gray-400 hover:text-white transition-colors duration-300">Контакты</a></li>
                </ul>
            </div>

            <!-- Column 4 -->
            <div>
                <h3 class="text-lg font-bold mb-4">Контакты</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-phone text-gray-400 mr-3 mt-1"></i>
                        <span class="text-gray-400">+375291234567</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope text-gray-400 mr-3 mt-1"></i>
                        <span class="text-gray-400">hello@cliently.by</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 mb-4 md:mb-0">© 2024 Cliently.by. Все права защищены.</p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-300">Оферта</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-300">Конфиденциальность</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
