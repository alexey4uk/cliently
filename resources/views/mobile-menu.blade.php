<!-- Сэндвич-меню с Alpine.js (только мобильные) -->
<div x-data="{ open: false }" 
     @keydown.escape.window="open = false"
     class="lg:hidden">
    <!-- Кнопка открытия меню (в header) -->
    <button @click="open = true" 
            class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0"
            aria-label="Открыть меню">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <!-- Overlay -->
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
         style="display: none;">
    </div>

    <!-- Боковое меню -->
    <aside x-show="open"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           @click.away="open = false"
           class="fixed left-0 top-0 bottom-0 w-72 bg-white dark:bg-slate-900 shadow-xl z-50 overflow-y-auto"
           style="display: none;">
        <div class="flex flex-col h-full">
            <!-- Заголовок с кнопкой закрытия -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex-shrink-0">
                <a href="{{ route('dashboard') }}" @click="open = false" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <x-logo size="sm" />
                    <span class="text-lg font-bold text-slate-900 dark:text-white">CLIENTLY</span>
                </a>
                <button @click="open = false" 
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        aria-label="Закрыть меню">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <!-- Навигация -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                <!-- Основное -->
                <div>
                    <h3 class="px-3 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Основное
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" 
                           @click="open = false"
                           class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('dashboard') ?
                               'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                               'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-chart-line {{ Request::routeIs('dashboard') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                            </div>
                            Дашборд
                        </a>

                        <a href="{{ route('clients.index') }}" 
                           @click="open = false"
                           class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('clients.*') ?
                               'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                               'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-users {{ Request::routeIs('clients.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                            </div>
                            Клиенты
                        </a>

                        <a href="#" 
                           @click="open = false"
                           class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.*') ?
                               'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                               'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                            <div class="flex items-center justify-center w-6 mr-3 relative">
                                <i class="fas fa-calendar-check {{ Request::routeIs('appointments.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                                <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs font-semibold rounded-full h-4 w-4 flex items-center justify-center">5</span>
                            </div>
                            Записи
                            <span class="ml-auto bg-emerald-100 dark:bg-emerald-800 text-emerald-600 dark:text-emerald-300 text-xs font-semibold px-2 py-1 rounded-full min-w-[2rem] text-center">
                                5
                            </span>
                        </a>

                        <a href="{{ route('services.index') }}" 
                           @click="open = false"
                           class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('services.*') ?
                               'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                               'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-scissors {{ Request::routeIs('services.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                            </div>
                            Услуги
                        </a>
                    </div>
                </div>

                <!-- Аналитика -->
                <div>
                    <h3 class="px-3 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Аналитика
                    </h3>
                    <div class="space-y-1">
                        <a href="#" 
                           @click="open = false"
                           class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('finance.*') ?
                               'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                               'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-chart-line {{ Request::routeIs('finance.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                            </div>
                            Финансы
                        </a>

                        <a href="#" 
                           @click="open = false"
                           class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('reports.*') ?
                               'bg-gradient-to-r from-[#6366F1]/10 to-[#6366F1]/15 dark:from-[#6366F1]/20 dark:to-[#6366F1]/30 text-[#6366F1] dark:text-[#818CF8] shadow-sm border border-[#6366F1]/20 dark:border-[#6366F1]/40' :
                               'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:shadow-sm' }}">
                            <div class="flex items-center justify-center w-6 mr-3">
                                <i class="fas fa-chart-bar {{ Request::routeIs('reports.*') ? 'text-[#6366F1] dark:text-[#818CF8]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} text-sm"></i>
                            </div>
                            Отчеты
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Нижняя часть: Профиль -->
            <div class="border-t border-slate-200 dark:border-slate-800 p-4 flex-shrink-0">
                @auth
                    <!-- Информация о пользователе (кликабельная, ведет на профиль) -->
                    <a href="{{ route('profile.edit') }}" 
                       @click="open = false"
                       class="block mb-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-semibold text-sm overflow-hidden flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-full h-full object-cover rounded-full">
                                @else
                                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                            </div>
                        </div>
                    </a>
                @endauth

                <!-- Выход -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-xl transition-all duration-200">
                        <div class="flex items-center justify-center w-6 mr-3">
                            <i class="fas fa-sign-out-alt text-slate-400 text-sm"></i>
                        </div>
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
