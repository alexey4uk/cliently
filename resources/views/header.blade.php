<div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between gap-4">
    <!-- Бренд -->
    <div class="flex items-center gap-2">
        <x-logo size="sm" />


        <span class="text-sm font-semibold text-slate-950 dark:text-white tracking-tight uppercase font-display">
            CLIENTLY
        </span>
        <span
            class="hidden sm:inline-block text-[11px] text-slate-500 dark:text-slate-400 pl-2 border-l border-slate-200 dark:border-slate-700">
            {{ Str::startsWith(Request::path(), 'panel') ? 'Панель управления' : 'онлайн‑записи и клиенты' }}
        </span>
        @if(Str::startsWith(Request::path(), 'panel'))
            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 rounded-full ml-2">
                <i class="fa-solid fa-shield-halved"></i>
                Админ
            </span>
        @endif
    </div>


    <!-- Меню (десктоп) -->
    <nav class="hidden md:flex items-center gap-1 text-sm">
        @if(Str::startsWith(Request::path(), 'panel'))
            @can('analytics.view')
                <a href="{{ route('panel.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-900 dark:text-white font-medium {{ request()->routeIs('panel.index') ?  'bg-[#6366F1]/10 dark:bg-[#6366F1]/20' : ''}} transition-colors">
                    <i class="fa-solid fa-chart-line text-xs"></i>
                    <span>Главная</span>
                </a>
            @endcan
            @can('appointments.view')
                <a href="{{ route('panel.appointments') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors {{ request()->routeIs('panel.appointments') ?  'bg-[#6366F1]/10 dark:bg-[#6366F1]/20' : ''}}">
                    <i class="fa-solid fa-calendar text-xs"></i>
                    <span>Записи</span>
                </a>
            @endcan
            @can('clients.view')
                <a href="{{ route('panel.clients') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors {{ request()->routeIs('panel.clients') ?  'bg-[#6366F1]/10 dark:bg-[#6366F1]/20' : ''}}">
                    <i class="fa-solid fa-users text-xs"></i>
                    <span>Клиенты</span>
                </a>
            @endcan
        @else
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-900 dark:text-white font-medium {{ request()->routeIs('dashboard') ?  'bg-[#6366F1]/10 dark:bg-[#6366F1]/20' : ''}} transition-colors">
                <i class="fa-solid fa-chart-line text-xs"></i>
                <span>Главная</span>
            </a>
            <a href="#"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-calendar text-xs"></i>
                <span>Записи</span>
            </a>
            <a href="#"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-users text-xs"></i>
                <span>Клиенты</span>
            </a>
            <!-- Дополнительные пункты меню -->
            <div class="relative">
                <button
                    class="menu-trigger inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors"
                    aria-label="Дополнительные разделы">
                    <i class="fa-solid fa-ellipsis text-xs"></i>
                    <span>Еще</span>
                </button>
                <div
                    class="menu-panel z-[100] hidden w-48 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                    <a href="#"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                        <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-scissors"></i></span>
                        <span>Услуги</span>
                    </a>
                    <a href="#"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                        <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-chart-bar"></i></span>
                        <span>Аналитика</span>
                    </a>
                    <a href="#"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                        <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                class="fa-solid fa-file-invoice"></i></span>
                        <span>Отчеты</span>
                    </a>
                    <a href="#"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                        <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-bell"></i></span>
                        <span>Уведомления</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>


    <!-- Действия -->
    <div class="flex items-center gap-2">
        <!-- Мобильная версия кнопки -->
        <button
            class="sm:hidden inline-flex items-center gap-1 rounded-full bg-[#6366F1] px-3 py-1.5 text-xs font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:bg-[#4F46E5] active:bg-[#4338CA] transition-colors">
            <span class="text-base leading-none">+</span>
        </button>


        <!-- Десктопная версия кнопки -->
        <button
            class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-[#6366F1] px-3.5 py-1.5 text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:bg-[#4F46E5] active:bg-[#4338CA] transition-colors">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/10">
                +
            </span>
            <span>Новая запись</span>
        </button>

        <!-- Админка (только для админов) -->
        @if(!Str::startsWith(Request::path(), 'panel') && Auth::user()->hasRole('admin'))
            <a href="{{ route('panel.index') }}" 
               class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                <span>Админка</span>
            </a>
        @endif


        <!-- Переключатель темы -->
        <button id="themeToggle"
            class="h-8 w-8 rounded-full border border-slate-300 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-100 transition-colors dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
            aria-label="Переключить тему">
            <x-icon name="sun" size="sm" class="hidden dark:block" />
            <x-icon name="moon" size="sm" class="block dark:hidden" />
        </button>


        <!-- Аватар / профиль -->
        <div class="relative">
            <button
                class="menu-trigger h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-800 border border-slate-300 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors"
                aria-label="Профиль">
                АМ
            </button>
            <div
                class="menu-panel z-[100] hidden w-48 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                <a href="{{ route('profile.edit') }}"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-user"></i></span>
                    <span>Профиль</span>
                </a>
                <a href="{{ route('settings.index') }}"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-cog"></i></span>
                    <span>Настройки</span>
                </a>
                @if(!Str::startsWith(Request::path(), 'panel') && Auth::user()->hasRole('admin'))
                    <a href="{{ route('panel.index') }}"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                        <span class="w-4 text-amber-500 dark:text-amber-300"><i class="fa-solid fa-shield-halved"></i></span>
                        <span>Админка</span>
                    </a>
                @endif
                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                        <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                class="fa-solid fa-right-from-bracket"></i></span>
                        <span>Выйти</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('mobile-menu')