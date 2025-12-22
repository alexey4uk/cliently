<div class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white dark:bg-slate-900 pt-6 pb-4 overflow-y-auto border-r border-slate-200 dark:border-slate-800 transition-all duration-300">
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
                            <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('dashboard') ?
                                'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                                <div class="flex items-center justify-center w-6 mr-3">
                                    <i class="fas fa-chart-line {{ Request::routeIs('dashboard') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                </div>
                                Главная
                            </a>

                            <!-- Клиенты -->
                            <a href="{{ route('clients.index') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('clients.*') ?
                                'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                                <div class="flex items-center justify-center w-6 mr-3">
                                    <i class="fas fa-users {{ Request::routeIs('clients.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                </div>
                                Клиенты
                            </a>

                            <!-- Записи -->
                            <a href="#" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.*') ?
                                'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                                <div class="flex items-center justify-center w-6 mr-3">
                                    <i class="fas fa-calendar-check {{ Request::routeIs('appointments.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                </div>
                                Записи
                                <span class="ml-auto bg-emerald-100 dark:bg-emerald-800 text-emerald-600 dark:text-emerald-300 text-xs font-semibold px-2 py-1 rounded-full min-w-[2rem] text-center">
                                    5
                                </span>
                            </a>

                            <!-- Услуги -->
                            <a href="{{ route('services.index') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('services.*') ?
                                'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                                <div class="flex items-center justify-center w-6 mr-3">
                                    <i class="fas fa-scissors {{ Request::routeIs('services.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                </div>
                                Услуги
                            </a>
                        </nav>
                    </div>

                    <!-- Аналитика -->
                    <div>
                        <h3 class="px-3 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Аналитика
                        </h3>
                        <nav class="space-y-1">
                            <!-- Финансы -->
                            <a href="#" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('finance.*') ?
                                'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                                <div class="flex items-center justify-center w-6 mr-3">
                                    <i class="fas fa-chart-line {{ Request::routeIs('finance.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                </div>
                                Финансы
                            </a>

                            <!-- Отчеты -->
                            <a href="#" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('reports.*') ?
                                'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                                <div class="flex items-center justify-center w-6 mr-3">
                                    <i class="fas fa-chart-bar {{ Request::routeIs('reports.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                </div>
                                Отчеты
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Нижняя часть сайдбара -->
            <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                <!-- Профиль пользователя -->
                <div class="px-3 space-y-2">
                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-xl transition-all duration-200 group">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-sign-out-alt text-slate-400 group-hover:text-red-500 text-sm"></i>
                            </div>
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
