<div class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white dark:bg-gray-800 pt-5 pb-4 overflow-y-auto border-r border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <!-- Логотип -->
            <div class="flex items-center flex-shrink-0 px-4 mb-8">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 text-white p-2 rounded-lg">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white block">CLIENTLY</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">CRM для мастеров</span>
                    </div>
                </div>
            </div>

            <!-- Навигация -->
            <div class="flex-grow flex flex-col">
                <nav class="flex-1 px-3 space-y-1">
                    <!-- Панель управления -->
                    <a href="{{ route('dashboard') }}" class="{{ Request::routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="{{ Request::routeIs('dashboard') ? 'fas fa-tachometer-alt text-blue-600' : 'fas fa-tachometer-alt text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                        Панель управления
                    </a>

                    <!-- Клиенты -->
                    <a href="" class="{{ Request::routeIs('clients.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="{{ Request::routeIs('clients.*') ? 'fas fa-users text-blue-600' : 'fas fa-users text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                        Клиенты
                        <span class="ml-auto bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-300 text-xs font-medium px-2 py-0.5 rounded-full">
                            24
                        </span>
                    </a>

                    <!-- Записи -->
                    <a href="" class="{{ Request::routeIs('appointments.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="{{ Request::routeIs('appointments.*') ? 'fas fa-calendar-check text-blue-600' : 'fas fa-calendar-check text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                        Записи
                        <span class="ml-auto bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 text-xs font-medium px-2 py-0.5 rounded-full">
                            5
                        </span>
                    </a>

                    <!-- Финансы -->
                    <a href="" class="{{ Request::routeIs('finance.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="{{ Request::routeIs('finance.*') ? 'fas fa-chart-line text-blue-600' : 'fas fa-chart-line text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                        Финансы
                    </a>

                    <!-- Отчеты -->
                    <a href="" class="{{ Request::routeIs('reports.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="{{ Request::routeIs('reports.*') ? 'fas fa-chart-bar text-blue-600' : 'fas fa-chart-bar text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                        Отчеты
                    </a>

                    <!-- Настройки -->
                    <a href="" class="{{ Request::routeIs('settings.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 border-r-2 border-blue-600' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="{{ Request::routeIs('settings.*') ? 'fas fa-cog text-blue-600' : 'fas fa-cog text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} w-5 text-center mr-3"></i>
                        Настройки
                    </a>
                </nav>
            </div>

            <!-- Нижняя часть сайдбара -->
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 pt-4">
                <!-- Профиль пользователя -->
                <div class="px-3 space-y-3">
                    <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                            </div>
                        </div>
                        <div class="ml-3 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-sign-out-alt w-5 text-center mr-3 text-gray-400"></i>
                            Выйти
                        </button>
                    </form>
                </div>

                <!-- Информация о тарифе -->
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
</div>
