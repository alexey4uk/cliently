<div class="sidebar-container hidden lg:flex lg:flex-shrink-0 fixed left-0 top-0 bottom-0 z-20" x-data="{
    managementOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/users') ||
            Str::startsWith(Request::path(), 'panel/roles') ||
            Str::startsWith(Request::path(), 'panel/permissions') ||
            Str::startsWith(Request::path(), 'panel/businesses') ||
            Str::startsWith(Request::path(), 'panel/services') ||
            Str::startsWith(Request::path(), 'panel/locations') ||
            Str::startsWith(Request::path(), 'panel/masters') ||
            Str::startsWith(Request::path(), 'panel/telegram-management')
        )) || 
        (!Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'settings') ||
            (Request::path() === 'services' || Str::startsWith(Request::path(), 'services/'))
        )) 
        ? 'true' : 'false' 
    }},
    analyticsOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/analytics') ||
            Str::startsWith(Request::path(), 'panel/support')
        )) || 
        (!Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'finance') ||
            Str::startsWith(Request::path(), 'reports')
        )) 
        ? 'true' : 'false' 
    }},
    collapsed: (() => {
        try {
            return localStorage.getItem('sidebarCollapsed') === 'true';
        } catch (e) {
            return false;
        }
    })(),
    transitionsEnabled: false,
    init() {
        // Если sidebar свернут, автоматически открываем подменю для показа иконок
        if (this.collapsed) {
            this.managementOpen = true;
            this.analyticsOpen = true;
        }

        // Слушаем события переключения sidebar из layout
        const handleToggle = (e) => {
            if (e.detail && typeof e.detail.collapsed !== 'undefined') {
                // Включаем transitions при первом переключении (если еще не включены)
                if (!this.transitionsEnabled) {
                    this.transitionsEnabled = true;
                    this.$el.classList.add('transition-all', 'duration-300', 'ease-in-out');
                }

                this.collapsed = e.detail.collapsed;
                // При сворачивании автоматически открываем подменю
                if (this.collapsed) {
                    this.managementOpen = true;
                    this.analyticsOpen = true;
                }
            }
        };

        window.addEventListener('sidebar-toggle', handleToggle);

        // Очистка при размонтировании компонента
        this.$el.addEventListener('alpine:destroy', () => {
            window.removeEventListener('sidebar-toggle', handleToggle);
        });
    }
}"
    :class="collapsed ? 'w-16' : 'w-64'">
    <div class="flex flex-col h-full w-full">
        <div
            class="flex flex-col flex-grow bg-white dark:bg-slate-900 pt-6 pb-6 overflow-y-auto overflow-x-hidden border-r border-slate-200 dark:border-slate-800">
            <!-- Логотип -->
            <div class="flex items-center flex-shrink-0 px-4 lg:px-6 mb-8"
                :class="collapsed ? 'justify-center px-2' : 'justify-start'">
                <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.index') : route('dashboard') }}"
                    class="flex items-center gap-3 group cursor-pointer hover:opacity-80 transition-opacity"
                    :class="collapsed ? 'flex-col gap-2' : 'flex-row'">
                    <x-logo size="sidebar" />
                    <span x-show="!collapsed" x-cloak
                        class="sidebar-text text-xl font-bold text-slate-900 dark:text-white tracking-tight uppercase font-display whitespace-nowrap">
                        CLIENTLY
                    </span>
                </a>
            </div>

            <!-- Основная навигация -->
            <div class="flex-grow flex flex-col">
                <div class="flex-1 space-y-6" :class="collapsed ? 'px-0' : 'px-4 lg:px-6'">
                    <!-- Основное -->
                    <div>
                        <h3 x-show="!collapsed" x-cloak
                            class="sidebar-section-title px-3 mb-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Основное
                        </h3>
                        <nav class="space-y-1.5">
                            <!-- Панель управления / Главная -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('analytics.view')
                                    <a href="{{ route('panel.index') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.index')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'" :title="collapsed ? 'Главная' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6 mr-0'">
                                            <i class="fa-solid fa-chart-line" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Главная</span>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ route('dashboard') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('dashboard')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'" :title="collapsed ? 'Главная' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6 mr-0'">
                                        <i class="fa-solid fa-chart-line" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Главная</span>
                                </a>
                            @endif


                            <!-- Клиенты -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('clients.view')
                                    <a href="{{ route('panel.clients') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.clients')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'" :title="collapsed ? 'Клиенты' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-users" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Клиенты</span>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ route('clients.index') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('clients.*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'" :title="collapsed ? 'Клиенты' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-users" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Клиенты</span>
                                </a>
                            @endif

                            <!-- Записи -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('appointments.view')
                                    <a href="{{ route('panel.appointments') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.appointments')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Записи' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-calendar-check"
                                                :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Записи</span>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ route('appointments.index') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('appointments.*') && !Request::routeIs('appointments.calendar')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Записи' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-calendar-check"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Записи</span>
                                </a>
                            @endif
                        </nav>
                    </div>

                    <!-- Управление -->
                    <div>
                        <button @click="managementOpen = !managementOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                            <span>Управление</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': managementOpen }"></i>
                        </button>
                        <nav x-show="managementOpen || collapsed" class="space-y-1.5 overflow-hidden">
                            @if(Str::startsWith(Request::path(), 'panel'))
                                <!-- Админские разделы -->
                                @can('users.view')
                                    <a href="{{ route('panel.users') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.users')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Пользователи' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-users-gear" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Пользователи</span>
                                    </a>
                                @endcan

                                @can('roles.view')
                                    <a href="{{ route('panel.roles') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.roles')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Роли' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-shield-halved" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Роли</span>
                                    </a>
                                @endcan

                                @can('roles.view')
                                    <a href="{{ route('panel.permissions') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.permissions')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Права' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-key" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Права</span>
                                    </a>
                                @endcan

                                @can('businesses.view')
                                    <a href="{{ route('panel.businesses') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.businesses')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Бизнесы' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-building" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Бизнесы</span>
                                    </a>
                                @endcan

                                @can('services.view')
                                    <a href="{{ route('panel.services') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.services')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Услуги' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-scissors" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Услуги</span>
                                    </a>
                                @endcan

                                @can('locations.view')
                                    <a href="{{ route('panel.locations') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.locations')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Локации' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-location-dot" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Локации</span>
                                    </a>
                                @endcan

                                @can('masters.view')
                                    <a href="{{ route('panel.masters') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.masters')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Мастера' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-user-tie" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Мастера</span>
                                    </a>
                                @endcan

                                @can('telegram.manage')
                                    <a href="{{ route('panel.telegram.management') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.telegram.management')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Telegram Bot' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-brands fa-telegram" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Telegram Bot</span>
                                    </a>
                                @endcan
                            @else
                                <!-- Пользовательские разделы -->
                                <!-- Настройки бизнеса -->
                                <a href="{{ route('settings.index') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('settings.index')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Бизнес' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-building" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Бизнес</span>
                                </a>

                                <!-- Локации -->
                                <a href="{{ route('settings.locations') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('settings.locations*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Локации' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-location-dot"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Локации</span>
                                </a>

                                <!-- Услуги -->
                                <a href="{{ route('services.index') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('services.*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Услуги' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-scissors" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Услуги</span>
                                </a>

                                <!-- Мастера -->
                                <a href="{{ route('settings.masters') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('settings.masters*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Мастера' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-user-tie" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Мастера</span>
                                </a>

                                <!-- Онлайн запись -->
                                <a href="{{ route('settings.online-booking') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('settings.online-booking*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Мастера' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-link" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Онлайн запись</span>
                                </a>

                                <!-- Telrgram -->
                                <a href="{{ route('settings.telegram') }}"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('settings.telegram*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Мастера' : ''">
                                    <div class="flex items-center justify-center shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-brands fa-telegram" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak class="sidebar-text ml-3 whitespace-nowrap">Telegram
                                        Bot</span>
                                </a>
                            @endif
                        </nav>
                    </div>

                    <!-- Аналитика -->
                    <div>
                        <button @click="analyticsOpen = !analyticsOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                            <span>Аналитика</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': analyticsOpen }"></i>
                        </button>
                        <nav x-show="analyticsOpen || collapsed" class="space-y-1.5 overflow-hidden">
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('analytics.view')
                                    <a href="{{ route('panel.analytics') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.analytics')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Аналитика' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6 mr-0'">
                                            <i class="fa-solid fa-chart-line" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Аналитика</span>
                                    </a>
                                @endcan

                                @can('support.view')
                                    <a href="{{ route('panel.support') }}"
                                        class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('panel.support')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                        :title="collapsed ? 'Поддержка' : ''">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                            <i class="fa-solid fa-headset" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text ml-3 whitespace-nowrap">Поддержка</span>
                                    </a>
                                @endcan
                            @else
                                <!-- Финансы -->
                                <a href="#"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('finance.*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Финансы' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6 mr-0'">
                                        <i class="fa-solid fa-chart-line" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Финансы</span>
                                </a>

                                <!-- Отчеты -->
                                <a href="#"
                                    class="group flex items-center py-3 text-sm font-medium rounded-xl transition-colors duration-200 {{ Request::routeIs('reports.*')
                                        ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-4'"
                                    :title="collapsed ? 'Отчеты' : ''">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                        <i class="fa-solid fa-chart-bar" :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text ml-3 whitespace-nowrap">Отчеты</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Нижняя часть сайдбара -->
            <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                <div :class="collapsed ? 'px-0' : 'px-4 lg:px-6'">
                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="group w-full flex items-center py-3 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-xl transition-colors duration-200"
                            :class="collapsed ? 'justify-center mx-2' : 'px-4'" :title="collapsed ? 'Выйти' : ''">
                            <div class="flex items-center justify-center flex-shrink-0"
                                :class="collapsed ? 'mx-auto w-7 h-7' : 'w-6 h-6'">
                                <i class="fa-solid fa-right-from-bracket"
                                    :class="collapsed ? 'text-lg' : 'text-base'"></i>
                            </div>
                            <span x-show="!collapsed" x-cloak class="sidebar-text ml-3 whitespace-nowrap">Выйти</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
