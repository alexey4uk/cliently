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
            Str::startsWith(Request::path(), 'analytics')
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
            class="flex flex-col flex-grow bg-white dark:bg-slate-900 pt-6 pb-6 overflow-y-auto overflow-x-hidden border-r border-slate-200 dark:border-slate-800 shadow-sm">
            <!-- Логотип -->
            <div class="flex items-center flex-shrink-0 px-4 lg:px-6 mb-8"
                :class="collapsed ? 'justify-center px-2' : 'justify-start'">
                <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.index') : route('dashboard') }}"
                    class="flex items-center gap-3 group cursor-pointer rounded-lg p-2 -ml-2 transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:shadow-sm"
                    :class="collapsed ? 'flex-col gap-2 px-2' : 'flex-row'">
                    <div class="transition-transform duration-200 group-hover:scale-105">
                        <x-logo size="sidebar" />
                    </div>
                    <span x-show="!collapsed" x-cloak
                        class="sidebar-text text-xl font-bold text-slate-900 dark:text-white tracking-tight uppercase font-display whitespace-nowrap transition-opacity duration-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
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
                            class="sidebar-section-title px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-grip-vertical text-[10px] opacity-50"></i>
                            <span>Основное</span>
                        </h3>
                        <nav class="space-y-1">
                            <!-- Панель управления / Главная -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('analytics.view')
                                    <a href="{{ route('panel.index') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ Request::routeIs('panel.index')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                        :title="collapsed ? 'Главная' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0 relative"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-chart-line transition-transform duration-200 {{ Request::routeIs('panel.index') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Главная</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Главная
                                        </div>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ route('dashboard') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('dashboard')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                    :title="collapsed ? 'Главная' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-chart-line transition-transform duration-200 {{ Request::routeIs('dashboard') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Главная</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Главная
                                    </div>
                                </a>
                            @endif

                            <!-- Клиенты -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('clients.view')
                                    <a href="{{ route('panel.clients') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.clients')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                        :title="collapsed ? 'Клиенты' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-users transition-transform duration-200 {{ Request::routeIs('panel.clients') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Клиенты</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Клиенты
                                        </div>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ route('clients.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('clients.*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                    :title="collapsed ? 'Клиенты' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-users transition-transform duration-200 {{ Request::routeIs('clients.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Клиенты</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Клиенты
                                    </div>
                                </a>
                            @endif

                            <!-- Записи -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('appointments.view')
                                    <a href="{{ route('panel.appointments') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.appointments')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Записи' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-calendar-check transition-transform duration-200 {{ Request::routeIs('panel.appointments') ? 'scale-110' : 'group-hover:scale-110' }}"
                                                :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Записи</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Записи
                                        </div>
                                    </a>
                                @endcan
                            @else
                                <a href="{{ route('appointments.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('appointments.*') && !Request::routeIs('appointments.calendar')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Записи' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-calendar-check transition-transform duration-200 {{ Request::routeIs('appointments.*') && !Request::routeIs('appointments.calendar') ? 'scale-110' : 'group-hover:scale-110' }}"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Записи</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Записи
                                    </div>
                                </a>

                                <!-- Календарь -->
                                <a href="{{ route('appointments.calendar') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('appointments.calendar')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Календарь' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-calendar transition-transform duration-200 {{ Request::routeIs('appointments.calendar') ? 'scale-110' : 'group-hover:scale-110' }}"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Календарь</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Календарь
                                    </div>
                                </a>
                            @endif
                        </nav>
                    </div>

                    <!-- Управление -->
                    <div>
                        <button @click="managementOpen = !managementOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-gear text-[10px] opacity-60"></i>
                                <span>Управление</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': managementOpen }"></i>
                        </button>
                        <nav x-show="managementOpen || collapsed" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @if(Str::startsWith(Request::path(), 'panel'))
                                <!-- Админские разделы -->
                                @can('users.view')
                                    <a href="{{ route('panel.users') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.users')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Пользователи' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-users-gear transition-transform duration-200 {{ Request::routeIs('panel.users') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Пользователи</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Пользователи
                                        </div>
                                    </a>
                                @endcan

                                @can('roles.view')
                                    <a href="{{ route('panel.roles') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.roles')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Роли' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-shield-halved transition-transform duration-200 {{ Request::routeIs('panel.roles') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Роли</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Роли
                                        </div>
                                    </a>
                                @endcan

                                @can('roles.view')
                                    <a href="{{ route('panel.permissions') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.permissions')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Права' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-key transition-transform duration-200 {{ Request::routeIs('panel.permissions') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Права</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Права
                                        </div>
                                    </a>
                                @endcan

                                @can('businesses.view')
                                    <a href="{{ route('panel.businesses') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.businesses')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Бизнесы' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-building transition-transform duration-200 {{ Request::routeIs('panel.businesses') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Бизнесы</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Бизнесы
                                        </div>
                                    </a>
                                @endcan

                                @can('services.view')
                                    <a href="{{ route('panel.services') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.services')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Услуги' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-scissors transition-transform duration-200 {{ Request::routeIs('panel.services') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Услуги</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Услуги
                                        </div>
                                    </a>
                                @endcan

                                @can('locations.view')
                                    <a href="{{ route('panel.locations') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.locations')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Локации' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-location-dot transition-transform duration-200 {{ Request::routeIs('panel.locations') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Локации</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Локации
                                        </div>
                                    </a>
                                @endcan

                                @can('masters.view')
                                    <a href="{{ route('panel.masters') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.masters')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Мастера' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-user-tie transition-transform duration-200 {{ Request::routeIs('panel.masters') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Мастера</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Мастера
                                        </div>
                                    </a>
                                @endcan

                                @can('telegram.manage')
                                    <a href="{{ route('panel.telegram.management') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.telegram.management')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Telegram Bot' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-brands fa-telegram transition-transform duration-200 {{ Request::routeIs('panel.telegram.management') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Telegram Bot</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Telegram Bot
                                        </div>
                                    </a>
                                @endcan
                            @else
                                <!-- Пользовательские разделы -->
                                <!-- Настройки бизнеса -->
                                <a href="{{ route('settings.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.index')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Бизнес' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-building transition-transform duration-200 {{ Request::routeIs('settings.index') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Бизнес</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Бизнес
                                    </div>
                                </a>

                                <!-- Локации -->
                                <a href="{{ route('settings.locations') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.locations*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Локации' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-location-dot transition-transform duration-200 {{ Request::routeIs('settings.locations*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Локации</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Локации
                                    </div>
                                </a>

                                <!-- Услуги -->
                                <a href="{{ route('services.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('services.*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Услуги' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-scissors transition-transform duration-200 {{ Request::routeIs('services.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Услуги</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Услуги
                                    </div>
                                </a>

                                <!-- Мастера -->
                                <a href="{{ route('settings.masters') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.masters*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Мастера' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-user-tie transition-transform duration-200 {{ Request::routeIs('settings.masters*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Мастера</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Мастера
                                    </div>
                                </a>

                                <!-- Онлайн запись -->
                                <a href="{{ route('settings.online-booking') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.online-booking*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Онлайн запись' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-link transition-transform duration-200 {{ Request::routeIs('settings.online-booking*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Онлайн запись</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Онлайн запись
                                    </div>
                                </a>

                                <!-- Telegram -->
                                <a href="{{ route('settings.telegram') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.telegram*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Telegram Bot' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-brands fa-telegram transition-transform duration-200 {{ Request::routeIs('settings.telegram*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Telegram Bot</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Telegram Bot
                                    </div>
                                </a>
                            @endif
                        </nav>
                    </div>

                    <!-- Аналитика -->
                    <div>
                        <button @click="analyticsOpen = !analyticsOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-chart-bar text-[10px] opacity-60"></i>
                                <span>Аналитика</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': analyticsOpen }"></i>
                        </button>
                        <nav x-show="analyticsOpen || collapsed" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('analytics.view')
                                    <a href="{{ route('panel.analytics') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.analytics')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Аналитика' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-chart-line transition-transform duration-200 {{ Request::routeIs('panel.analytics') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Аналитика</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Аналитика
                                        </div>
                                    </a>
                                @endcan

                                @can('support.view')
                                    <a href="{{ route('panel.support') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.support')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Поддержка' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-headset transition-transform duration-200 {{ Request::routeIs('panel.support') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Поддержка</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Поддержка
                                        </div>
                                    </a>
                                @endcan
                            @else
                                <!-- Клиентская часть -->
                                <a href="{{ route('analytics.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('analytics.index')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                    :title="collapsed ? 'Обзор' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-chart-bar transition-transform duration-200 {{ Request::routeIs('analytics.index') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Обзор</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Обзор
                                    </div>
                                </a>
                                <a href="{{ route('analytics.financial') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('analytics.financial')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                    :title="collapsed ? 'Финансовая' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-money-bill-wave transition-transform duration-200 {{ Request::routeIs('analytics.financial') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Финансовая</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Финансовая
                                    </div>
                                </a>
                                <a href="{{ route('analytics.general') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('analytics.general')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                    :title="collapsed ? 'Общая' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-chart-line transition-transform duration-200 {{ Request::routeIs('analytics.general') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Общая</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Общая
                                    </div>
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
                            class="group w-full flex items-center py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-all duration-200 relative"
                            :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                            :title="collapsed ? 'Выйти' : ''"
                            x-data="{ tooltip: false }"
                            @mouseenter="if (collapsed) tooltip = true"
                            @mouseleave="tooltip = false">
                            <div class="flex items-center justify-center flex-shrink-0"
                                :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                <i class="fa-solid fa-right-from-bracket transition-transform duration-200 group-hover:scale-110"
                                    :class="collapsed ? 'text-lg' : 'text-base'"></i>
                            </div>
                            <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Выйти</span>
                            <div x-show="tooltip && collapsed" 
                                 x-transition
                                 class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Выйти
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
