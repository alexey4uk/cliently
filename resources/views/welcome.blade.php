<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliently - Простая CRM для самозанятых и мастеров</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.svg') }}">
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
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>
<body class="font-sans bg-gray-50 dark:bg-gray-900">
<!-- Header -->
<header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="bg-primary dark:bg-primary-dark text-white p-2 rounded-lg">
                <i class="fas fa-users text-xl"></i>
            </div>
            <span class="text-xl font-bold text-gray-800 dark:text-white">C L I E N T L Y</span>
        </div>

        <nav class="hidden md:flex space-x-8">
            <a href="#features" class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary-dark font-medium">Возможности</a>
            <a href="#how-it-works" class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary-dark font-medium">Как работает</a>
            <a href="#pricing" class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary-dark font-medium">Тарифы</a>
            <a href="#faq" class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary-dark font-medium">Вопросы</a>
        </nav>

        <div class="flex items-center space-x-4">
            @auth
                <div class="text-gray-600 dark:text-gray-300 font-medium hidden md:block">{{ Auth::user()->email }}</div>
                <a href="{{ route('dashboard') }}" class="bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white px-5 py-2 rounded-lg font-medium transition duration-300 hidden md:block">
                    Панель управления
                </a>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary-dark font-medium hidden md:block">Войти</a>
                <a href="{{ route('register') }}" class="bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white px-5 py-2 rounded-lg font-medium transition duration-300 hidden md:block">
                    Регистрация
                </a>
            @endauth

            <!-- Кнопка мобильного меню всегда видна на мобильных -->
            <button class="md:hidden text-gray-600 dark:text-gray-300" id="mobileMenuButton">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="mobileMenuOverlay" class="mobile-menu-overlay fixed inset-0 bg-black bg-opacity-50 z-50 md:hidden"></div>

<div id="mobileMenu" class="mobile-menu fixed top-0 left-0 w-80 h-full bg-white dark:bg-gray-800 shadow-xl z-50 md:hidden">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-2">
                <div class="bg-primary dark:bg-primary-dark text-white p-2 rounded-lg">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <span class="text-xl font-bold text-gray-800 dark:text-white">Cliently</span>
            </div>
            <button id="mobileMenuClose" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <nav class="space-y-6">
            <a href="#features" class="block text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-primary-dark font-medium py-2 border-b border-gray-100 dark:border-gray-700 mobile-nav-link">Возможности</a>
            <a href="#how-it-works" class="block text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-primary-dark font-medium py-2 border-b border-gray-100 dark:border-gray-700 mobile-nav-link">Как работает</a>
            <a href="#pricing" class="block text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-primary-dark font-medium py-2 border-b border-gray-100 dark:border-gray-700 mobile-nav-link">Тарифы</a>
            <a href="#faq" class="block text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-primary-dark font-medium py-2 border-b border-gray-100 dark:border-gray-700 mobile-nav-link">Вопросы</a>

            @auth
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-gray-700 dark:text-gray-200 font-medium py-2">{{ Auth::user()->email }}</div>
                    <a href="{{ route('dashboard') }}" class="block text-primary dark:text-primary-dark hover:text-secondary dark:hover:text-secondary-dark font-medium py-2 mobile-nav-link">
                        Панель управления
                    </a>
                </div>
            @else
                <a href="{{ route('login') }}" class="block text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-primary-dark font-medium py-2 border-b border-gray-100 dark:border-gray-700 mobile-nav-link">Войти</a>
            @endauth
        </nav>
    </div>

    <div class="p-6">
        @auth
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-center py-3 rounded-lg font-medium transition duration-300">
                    Выйти
                </button>
            </form>
        @else
            <a href="{{ route('register') }}" class="w-full bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white text-center py-3 rounded-lg font-medium transition duration-300 block">
                Начать бесплатно
            </a>
        @endauth
    </div>
</div>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary to-secondary dark:from-primary-dark dark:to-secondary-dark text-white py-16 md:py-24">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-10 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Простая CRM для самозанятых и мастеров</h1>
            <p class="text-xl mb-8 opacity-90">Cliently помогает организовать клиентов и записи без сложностей. Начните работать эффективнее уже сегодня.</p>

            <div class="mb-8 space-y-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-accent dark:text-accent-dark mr-3"></i>
                    <span>Учет клиентов и истории обращений</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-accent dark:text-accent-dark mr-3"></i>
                    <span>Онлайн-запись от клиентов</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-accent dark:text-accent-dark mr-3"></i>
                    <span>Напоминания о встречах</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-accent dark:text-accent-dark mr-3"></i>
                    <span>Простота и удобство</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-primary dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-6 py-3 rounded-lg font-bold text-center transition duration-300">
                        Перейти в панель
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-white text-primary dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-6 py-3 rounded-lg font-bold text-center transition duration-300">
                        Попробовать бесплатно
                    </a>
                @endauth
                <a href="#features" class="border-2 border-white text-white hover:bg-white hover:text-primary dark:hover:bg-gray-800 dark:hover:text-white px-6 py-3 rounded-lg font-medium text-center transition duration-300">
                    Узнать больше
                </a>
            </div>
        </div>

        <div class="md:w-1/2 flex justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-4 transform rotate-1 max-w-md">
                <div class="bg-gray-800 dark:bg-gray-900 text-white p-3 rounded-t-lg flex justify-between items-center">
                    <div class="flex space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <span class="ml-10 text-sm">Cliently - Мои клиенты</span>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-b-lg">
                    <div class="bg-white dark:bg-gray-600 p-4 rounded-lg shadow mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-bold text-gray-800 dark:text-white">Сегодня</div>
                            <div class="text-primary dark:text-primary-dark text-sm">3 записи</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-500">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white">Анна К.</div>
                                    <div class="text-gray-600 dark:text-gray-300 text-xs">Стрижка • 14:00</div>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-500">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white">Мария С.</div>
                                    <div class="text-gray-600 dark:text-gray-300 text-xs">Маникюр • 16:30</div>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white">Ирина П.</div>
                                    <div class="text-gray-600 dark:text-gray-300 text-xs">Консультация • 18:00</div>
                                </div>
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-600 p-3 rounded-lg shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-bold text-gray-800 dark:text-white">Новые клиенты</div>
                            <div class="text-primary dark:text-primary-dark text-sm">+2</div>
                        </div>
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full border-2 border-white dark:border-gray-600"></div>
                            <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full border-2 border-white dark:border-gray-600"></div>
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full border-2 border-white dark:border-gray-600"></div>
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
            <div class="text-center">
                <div class="bg-primary dark:bg-primary-dark text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-scissors text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Парикмахеры</h3>
                <p class="text-gray-600 dark:text-gray-300">Управляйте записями клиентов и их предпочтениями</p>
            </div>

            <div class="text-center">
                <div class="bg-secondary dark:bg-secondary-dark text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-spa text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Косметологи</h3>
                <p class="text-gray-600 dark:text-gray-300">Отслеживайте процедуры и историю посещений</p>
            </div>

            <div class="text-center">
                <div class="bg-accent dark:bg-accent-dark text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-hands text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Мастера маникюра</h3>
                <p class="text-gray-600 dark:text-gray-300">Организуйте записи и предпочтения клиентов</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4 text-gray-800 dark:text-white">Все необходимое для вашего бизнеса</h2>
        <p class="text-gray-600 dark:text-gray-400 text-center max-w-2xl mx-auto mb-12">Мы сосредоточились на самом важном, чтобы вы могли работать эффективнее</p>

        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start">
                    <div class="bg-blue-100 dark:bg-blue-900 text-primary dark:text-primary-dark p-3 rounded-lg mr-5">
                        <i class="fas fa-address-book text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">База клиентов</h3>
                        <p class="text-gray-600 dark:text-gray-300">Храните контакты, заметки и историю обращений всех ваших клиентов в одном месте. Больше не нужно искать в чатах и заметках.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start">
                    <div class="bg-green-100 dark:bg-green-900 text-accent dark:text-accent-dark p-3 rounded-lg mr-5">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Онлайн-запись</h3>
                        <p class="text-gray-600 dark:text-gray-300">Клиенты могут записываться самостоятельно через вашу ссылку. Вы получаете уведомления о новых записях.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start">
                    <div class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 p-3 rounded-lg mr-5">
                        <i class="fas fa-bell text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Напоминания</h3>
                        <p class="text-gray-600 dark:text-gray-300">Система напоминает о предстоящих встречах, чтобы вы ничего не забыли и клиенты были довольны.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start">
                    <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 p-3 rounded-lg mr-5">
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
            <div class="text-center">
                <div class="bg-primary dark:bg-primary-dark text-white w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Регистрация</h3>
                <p class="text-gray-600 dark:text-gray-400">Создайте аккаунт за 2 минуты. Никаких сложных настроек.</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="bg-primary dark:bg-primary-dark text-white w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Добавьте клиентов</h3>
                <p class="text-gray-600 dark:text-gray-400">Перенесите контакты из телефона или добавляйте постепенно.</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="bg-primary dark:bg-primary-dark text-white w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Работайте</h3>
                <p class="text-gray-600 dark:text-gray-400">Принимайте записи и управляйте клиентами эффективно.</p>
            </div>
        </div>

        <div class="text-center mt-12">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white px-8 py-3 rounded-lg font-bold transition duration-300">
                    Перейти в панель
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white px-8 py-3 rounded-lg font-bold transition duration-300">
                    Начать бесплатно
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4 text-gray-800 dark:text-white">Простые и понятные тарифы</h2>
        <p class="text-gray-600 dark:text-gray-400 text-center max-w-2xl mx-auto mb-12">Начните бесплатно, платите только когда бизнес растет</p>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Тариф 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">Бесплатный</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Идеально для начала</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-800 dark:text-white">0</span>
                        <span class="text-gray-600 dark:text-gray-400">руб/месяц</span>
                    </div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full bg-gray-100 dark:bg-gray-700 hover:bg-primary dark:hover:bg-primary-dark hover:text-white text-gray-800 dark:text-gray-200 text-center py-3 rounded-lg font-medium transition duration-300">
                            Использую
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-gray-100 dark:bg-gray-700 hover:bg-primary dark:hover:bg-primary-dark hover:text-white text-gray-800 dark:text-gray-200 text-center py-3 rounded-lg font-medium transition duration-300">
                            Начать сейчас
                        </a>
                    @endauth
                </div>
                <div class="p-8">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">До 30 клиентов</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">База клиентов</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Онлайн-запись</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Напоминания</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Тариф 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border-2 border-primary dark:border-primary-dark relative overflow-hidden">
                <div class="bg-primary dark:bg-primary-dark text-white text-center py-2 text-sm font-bold">
                    ПОПУЛЯРНЫЙ
                </div>
                <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">Профи</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Для растущего бизнеса</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-800 dark:text-white">490</span>
                        <span class="text-gray-600 dark:text-gray-400">руб/месяц</span>
                    </div>
                    @auth
                        <a href="#" class="block w-full bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white text-center py-3 rounded-lg font-medium transition duration-300">
                            Перейти на Профи
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-primary dark:bg-primary-dark hover:bg-secondary dark:hover:bg-secondary-dark text-white text-center py-3 rounded-lg font-medium transition duration-300">
                            Попробовать бесплатно
                        </a>
                    @endauth
                </div>
                <div class="p-8">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Неограниченные клиенты</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Расширенная база</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Приоритетная поддержка</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-accent dark:text-accent-dark mr-3 mt-1"></i>
                            <span class="text-gray-700 dark:text-gray-300">Все бесплатные функции</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <p class="text-gray-600 dark:text-gray-400 text-sm">Оба тарифа включают 14 дней бесплатного пробного периода</p>
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
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 mb-6">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Сложно ли начать пользоваться?</h3>
                    <i class="fas fa-chevron-down text-primary dark:text-primary-dark transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Нет! Cliently создан специально для простоты. После регистрации вы сразу можете начать добавлять клиентов. Интерфейс интуитивно понятен и не требует обучения.</p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 mb-6">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Что будет, когда клиентов станет больше 30?</h3>
                    <i class="fas fa-chevron-down text-primary dark:text-primary-dark transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Вы сможете продолжить работу с существующими клиентами, но для добавления новых потребуется перейти на тариф "Профи". Мы заранее уведомим вас о приближении к лимиту.</p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6 mb-6">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Можно ли перенести данные при переходе с других сервисов?</h3>
                    <i class="fas fa-chevron-down text-primary dark:text-primary-dark transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Пока мы не поддерживаем автоматический импорт из других CRM, но вы можете легко добавить клиентов вручную или обратиться в поддержку за помощью с переносом данных.</p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="faq-item bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600 p-6">
                <div class="flex justify-between items-center cursor-pointer faq-question">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Есть ли мобильное приложение?</h3>
                    <i class="fas fa-chevron-down text-primary dark:text-primary-dark transition-transform duration-300"></i>
                </div>
                <div class="faq-answer mt-4 text-gray-600 dark:text-gray-300">
                    <p>Пока мы работаем над мобильным приложением. Наш сайт полностью адаптирован для мобильных устройств и работает как приложение. Вы можете добавить его на главный экран вашего смартфона.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-16 bg-primary dark:bg-primary-dark text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-6">Готовы организовать свой бизнес?</h2>
        <p class="text-xl max-w-2xl mx-auto mb-8 opacity-90">Присоединяйтесь к мастерам, которые уже работают эффективнее с Cliently</p>
        @auth
            <a href="{{ route('dashboard') }}" class="bg-white text-primary dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-8 py-3 rounded-lg font-bold text-lg transition duration-300">
                Перейти в панель управления
            </a>
        @else
            <a href="{{ route('register') }}" class="bg-white text-primary dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-8 py-3 rounded-lg font-bold text-lg transition duration-300">
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
                    <div class="bg-primary dark:bg-primary-dark text-white p-2 rounded-lg">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-xl font-bold">Cliently</span>
                </div>
                <p class="text-gray-400 mb-4">Простая CRM для самозанятых и мастеров. Организуйте клиентов и записи без сложностей.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-vk"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2 -->
            <div>
                <h3 class="text-lg font-bold mb-4">Сервис</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-gray-400 hover:text-white">Возможности</a></li>
                    <li><a href="#how-it-works" class="text-gray-400 hover:text-white">Как работает</a></li>
                    <li><a href="#pricing" class="text-gray-400 hover:text-white">Тарифы</a></li>
                    <li><a href="#faq" class="text-gray-400 hover:text-white">Вопросы</a></li>
                </ul>
            </div>

            <!-- Column 3 -->
            <div>
                <h3 class="text-lg font-bold mb-4">Поддержка</h3>
                <ul class="space-y-2">
                    <li><a href="mailto:support@cliently.by" class="text-gray-400 hover:text-white">Помощь</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Инструкции</a></li>
                    <li><a href="mailto:hello@cliently.by" class="text-gray-400 hover:text-white">Контакты</a></li>
                </ul>
            </div>

            <!-- Column 4 -->
            <div>
                <h3 class="text-lg font-bold mb-4">Контакты</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-envelope text-gray-400 mr-3 mt-1"></i>
                        <span class="text-gray-400">hello@cliently.by</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-globe text-gray-400 mr-3 mt-1"></i>
                        <span class="text-gray-400">cliently.by</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 mb-4 md:mb-0">© 2024 Cliently.by. Все права защищены.</p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white text-sm">Оферта</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm">Конфиденциальность</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
