<div class="lg:hidden">
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Кнопка меню для мобильных -->
                    <button id="mobile-menu-button" class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors duration-200">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <!-- Логотип -->
                    <div class="flex-shrink-0 flex items-center ml-3">
                        <div class="flex items-center space-x-2">
                            <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">CLIENTLY</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <!-- Переключатель темы -->
                    <button id="theme-toggle" class="flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors duration-200">
                        <svg id="theme-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-dark-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Уведомления с бейджем -->
                    <button class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <!-- Бейдж уведомлений -->
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-[12px] rounded-full h-4 w-4 flex items-center justify-center font-medium">3</span>
                    </button>

                    <!-- Профиль -->
                    <div class="relative flex-shrink-0">
                        <button id="user-menu-button" class="flex text-sm rounded-full focus:outline-none">
                            <span class="sr-only">Открыть меню пользователя</span>
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Аватар" class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                            @else
                                <div class="w-8 h-8 rounded-full border-white dark:border-gray-800 shadow-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium">
                                    <span class="font-bold text-white">
                                        {{ Str::substr(auth()->user()->first_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </button>

                        <!-- Выпадающее меню профиля -->
                        <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:outline-none z-10">
                            <!-- Заголовок профиля -->
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>

                            <div class="py-2" role="none">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-user w-4 mr-3 text-center"></i>
                                    Ваш профиль
                                </a>
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-cog w-4 mr-3 text-center"></i>
                                    Настройки
                                </a>
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-shield-alt w-4 mr-3 text-center"></i>
                                    Безопасность
                                </a>

                                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt w-4 mr-3 text-center"></i>
                                        Выйти из аккаунта
                                    </button>
                                </form>
                            </div>

                            <!-- Футер меню -->
                            <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Тариф: <span class="font-medium text-gray-700 dark:text-gray-300">Бесплатный</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
        <div class="pt-2 pb-4">
            <nav class="space-y-1 px-3">
                <!-- Панель управления -->
                <a href="{{ route('dashboard') }}" class="{{ Request::routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200">
                    <i class="{{ Request::routeIs('dashboard') ? 'fas fa-tachometer-alt text-blue-600' : 'fas fa-tachometer-alt text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                    Панель управления
                </a>

                <!-- Клиенты -->
                <a href="" class="{{ Request::routeIs('clients.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200">
                    <i class="{{ Request::routeIs('clients.*') ? 'fas fa-users text-blue-600' : 'fas fa-users text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                    Клиенты
                    <span class="ml-auto bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-300 text-xs font-medium px-2 py-0.5 rounded-full">
                        24
                    </span>
                </a>

                <!-- Записи -->
                <a href="" class="{{ Request::routeIs('appointments.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200">
                    <i class="{{ Request::routeIs('appointments.*') ? 'fas fa-calendar-check text-blue-600' : 'fas fa-calendar-check text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                    Записи
                    <span class="ml-auto bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 text-xs font-medium px-2 py-0.5 rounded-full">
                        5
                    </span>
                </a>

                <!-- Финансы -->
                <a href="" class="{{ Request::routeIs('finance.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200">
                    <i class="{{ Request::routeIs('finance.*') ? 'fas fa-chart-line text-blue-600' : 'fas fa-chart-line text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                    Финансы
                </a>

                <!-- Отчеты -->
                <a href="" class="{{ Request::routeIs('reports.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200">
                    <i class="{{ Request::routeIs('reports.*') ? 'fas fa-chart-bar text-blue-600' : 'fas fa-chart-bar text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                    Отчеты
                </a>

                <!-- Настройки -->
                <a href="" class="{{ Request::routeIs('settings.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200">
                    <i class="{{ Request::routeIs('settings.*') ? 'fas fa-cog text-blue-600' : 'fas fa-cog text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                    Настройки
                </a>
            </nav>

            <!-- Информация о тарифе в мобильном меню -->
            <div class="mt-4 px-3">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-3 text-white">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium">Бесплатный тариф</span>
                        <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded">30/30</span>
                    </div>
                    <div class="w-full bg-white/30 rounded-full h-1.5 mb-2">
                        <div class="bg-white h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                    <button class="w-full text-xs font-medium bg-white text-blue-600 py-1.5 rounded hover:bg-blue-50 transition-colors duration-200">
                        Обновить тариф
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
