<nav
    class="lg:hidden border-t border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95 fixed bottom-0 left-0 right-0 z-50">
    <div class="mx-auto max-w-6xl flex justify-around py-2.5 text-xs">
        <!-- Дашборд -->
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center gap-1.5 min-w-[44px] {{ Request::routeIs('dashboard') ? 'text-[#6366F1]' : 'text-slate-500 dark:text-slate-400' }} active:opacity-70 transition-all duration-200">
            <span
                class="h-8 w-8 rounded-lg {{ Request::routeIs('dashboard') ? 'bg-gradient-to-r from-[#6366F1]/15 to-[#818CF8]/15' : 'bg-slate-100 dark:bg-slate-800' }} flex items-center justify-center {{ Request::routeIs('dashboard') ? 'text-[#6366F1]' : 'text-slate-600 dark:text-slate-400' }} transition-all duration-200 {{ Request::routeIs('dashboard') ? 'scale-110' : 'hover:scale-105' }}">
                <i class="fa-solid fa-chart-line text-sm"></i>
            </span>
            <span class="font-medium text-[10px]">Дашборд</span>
        </a>

        <!-- Записи с badge -->
        <a href="#" 
           class="flex flex-col items-center gap-1.5 min-w-[44px] relative {{ Request::routeIs('appointments.*') ? 'text-[#6366F1]' : 'text-slate-500 dark:text-slate-400' }} active:opacity-70 transition-all duration-200">
            <span
                class="h-8 w-8 rounded-lg relative {{ Request::routeIs('appointments.*') ? 'bg-gradient-to-r from-[#6366F1]/15 to-[#818CF8]/15' : 'bg-slate-100 dark:bg-slate-800' }} flex items-center justify-center {{ Request::routeIs('appointments.*') ? 'text-[#6366F1]' : 'text-slate-600 dark:text-slate-400' }} transition-all duration-200 {{ Request::routeIs('appointments.*') ? 'scale-110' : 'hover:scale-105' }}">
                <i class="fa-solid fa-calendar-check text-sm"></i>
                <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-semibold rounded-full h-4 w-4 flex items-center justify-center shadow-sm">5</span>
            </span>
            <span class="font-medium text-[10px]">Записи</span>
        </a>

        <!-- Клиенты -->
        <a href="{{ route('clients.index') }}" 
           class="flex flex-col items-center gap-1.5 min-w-[44px] {{ Request::routeIs('clients.*') ? 'text-[#6366F1]' : 'text-slate-500 dark:text-slate-400' }} active:opacity-70 transition-all duration-200">
            <span
                class="h-8 w-8 rounded-lg {{ Request::routeIs('clients.*') ? 'bg-gradient-to-r from-[#6366F1]/15 to-[#818CF8]/15' : 'bg-slate-100 dark:bg-slate-800' }} flex items-center justify-center {{ Request::routeIs('clients.*') ? 'text-[#6366F1]' : 'text-slate-600 dark:text-slate-400' }} transition-all duration-200 {{ Request::routeIs('clients.*') ? 'scale-110' : 'hover:scale-105' }}">
                <i class="fa-solid fa-users text-sm"></i>
            </span>
            <span class="font-medium text-[10px]">Клиенты</span>
        </a>

        <!-- Услуги -->
        <a href="{{ route('services.index') }}" 
           class="flex flex-col items-center gap-1.5 min-w-[44px] {{ Request::routeIs('services.*') ? 'text-[#6366F1]' : 'text-slate-500 dark:text-slate-400' }} active:opacity-70 transition-all duration-200">
            <span
                class="h-8 w-8 rounded-lg {{ Request::routeIs('services.*') ? 'bg-gradient-to-r from-[#6366F1]/15 to-[#818CF8]/15' : 'bg-slate-100 dark:bg-slate-800' }} flex items-center justify-center {{ Request::routeIs('services.*') ? 'text-[#6366F1]' : 'text-slate-600 dark:text-slate-400' }} transition-all duration-200 {{ Request::routeIs('services.*') ? 'scale-110' : 'hover:scale-105' }}">
                <i class="fa-solid fa-scissors text-sm"></i>
            </span>
            <span class="font-medium text-[10px]">Услуги</span>
        </a>

        <!-- Профиль с dropdown -->
        <div class="relative">
            <button
                class="menu-trigger flex flex-col items-center gap-1.5 min-w-[44px] text-slate-500 dark:text-slate-400 active:opacity-70 transition-all duration-200 hover:text-slate-700 dark:hover:text-slate-300"
                aria-label="Профиль">
                <span
                    class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 transition-all duration-200 hover:scale-105">
                    <i class="fa-solid fa-user text-sm"></i>
                </span>
                <span class="font-medium text-[10px]">Профиль</span>
            </button>
            <div
                class="menu-panel z-[100] hidden w-56 rounded-md border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900 shadow-lg">
                <!-- Информация о пользователе -->
                @auth
                    <div class="px-3 py-2.5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-xs font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-slate-700 overflow-hidden flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endauth
                
                <!-- Действия -->
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-user w-4 text-indigo-500 dark:text-indigo-400 text-xs"></i>
                        <span>Профиль</span>
                    </a>
                    <a href="#"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-cog w-4 text-indigo-500 dark:text-indigo-400 text-xs"></i>
                        <span>Настройки</span>
                    </a>
                    <a href="#"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-chart-line w-4 text-indigo-500 dark:text-indigo-400 text-xs"></i>
                        <span>Финансы</span>
                    </a>
                    <a href="#"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-file-invoice w-4 text-indigo-500 dark:text-indigo-400 text-xs"></i>
                        <span>Отчеты</span>
                    </a>
                    <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-left text-slate-700 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-4 text-indigo-500 dark:text-indigo-400 text-xs"></i>
                            <span>Выйти</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>