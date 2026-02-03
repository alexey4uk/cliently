<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 border-b border-gray-200 dark:border-gray-800 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-3">
                <x-logo size="md" />
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-gray-900 dark:text-white uppercase font-display">CLIENTLY</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline pl-2 border-l border-gray-300 dark:border-gray-600">Запись и CRM</span>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-1">
                <a href="#features" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Возможности</a>
                <a href="#how-it-works" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Как это работает</a>
                <a href="#pricing" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Тарифы</a>
                <a href="#faq" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">FAQ</a>
            </nav>

            <div class="flex items-center gap-2">
                <x-theme-toggle />

                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <x-icon name="squares-2x2" variant="outline" size="sm" />
                        Панель управления
                    </a>
                    <a href="{{ route('dashboard') }}" class="sm:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Панель управления">
                        <x-icon name="squares-2x2" variant="outline" size="md" />
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Войти</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Начать бесплатно</a>
                    <a href="{{ route('login') }}" class="sm:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Войти">
                        <x-icon name="arrow-right-on-rectangle" variant="outline" size="md" />
                    </a>
                @endauth

                <button id="landing-mobile-menu-button" type="button" class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Меню">
                    <x-icon name="bars-3" variant="outline" size="md" />
                </button>
            </div>
        </div>
    </div>
</header>

<div id="landing-mobile-overlay" class="landing-mobile-overlay fixed inset-0 bg-black/40 z-40 lg:hidden" aria-hidden="true"></div>
<aside id="landing-mobile-menu" class="landing-mobile-menu fixed top-0 left-0 w-full sm:w-80 h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 z-50 lg:hidden">
    <div class="p-4 sm:p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <x-logo size="md" />
                <span class="text-lg font-bold text-gray-900 dark:text-white uppercase font-display">CLIENTLY</span>
            </div>
            <button id="landing-mobile-menu-close" type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Закрыть">
                <x-icon name="x-mark" variant="outline" size="md" />
            </button>
        </div>

        <nav class="space-y-1 mb-6">
            <a href="#features" class="landing-mobile-nav-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">Возможности</a>
            <a href="#how-it-works" class="landing-mobile-nav-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">Как это работает</a>
            <a href="#pricing" class="landing-mobile-nav-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">Тарифы</a>
            <a href="#faq" class="landing-mobile-nav-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">FAQ</a>
        </nav>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            @auth
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-semibold text-sm flex-shrink-0">
                            @if(Auth::user()->getAvatarUrl())
                                <img src="{{ Auth::user()->getAvatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-full h-full rounded-lg object-cover" referrerpolicy="no-referrer">
                            @else
                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg mb-3 transition-colors">
                    <x-icon name="squares-2x2" variant="outline" size="sm" />
                    Панель управления
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mb-3 transition-colors">
                    <x-icon name="user" variant="outline" size="sm" />
                    Профиль
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <x-icon name="arrow-right-on-rectangle" variant="outline" size="sm" />
                        Выйти
                    </button>
                </form>
            @else
                <div class="space-y-3">
                    <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                        <x-icon name="user-plus" variant="outline" size="sm" />
                        Начать бесплатно
                    </a>
                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="arrow-right-on-rectangle" variant="outline" size="sm" />
                        Войти в аккаунт
                    </a>
                </div>
            @endauth
        </div>
    </div>
</aside>
