<div class="hidden lg:flex lg:flex-shrink-0" 
     x-data="{ 
         managementOpen: {{ Request::routeIs('settings.*') || Request::routeIs('services.*') ? 'true' : 'false' }},
         analyticsOpen: {{ Request::routeIs('finance.*') || Request::routeIs('reports.*') ? 'true' : 'false' }}
     }">
    <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white dark:bg-slate-900 pt-6 pb-4 overflow-y-auto border-r border-slate-200 dark:border-slate-800">
            <!-- Логотип -->
            <div class="flex items-center flex-shrink-0 px-6 mb-8">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group cursor-pointer hover:opacity-80 transition-opacity">
                    <x-logo size="sidebar" />
                    <div>
                        <span class="text-xl font-bold text-slate-900 dark:text-white block tracking-tight uppercase font-display">CLIENTLY</span>
                    </div>
                </a>
            </div>

            <!-- Основная навигация -->
            <div class="flex-grow flex flex-col">
                <div class="flex-1 px-3 space-y-6">
                    <!-- Основное -->
                    <div>
                        <h3 class="px-3 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Основное
                        </h3>
                        <nav class="space-y-1">
                            <!-- Панель управления -->
                            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('dashboard') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-chart-line w-5 text-center mr-3"></i>
                                Главная
                            </a>

                            <!-- Клиенты -->
                            <a href="{{ route('clients.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('clients.*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-users w-5 text-center mr-3"></i>
                                Клиенты
                            </a>

                            <!-- Записи -->
                            <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('appointments.*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-calendar-check w-5 text-center mr-3"></i>
                                Записи
                                <span class="ml-auto bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    5
                                </span>
                            </a>
                        </nav>
                    </div>

                    <!-- Управление -->
                    <div>
                        <button @click="managementOpen = !managementOpen" 
                                class="w-full flex items-center justify-between px-3 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                            <span>Управление</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" 
                               :class="{ 'rotate-180': managementOpen }"></i>
                        </button>
                        <nav x-show="managementOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="space-y-1 overflow-hidden">
                            <!-- Настройки бизнеса -->
                            <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('settings.index') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-building w-5 text-center mr-3"></i>
                                Бизнес
                            </a>

                            <!-- Локации -->
                            <a href="{{ route('settings.locations') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('settings.locations*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-location-dot w-5 text-center mr-3"></i>
                                Локации
                            </a>

                            <!-- Услуги -->
                            <a href="{{ route('services.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('services.*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-scissors w-5 text-center mr-3"></i>
                                Услуги
                            </a>

                            <!-- Мастера -->
                            <a href="{{ route('settings.masters') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('settings.masters*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-user-tie w-5 text-center mr-3"></i>
                                Мастера
                            </a>
                        </nav>
                    </div>

                    <!-- Аналитика -->
                    <div>
                        <button @click="analyticsOpen = !analyticsOpen" 
                                class="w-full flex items-center justify-between px-3 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                            <span>Аналитика</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" 
                               :class="{ 'rotate-180': analyticsOpen }"></i>
                        </button>
                        <nav x-show="analyticsOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="space-y-1 overflow-hidden">
                            <!-- Финансы -->
                            <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('finance.*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-chart-line w-5 text-center mr-3"></i>
                                Финансы
                            </a>

                            <!-- Отчеты -->
                            <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('reports.*') ?
                                'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' :
                                'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <i class="fa-solid fa-chart-bar w-5 text-center mr-3"></i>
                                Отчеты
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Нижняя часть сайдбара -->
            <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                <div class="px-3 space-y-2">
                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center mr-3"></i>
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
