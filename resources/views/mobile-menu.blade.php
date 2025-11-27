<div class="lg:hidden">
    <div class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Кнопка меню для мобильных -->
                    <button id="mobile-menu-button" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex-shrink-0 flex items-center ml-4">
                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name', 'Мастерская CRM') }}</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <!-- Переключатель темы -->
                    <button id="theme-toggle" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                        <svg id="theme-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-dark-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Уведомления -->
                    <button class="ml-4 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>

                    <!-- Профиль -->
                    <div class="ml-4 relative flex-shrink-0">
                        <button id="user-menu-button" class="flex text-sm rounded-full focus:outline-none">
                            <span class="sr-only">Открыть меню пользователя</span>
                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium">
                                {{ Str::substr(auth()->user()->first_name, 0, 1) }}
                            </div>
                        </button>

                        <!-- Выпадающее меню профиля -->
                        <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                            <div class="py-1" role="none">
                                <a href="" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Ваш профиль</a>
                                <a href="" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Настройки</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Выйти</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white dark:bg-gray-800 shadow-lg">
        <div class="pt-2 pb-3 space-y-1">
            <a href="" class="{{ Request::routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Панель управления</a>
            <a href="" class="{{ Request::routeIs('clients.*') ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Клиенты</a>
            <a href="" class="{{ Request::routeIs('appointments.*') ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Записи</a>
            <a href="" class="{{ Request::routeIs('finance.*') ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Финансы</a>
            <a href="" class="{{ Request::routeIs('reports.*') ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Отчеты</a>
            <a href="" class="{{ Request::routeIs('settings.*') ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Настройки</a>
        </div>
    </div>
</div>
