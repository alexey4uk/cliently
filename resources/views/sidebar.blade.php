<div class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white dark:bg-gray-800 pt-6 pb-4 overflow-y-auto border-r border-gray-200 dark:border-gray-700 transition-all duration-300">
            <!-- Логотип -->
            <div class="flex items-center flex-shrink-0 px-6 mb-8">
                <div class="flex items-center space-x-3 group cursor-pointer">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-2 rounded-xl shadow-lg transition-transform duration-200 group-hover:scale-105">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white block tracking-tight">CLIENTLY</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mt-0.5">Управление клиентами</span>
                    </div>
                </div>
            </div>

            <!-- Основная навигация -->
            <div class="flex-grow flex flex-col">
                <nav class="flex-1 px-3 space-y-1">
                    <!-- Панель управления -->
                    <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('dashboard') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white hover:shadow-sm' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-tachometer-alt {{ Request::routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Панель управления
                    </a>

                    <!-- Клиенты -->
                    <a href="" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('clients.*') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white hover:shadow-sm' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-users {{ Request::routeIs('clients.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Клиенты
                        <span class="ml-auto bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-300 text-xs font-semibold px-2 py-1 rounded-full min-w-[2rem] text-center">
                            24
                        </span>
                    </a>

                    <!-- Записи -->
                    <a href="" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.*') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white hover:shadow-sm' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-calendar-check {{ Request::routeIs('appointments.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Записи
                        <span class="ml-auto bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 text-xs font-semibold px-2 py-1 rounded-full min-w-[2rem] text-center">
                            5
                        </span>
                    </a>

                    <!-- Услуги -->
                    <a href="" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('services.*') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white hover:shadow-sm' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-concierge-bell {{ Request::routeIs('services.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Услуги
                    </a>

                    <!-- Финансы -->
                    <a href="" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('finance.*') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white hover:shadow-sm' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-chart-line {{ Request::routeIs('finance.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Финансы
                    </a>

                    <!-- Отчеты -->
                    <a href="" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('reports.*') ?
                        'bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-200 shadow-sm border border-blue-100 dark:border-blue-800' :
                        'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white hover:shadow-sm' }}">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-chart-bar {{ Request::routeIs('reports.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }} text-sm"></i>
                        </div>
                        Отчеты
                    </a>
                </nav>
            </div>

            <!-- Нижняя часть сайдбара -->
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                <!-- Профиль пользователя -->
                <div class="px-3 space-y-2">
                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-xl transition-all duration-200 group">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-red-500 text-sm"></i>
                            </div>
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
