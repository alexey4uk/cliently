<div class="hidden lg:block">
    <div class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex"></div>
                <div class="flex items-center space-x-1">
                    <!-- Переключатель темы -->
                    <button id="theme-toggle-desktop" class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors duration-200">
                        <svg id="theme-light-icon-desktop" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-dark-icon-desktop" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Уведомления -->
                    <button class="ml-4 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <!-- Бейдж уведомлений -->
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-[12px] rounded-full h-4 w-4 flex items-center justify-center font-medium">3</span>
                    </button>

                    <!-- Профиль -->
                    <div class="relative flex-shrink-0">
                        <button id="user-menu-button-desktop" class="flex ml-1 items-center space-x-1 px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors duration-200">
                            <span class="sr-only">Открыть меню пользователя</span>
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                                {{ Str::substr(auth()->user()->first_name, 0, 1) }}
                            </div>
                            <div class="hidden xl:block text-left">
                                <p class="text-sm font-medium text-gray-900 dark:text-white leading-none">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-none mt-0.5">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs hidden xl:block"></i>
                        </button>

                        <!-- Выпадающее меню профиля -->
                        <div id="user-menu-desktop" class="hidden origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                            <!-- Заголовок профиля -->
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>

                            <div class="py-1" role="none">
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                    <i class="fas fa-user w-4 mr-3 text-center"></i>
                                    Ваш профиль
                                </a>
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                    <i class="fas fa-cog w-4 mr-3 text-center"></i>
                                    Настройки
                                </a>
                                <a href="" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                    <i class="fas fa-shield-alt w-4 mr-3 text-center"></i>
                                    Безопасность
                                </a>

                                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20" role="menuitem">
                                        <i class="fas fa-sign-out-alt w-4 mr-3 text-center"></i>
                                        Выйти
                                    </button>
                                </form>
                            </div>

                            <!-- Футер меню -->
                            <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
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
</div>
