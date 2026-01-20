@props([
    'showNewButton' => false,
    'showAdminButton' => false,
    'showNotifications' => false,
    'showProfile' => true,
    'showMobileMenu' => true,
    'showClientButton' => false,
    'showAdminBadge' => false,
    'pageTitle' => 'Панель управления',
    'pageDescription' => null,
    'showNotificationsDropdown' => false,
])

<header class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 w-full sticky top-0 z-30">
    <div class="w-full px-4 md:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between gap-2 md:gap-4 min-w-0">
            <!-- Левая часть: Кнопка sidebar + Заголовок -->
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <!-- Кнопка сворачивания sidebar (только десктоп) -->
                <button @click.stop="toggleSidebar()" 
                        class="hidden lg:flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        aria-label="Свернуть/развернуть меню"
                        type="button">
                    <i class="fa-solid fa-bars-staggered text-base"></i>
                </button>
                
                <!-- Логотип (только мобильные) -->
                <a href="{{ route(Str::startsWith(Request::path(), 'panel') ? 'panel.index' : 'dashboard') }}" class="lg:hidden flex items-center gap-2.5 shrink-0 hover:opacity-80 transition-opacity">
                    <x-logo size="sm" />
                </a>
                
                <!-- Заголовок страницы -->
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white truncate">
                        {{ $pageTitle }}
                    </h1>
                    @if($pageDescription)
                        <p class="hidden md:block text-sm text-slate-500 dark:text-slate-400 mt-1 truncate">
                            {{ $pageDescription }}
                        </p>
                    @endif
                </div>

                <!-- Индикатор режима (только для админки) -->
                @if($showAdminBadge)
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 rounded-full">
                        <i class="fa-solid fa-shield-halved"></i>
                        Админ
                    </span>
                @endif
            </div>

            <!-- Правая часть: Действия -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">
                <!-- Клиентская часть -->
                @if($showClientButton)
                    <a href="{{ route('dashboard') }}" 
                       class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition-colors">
                        <i class="fa-solid fa-user text-xs"></i>
                        <span>Клиентская часть</span>
                    </a>
                @endif

                <!-- Кнопка "Новая запись" (только на определенных страницах) -->
                @if($showNewButton)
                    <button
                        class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Новая запись</span>
                    </button>
                @endif

                <!-- Админка (только для админов) -->
                @if($showAdminButton)
                    <a href="{{ route('panel.index') }}" 
                       class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                        <span>Админка</span>
                    </a>
                @endif

                <!-- Переключатель темы -->
                <button id="theme-toggle"
                    class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    aria-label="Переключить тему">
                    <x-icon name="sun" size="md" variant="solid" class="hidden dark:block" />
                    <x-icon name="moon" size="md" variant="solid" class="block dark:hidden" />
                </button>

                <!-- Уведомления -->
                @if($showNotificationsDropdown)
                    <div x-data="{ open: false }" class="relative">
                        <button
                            x-ref="notificationsButton"
                            @click="open = !open"
                            class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative"
                            aria-label="Уведомления">
                            <i class="fa-solid fa-bell text-base"></i>
                            <!-- Индикатор новых уведомлений -->
                            <span class="absolute top-2 right-2 h-2 w-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                        </button>
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="fixed z-[100] w-[calc(100vw-2rem)] sm:w-80 max-w-sm rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl"
                            style="display: none;"
                            x-init="
                                $watch('open', value => {
                                    if (value) {
                                        $nextTick(() => {
                                            const button = $refs.notificationsButton;
                                            const menu = $el;
                                            if (button) {
                                                const buttonRect = button.getBoundingClientRect();
                                                const viewportHeight = window.innerHeight;
                                                const viewportWidth = window.innerWidth;
                                                
                                                // Позиционируем меню под кнопкой
                                                menu.style.top = (buttonRect.bottom + 8) + 'px';
                                                menu.style.right = (viewportWidth - buttonRect.right) + 'px';
                                                
                                                // Проверяем, не выходит ли меню за границы
                                                const menuRect = menu.getBoundingClientRect();
                                                if (menuRect.bottom > viewportHeight - 10) {
                                                    menu.style.top = (buttonRect.top - menuRect.height - 8) + 'px';
                                                }
                                                if (menuRect.right > viewportWidth - 10) {
                                                    menu.style.right = '0.5rem';
                                                }
                                                if (menuRect.left < 10) {
                                                    menu.style.left = '0.5rem';
                                                    menu.style.right = 'auto';
                                                }
                                            }
                                        });
                                    }
                                });
                            ">
                            <!-- Заголовок -->
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                                    Уведомления
                                </h3>
                                <button class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                                    Отметить все как прочитанные
                                </button>
                            </div>
                            
                            <!-- Список уведомлений -->
                            <div class="max-h-96 overflow-y-auto">
                                <!-- Пример уведомления -->
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0 mt-0.5">
                                            <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                                <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                                Новая запись
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                У вас новая запись на завтра в 14:00
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                                2 часа назад
                                            </p>
                                        </div>
                                        <div class="shrink-0">
                                            <span class="h-2 w-2 bg-rose-500 rounded-full block"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Пример уведомления -->
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0 mt-0.5">
                                            <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                                Запись выполнена
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Запись от 15:00 была отмечена как выполненная
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                                5 часов назад
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Пример уведомления -->
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0 mt-0.5">
                                            <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                                                <i class="fa-solid fa-exclamation text-amber-600 dark:text-amber-400 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                                Требуется внимание
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                У вас есть записи, требующие подтверждения
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                                Вчера
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Футер -->
                            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                                <a href="#" class="text-sm text-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors block">
                                    Показать все уведомления
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Профиль пользователя (только десктоп) -->
                @if($showProfile)
                    <div x-data="{ open: false }" class="relative hidden lg:block">
                        @auth
                            <button
                                x-ref="profileButton"
                                @click="open = !open"
                                class="h-9 w-9 rounded-xl flex items-center justify-center text-sm font-semibold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors overflow-hidden ring-1 ring-slate-200 dark:ring-slate-700"
                                aria-label="Профиль">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <span class="h-full w-full bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                    </span>
                                @endif
                            </button>
                        @else
                            <button
                                x-ref="profileButton"
                                @click="open = !open"
                                class="h-9 w-9 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-xs font-semibold text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-slate-700 hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors"
                                aria-label="Профиль">
                                АМ
                            </button>
                        @endauth
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="fixed z-[100] w-[calc(100vw-2rem)] sm:w-56 max-w-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl overflow-hidden"
                            style="display: none;"
                            x-init="
                                $watch('open', value => {
                                    if (value) {
                                        $nextTick(() => {
                                            const button = $refs.profileButton;
                                            const menu = $el;
                                            if (button) {
                                                const buttonRect = button.getBoundingClientRect();
                                                const viewportHeight = window.innerHeight;
                                                const viewportWidth = window.innerWidth;
                                                
                                                // Позиционируем меню под кнопкой
                                                menu.style.top = (buttonRect.bottom + 8) + 'px';
                                                menu.style.right = (viewportWidth - buttonRect.right) + 'px';
                                                
                                                // Проверяем, не выходит ли меню за границы
                                                const menuRect = menu.getBoundingClientRect();
                                                if (menuRect.bottom > viewportHeight - 10) {
                                                    menu.style.top = (buttonRect.top - menuRect.height - 8) + 'px';
                                                }
                                                if (menuRect.right > viewportWidth - 10) {
                                                    menu.style.right = '0.5rem';
                                                }
                                                if (menuRect.left < 10) {
                                                    menu.style.left = '0.5rem';
                                                    menu.style.right = 'auto';
                                                }
                                            }
                                        });
                                    }
                                });
                            ">
                            <!-- Информация о пользователе -->
                            @auth
                                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-sm font-semibold text-white overflow-hidden shrink-0">
                                            @if(Auth::user()->avatar)
                                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                                     alt="{{ Auth::user()->name }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
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
                            <div class="py-1.5 px-1.5">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-left text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors rounded-lg">
                                    <i class="fa-solid fa-user w-4 text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                    <span>Профиль</span>
                                </a>
                                <div class="border-t border-slate-200 dark:border-slate-800 my-1.5 mx-1.5"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-left text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors rounded-lg">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-xs"></i>
                                        <span>Выйти</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Кнопка меню (только мобильные) -->
                @if($showMobileMenu)
                    @include('mobile-menu')
                @endif
            </div>
        </div>
    </div>
</header>
