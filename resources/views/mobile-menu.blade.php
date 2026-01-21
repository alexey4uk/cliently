<!-- Сэндвич-меню с Alpine.js (только мобильные) -->
<div x-data="{
    open: false,
    managementOpen: {{ Request::routeIs('settings.*') || Request::routeIs('services.*') ? 'true' : 'false' }},
    analyticsOpen: {{ Request::routeIs('finance.*') || Request::routeIs('reports.*') ? 'true' : 'false' }}
}" @keydown.escape.window="open = false" class="lg:hidden">
    <!-- Кнопка открытия меню (в header) -->
    <button @click="open = true"
        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 flex-shrink-0 group"
        aria-label="Открыть меню">
        <i class="fa-solid fa-bars text-base group-hover:scale-110 transition-transform duration-200"></i>
    </button>

    <!-- Overlay -->
    <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="open = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
        style="display: none;">
    </div>

    <!-- Боковое меню -->
    <aside x-show="open" x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full" @click.away="open = false"
        class="fixed left-0 top-0 bottom-0 w-72 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm shadow-xl z-50 overflow-y-auto border-r border-slate-200/50 dark:border-slate-800/50"
        style="display: none;">
        <div class="flex flex-col h-full">
            <!-- Заголовок с кнопкой закрытия -->
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-slate-200/50 dark:border-slate-800/50 flex-shrink-0">
                <a href="{{ route('dashboard') }}" @click="open = false"
                    class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <x-logo size="sm" />
                    <span
                        class="text-xl font-bold text-slate-900 dark:text-white tracking-tight uppercase font-display">CLIENTLY</span>
                </a>
                <button @click="open = false"
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 group"
                    aria-label="Закрыть меню">
                    <i class="fa-solid fa-times text-base group-hover:scale-110 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Навигация -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                <!-- Основное -->
                <div>
                    <h3
                        class="px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Основное
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('dashboard')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Главная</span>
                        </a>

                        <a href="{{ route('clients.index') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('clients.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-users text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Клиенты</span>
                        </a>

                        <a href="{{ route('appointments.index') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-calendar-check text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Записи</span>
                        </a>
                    </div>
                </div>

                <!-- Управление -->
                <div>
                    <button @click="managementOpen = !managementOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <span>Управление</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': managementOpen }"></i>
                    </button>
                    <div x-show="managementOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        <a href="{{ route('settings.index') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.index')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-building text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Бизнес</span>
                        </a>

                        <a href="{{ route('settings.locations') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.locations*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Локации</span>
                        </a>

                        <a href="{{ route('services.index') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('services.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-scissors text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Услуги</span>
                        </a>

                        <a href="{{ route('settings.masters') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.masters*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-user-tie text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Мастера</span>
                        </a>

                        <a href="{{ route('settings.online-booking') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.online-booking*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-link text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Онлайн запись</span>
                        </a>

                        <a href="{{ route('settings.telegram') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.masters*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-brands fa-telegram text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Telegram Bot</span>
                        </a>
                    </div>
                </div>

                <!-- Аналитика -->
                <div>
                    <button @click="analyticsOpen = !analyticsOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <span>Аналитика</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': analyticsOpen }"></i>
                    </button>
                    <div x-show="analyticsOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        <a href="#" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('finance.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Финансы</span>
                        </a>

                        <a href="#" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('reports.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-chart-bar text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Отчеты</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Нижняя часть: Профиль -->
            <div class="border-t border-slate-200/50 dark:border-slate-800/50 pt-4 mt-4 flex-shrink-0">
                <div class="px-3">
                    @auth
                        <!-- Информация о пользователе (кликабельная, ведет на профиль) -->
                        <a href="{{ route('profile.edit') }}" @click="open = false"
                            class="block mb-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm overflow-hidden flex-shrink-0">
                                    @if (Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                            alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
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

                    <!-- Админка (только для админов) -->
                    @if(!Str::startsWith(Request::path(), 'panel') && Auth::user()->hasRole('admin'))
                        <a href="{{ route('panel.index') }}" @click="open = false"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 bg-amber-500 hover:bg-amber-600 text-white">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-shield-halved text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Админка</span>
                        </a>
                        <div class="border-t border-slate-200/50 dark:border-slate-800/50 my-2"></div>
                    @endif

                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-3 py-3 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-xl transition-all duration-200">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-right-from-bracket text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Выйти</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</div>
