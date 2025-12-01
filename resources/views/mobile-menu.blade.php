<div class="lg:hidden">
    <!-- Мобильный хедер -->
    <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200/50 dark:border-gray-700/50 shadow-sm sticky top-0 z-40">
        <div class="px-4 sm:px-6">
            <div class="flex justify-between items-center h-16">
                <!-- Левая часть - меню и лого -->
                <div class="flex items-center space-x-3">
                    <!-- Кнопка меню -->
                    <button id="mobile-menu-button" class="flex items-center justify-center w-10 h-10 rounded-xl text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 active:scale-95">
                        <i class="fas fa-bars text-base"></i>
                    </button>

                    <!-- Логотип -->
                    <div class="flex items-center space-x-2">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">CLIENTLY</span>
                    </div>
                </div>

                <!-- Правая часть - действия -->
                <div class="flex items-center space-x-1">

                    <!-- Переключатель темы -->
                    <button id="theme-toggle" class="flex items-center justify-center w-10 h-10 rounded-xl text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200">
                        <svg id="theme-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-dark-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Уведомления -->
                    <button class="relative flex items-center justify-center w-10 h-10 rounded-xl text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200">
                        <i class="fas fa-bell text-base"></i>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium border-2 border-white dark:border-gray-900 shadow-sm">
                            3
                        </span>
                    </button>

                    <!-- Профиль -->
                    <div class="relative">
                        <button id="user-menu-button" class="flex text-sm rounded-full focus:outline-none transition-transform duration-200 active:scale-95">
                            <span class="sr-only">Открыть меню пользователя</span>
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Аватар" class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 shadow-md">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium border-2 border-white dark:border-gray-800 shadow-md">
                                    <span class="text-sm font-bold">
                                        {{ Str::substr(auth()->user()->first_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </button>

                        <!-- Выпадающее меню профиля -->
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-64 rounded-2xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 backdrop-blur-lg z-50">
                            <!-- Заголовок профиля -->
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-t-2xl">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>

                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>
                                    Мой профиль
                                </a>
                                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                    Настройки
                                </a>
                                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <i class="fas fa-credit-card w-5 mr-3 text-gray-400"></i>
                                    Тарифы и оплата
                                </a>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 py-2">
                                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <i class="fas fa-question-circle w-5 mr-3 text-gray-400"></i>
                                    Помощь и поддержка
                                </a>
                                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <i class="fas fa-shield-alt w-5 mr-3 text-gray-400"></i>
                                    Безопасность
                                </a>
                            </div>

                            <!-- Футер меню -->
                            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Тариф:</span>
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">Бесплатный</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: 80%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">24 из 30 клиентов</p>
                            </div>

                            <!-- Выход -->
                            <div class="border-t border-gray-200 dark:border-gray-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 rounded-b-2xl">
                                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                        Выйти из аккаунта
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="pt-2 pb-4">
            <!-- Основная навигация -->
            <nav class="space-y-1 px-3 mb-4">
                <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('dashboard') ?
                    'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                    'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 mr-3">
                        <i class="fas fa-tachometer-alt {{ Request::routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                    </div>
                    Панель управления
                </a>

                <a href="" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('clients.*') ?
                    'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                    'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 mr-3">
                        <i class="fas fa-users {{ Request::routeIs('clients.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                    </div>
                    Клиенты
                    <span class="ml-auto bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-300 text-xs font-semibold px-2 py-1 rounded-full min-w-[2rem] text-center">
                        24
                    </span>
                </a>

                <a href="" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.*') ?
                    'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                    'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 mr-3">
                        <i class="fas fa-calendar-check {{ Request::routeIs('appointments.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                    </div>
                    Записи
                    <span class="ml-auto bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 text-xs font-semibold px-2 py-1 rounded-full min-w-[2rem] text-center">
                        5
                    </span>
                </a>

                <a href="" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('services.*') ?
                    'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                    'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 mr-3">
                        <i class="fas fa-concierge-bell {{ Request::routeIs('services.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                    </div>
                    Услуги
                </a>

                <a href="" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('finance.*') ?
                    'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                    'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 mr-3">
                        <i class="fas fa-chart-line {{ Request::routeIs('finance.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                    </div>
                    Финансы
                </a>

                <a href="" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('reports.*') ?
                    'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                    'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 mr-3">
                        <i class="fas fa-chart-bar {{ Request::routeIs('reports.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                    </div>
                    Отчеты
                </a>
            </nav>

            <!-- Раздел настроек -->
            <div class="px-3 mb-4">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-3">Настройки</p>
                <nav class="space-y-1">
                    <a href="" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.*') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-cog {{ Request::routeIs('settings.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Настройки
                    </a>
                </nav>
            </div>

            <!-- Информация о тарифе -->
            <div class="px-3">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold">Бесплатный тариф</span>
                        <span class="text-xs bg-white/20 px-2 py-1 rounded-full font-medium">24/30</span>
                    </div>
                    <div class="w-full bg-white/30 rounded-full h-2 mb-3">
                        <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: 80%"></div>
                    </div>
                    <button class="w-full text-xs font-semibold bg-white text-blue-600 py-2.5 rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-sm active:scale-95">
                        Обновить тариф
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
