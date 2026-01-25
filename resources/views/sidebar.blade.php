<div class="sidebar-container hidden lg:flex lg:flex-shrink-0 fixed left-0 top-0 bottom-0 z-20" x-data="{
    // Переменные для клиентской части
    businessOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('services.*') ||
            Request::routeIs('settings.masters*') ||
            Request::routeIs('settings.locations*')
        )) 
        ? 'true' : 'false' 
    }},
    teamOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('settings.index') ||
            Request::routeIs('settings.online-booking*') ||
            Request::routeIs('settings.users*') ||
            Request::routeIs('settings.roles*') ||
            Request::routeIs('settings.telegram*')
        )) 
        ? 'true' : 'false' 
    }},
    subscriptionOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('subscription.*')
        )) 
        ? 'true' : 'false' 
    }},
    analyticsOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/analytics')
        )) || 
        (!Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'analytics')
        )) 
        ? 'true' : 'false' 
    }},
    supportOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/support') ||
            Str::startsWith(Request::path(), 'panel/tickets') ||
            Str::startsWith(Request::path(), 'panel/ticket-categories')
        )) || 
        (!Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'tickets')
        )) 
        ? 'true' : 'false' 
    }},
    // Переменные для админ-панели
    adminOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/users') ||
            Str::startsWith(Request::path(), 'panel/roles') ||
            Str::startsWith(Request::path(), 'panel/permissions') ||
            Str::startsWith(Request::path(), 'panel/business-roles') ||
            Str::startsWith(Request::path(), 'panel/notifications') ||
            Str::startsWith(Request::path(), 'panel/settings/notifications')
        )) 
        ? 'true' : 'false' 
    }},
    platformOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/businesses') ||
            Str::startsWith(Request::path(), 'panel/plans') ||
            Str::startsWith(Request::path(), 'panel/invoices') ||
            Str::startsWith(Request::path(), 'panel/settings/bepaid')
        )) 
        ? 'true' : 'false' 
    }},
    contentOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/services') ||
            Str::startsWith(Request::path(), 'panel/locations') ||
            Str::startsWith(Request::path(), 'panel/masters')
        )) 
        ? 'true' : 'false' 
    }},
    panelIntegrationsOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/telegram-management')
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
            this.businessOpen = true;
            this.teamOpen = true;
            this.subscriptionOpen = true;
            this.integrationsOpen = true;
            this.analyticsOpen = true;
            this.supportOpen = true;
            this.adminOpen = true;
            this.platformOpen = true;
            this.contentOpen = true;
            this.panelIntegrationsOpen = true;
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
                    this.businessOpen = true;
                    this.teamOpen = true;
                    this.subscriptionOpen = true;
                    this.integrationsOpen = true;
                    this.analyticsOpen = true;
                    this.supportOpen = true;
                    this.adminOpen = true;
                    this.platformOpen = true;
                    this.contentOpen = true;
                    this.panelIntegrationsOpen = true;
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
                    @php
                        // Получаем бизнес и роль для проверки прав доступа (для клиентской части)
                        $user = Auth::user();
                        $currentBusiness = null;
                        $currentBusinessRole = null;
                        $currentBusinessRoleId = null;
                        $permissionService = null;
                        if ($user) {
                            $user->load('businesses');
                            $currentBusiness = $user->businesses->first();
                            if ($currentBusiness) {
                                $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
                                $currentBusinessRole = $pivot?->pivot->role ?? null;
                                $currentBusinessRoleId = $pivot?->pivot->role_id;
                                if ($currentBusinessRoleId) {
                                    $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                                }
                            }
                        }

                        // Функция для проверки бизнес-прав
                        $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
                            if (!$currentBusinessRoleId || !$permissionService) {
                                return false;
                            }
                            return $permissionService->hasPermission($currentBusinessRoleId, $permission);
                        };
                        
                        // Функция для проверки наличия хотя бы одного права из списка
                        $hasAnyPermission = function($permissions) use ($hasBusinessPermission, $user) {
                            foreach ($permissions as $permission) {
                                if (str_starts_with($permission, 'client.') || str_starts_with($permission, 'panel.')) {
                                    // Бизнес-права
                                    if (str_starts_with($permission, 'client.')) {
                                        if ($hasBusinessPermission($permission)) {
                                            return true;
                                        }
                                    } else {
                                        // Spatie права
                                        if ($user && $user->can($permission)) {
                                            return true;
                                        }
                                    }
                                } else {
                                    // Spatie права без префикса
                                    if ($user && $user->can($permission)) {
                                        return true;
                                    }
                                }
                            }
                            return false;
                        };
                    @endphp

                    <!-- Рабочий процесс -->
                    <div>
                        <h3 x-show="!collapsed" x-cloak
                            class="sidebar-section-title px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-[10px] opacity-50"></i>
                            <span>Рабочий процесс</span>
                        </h3>
                        <nav class="space-y-1">
                            <!-- Панель управления / Главная -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('panel.analytics.view')
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

                            <!-- Записи -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('panel.appointments.view')
                                    <a href="{{ route('panel.appointments') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.appointments*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Записи' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-calendar-check transition-transform duration-200 {{ Request::routeIs('panel.appointments*') ? 'scale-110' : 'group-hover:scale-110' }}"
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
                                @if($hasBusinessPermission('client.appointments.view'))
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
                            @endif

                            <!-- Клиенты -->
                            @if(Str::startsWith(Request::path(), 'panel'))
                                @can('panel.clients.view')
                                    <a href="{{ route('panel.clients') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.clients*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'" 
                                        :title="collapsed ? 'Клиенты' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-users transition-transform duration-200 {{ Request::routeIs('panel.clients*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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
                                @if($hasBusinessPermission('client.clients.view'))
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
                            @endif

                        </nav>
                    </div>

                    <!-- Ресурсы (клиентская часть) -->
                    @if(!Str::startsWith(Request::path(), 'panel'))
                    @php
                        $hasResourcesAccess = $hasBusinessPermission('client.locations.view') ||
                                             $hasBusinessPermission('client.services.view') ||
                                             $hasBusinessPermission('client.masters.view');
                    @endphp
                    @if($hasResourcesAccess)
                    <div>
                        <button @click="businessOpen = !businessOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-folder-open text-[10px] opacity-60"></i>
                                <span>Ресурсы</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': businessOpen }"></i>
                        </button>
                        <nav x-show="businessOpen || collapsed" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                                <!-- Услуги -->
                                @if($hasBusinessPermission('client.services.view'))
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
                                @endif

                                <!-- Мастера -->
                                @if($hasBusinessPermission('client.masters.view'))
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
                                @endif

                                <!-- Локации -->
                                @if($hasBusinessPermission('client.locations.view'))
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
                                @endif

                        </nav>
                    </div>
                    @endif
                    @endif

                    <!-- Настройки (клиентская часть) -->
                    @if(!Str::startsWith(Request::path(), 'panel'))
                    @php
                        // Проверяем доступ к Telegram боту согласно тарифу
                        $hasTelegramAccess = false;
                        if ($currentBusiness && $hasBusinessPermission('client.telegram.manage')) {
                            $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
                            if ($ownerRole) {
                                $ownerPivot = \Illuminate\Support\Facades\DB::table('business_user')
                                    ->where('business_id', $currentBusiness->id)
                                    ->where('role_id', $ownerRole->id)
                                    ->first();
                                if ($ownerPivot) {
                                    $owner = \App\Models\User::find($ownerPivot->user_id);
                                    if ($owner) {
                                        $subscriptionService = app(\App\Services\SubscriptionService::class);
                                        $telegramEnabled = $subscriptionService->getLimit($owner, 'telegram_bot_enabled');
                                        $hasTelegramAccess = $telegramEnabled === true;
                                    }
                                }
                            }
                        }
                        $hasSettingsAccess = $hasBusinessPermission('client.businesses.update') ||
                                           $hasBusinessPermission('client.business.users.view') ||
                                           $hasBusinessPermission('client.business.roles.manage') ||
                                           ($hasBusinessPermission('client.telegram.manage') && $hasTelegramAccess);
                    @endphp
                    @if($hasSettingsAccess)
                    <div>
                        <button @click="teamOpen = !teamOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-gear text-[10px] opacity-60"></i>
                                <span>Настройки</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': teamOpen }"></i>
                        </button>
                        <nav x-show="teamOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                                <!-- Бизнес -->
                                @if($hasBusinessPermission('client.businesses.update'))
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
                                @endif

                                <!-- Онлайн запись -->
                                @if($hasBusinessPermission('client.businesses.update'))
                                    <a href="{{ route('settings.online-booking') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.online-booking*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Онлайн-запись' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-link transition-transform duration-200 {{ Request::routeIs('settings.online-booking*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Онлайн-запись</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Онлайн-запись
                                        </div>
                                    </a>
                                @endif

                                <!-- Пользователи -->
                                @php
                                    $hasUsersPermission = false;
                                    if ($currentBusinessRoleId) {
                                        $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                                        $hasUsersPermission = $permissionService->hasPermission($currentBusinessRoleId, 'client.business.users.view');
                                    }
                                @endphp
                                @if($hasUsersPermission)
                                    <a href="{{ route('settings.users.index') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.users*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Пользователи' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-users-gear transition-transform duration-200 {{ Request::routeIs('settings.users*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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
                                @endif

                                <!-- Роли -->
                                @php
                                    $hasRolesPermission = false;
                                    if ($currentBusinessRoleId) {
                                        $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                                        $hasRolesPermission = $permissionService->hasPermission($currentBusinessRoleId, 'client.business.roles.manage');
                                    }
                                @endphp
                                @if($hasRolesPermission)
                                    <a href="{{ route('settings.roles.index') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.roles*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Роли' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-shield-halved transition-transform duration-200 {{ Request::routeIs('settings.roles*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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
                                @endif

                                <!-- Telegram Bot -->
                                @if($hasBusinessPermission('client.telegram.manage') && $hasTelegramAccess)
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
                    @endif
                    @endif

                    <!-- Подписка (клиентская часть) -->
                    @if(!Str::startsWith(Request::path(), 'panel'))
                    @if($hasBusinessPermission('client.subscription.view'))
                    <div>
                        <button @click="subscriptionOpen = !subscriptionOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-[10px] opacity-60"></i>
                                <span>Подписка</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': subscriptionOpen }"></i>
                        </button>
                        <nav x-show="subscriptionOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            <a href="{{ route('subscription.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('subscription.*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Тарифы' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-credit-card transition-transform duration-200 {{ Request::routeIs('subscription.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak
                                    class="sidebar-text whitespace-nowrap font-medium">Тарифы</span>
                                <div x-show="tooltip && collapsed" 
                                     x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                    Тарифы
                                </div>
                            </a>
                        </nav>
                    </div>
                    @endif
                    @endif

                    <!-- Услуги и ресурсы (админ-панель) -->
                    @if(Str::startsWith(Request::path(), 'panel'))
                    @php
                        $hasContentAccess = $user && (
                            $user->can('panel.services.view') ||
                            $user->can('panel.locations.view') ||
                            $user->can('panel.masters.view')
                        );
                    @endphp
                    @if($hasContentAccess)
                    <div>
                        <button @click="contentOpen = !contentOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-folder-open text-[10px] opacity-60"></i>
                                <span>Ресурсы</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': contentOpen }"></i>
                        </button>
                        <nav x-show="contentOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @can('panel.services.view')
                                <a href="{{ route('panel.services') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.services*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Услуги' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-scissors transition-transform duration-200 {{ Request::routeIs('panel.services*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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

                            @can('panel.locations.view')
                                <a href="{{ route('panel.locations') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.locations*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Локации' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-location-dot transition-transform duration-200 {{ Request::routeIs('panel.locations*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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

                            @can('panel.masters.view')
                                <a href="{{ route('panel.masters') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.masters*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Мастера' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-user-tie transition-transform duration-200 {{ Request::routeIs('panel.masters*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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
                        </nav>
                    </div>
                    @endif
                    @endif

                    <!-- Аналитика -->
                    @php
                        // Определяем роль пользователя
                        $businessRoleModel = $currentBusinessRoleId ? \App\Models\BusinessRole::find($currentBusinessRoleId) : null;
                        $businessRoleSlug = $businessRoleModel ? $businessRoleModel->slug : ($currentBusinessRole ?? null);
                        $isOwner = $businessRoleSlug === 'owner';
                        
                        $hasAnalyticsAccess = false;
                        if (Str::startsWith(Request::path(), 'panel')) {
                            // Для админ-панели проверяем Spatie права
                            $hasAnalyticsAccess = $user && $user->can('panel.analytics.view');
                        } else {
                            // Для клиентской части проверяем подписку владельца бизнеса
                            if ($currentBusiness) {
                                $accessService = app(\App\Services\SubscriptionAccessService::class);
                                $hasAnalyticsAccess = $accessService->hasAccess($currentBusiness, 'analytics_enabled', 'client.analytics.view');
                            }
                        }
                    @endphp
                    @if($hasAnalyticsAccess)
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
                                @can('panel.analytics.view')
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
                            @else
                                <!-- Клиентская часть -->
                                @php
                                    // Проверяем доступ к аналитике согласно тарифу (используем существующую проверку выше)
                                    // $hasAnalyticsAccess уже определен выше в блоке @php
                                @endphp
                                @if($hasBusinessPermission('client.analytics.view') && $hasAnalyticsAccess)
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
                                            class="sidebar-text whitespace-nowrap font-medium">Финансы</span>
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
                            @endif
                        </nav>
                    </div>
                    @endif

                    <!-- Поддержка -->
                    @php
                        $hasSupportAccess = false;
                        if (Str::startsWith(Request::path(), 'panel')) {
                            // Для админ-панели проверяем Spatie права
                            $hasSupportAccess = $user && (
                                $user->can('panel.tickets.view') ||
                                $user->can('panel.tickets.categories.manage') ||
                                $user->can('panel.tickets.settings')
                            );
                        } else {
                            // Для клиентской части проверяем бизнес-права
                            $hasSupportAccess = $hasBusinessPermission('client.tickets.create') ||
                                               $hasBusinessPermission('client.tickets.view');
                        }
                    @endphp
                    @if($hasSupportAccess)
                    <div>
                        <button @click="supportOpen = !supportOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-ticket text-[10px] opacity-60"></i>
                                <span>Поддержка</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': supportOpen }"></i>
                        </button>
                        <nav x-show="supportOpen || collapsed" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @if(Str::startsWith(Request::path(), 'panel'))
                                <!-- Админские разделы -->
                                @can('panel.tickets.view')
                                    <a href="{{ route('panel.tickets') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ (Request::routeIs('panel.tickets') || Request::routeIs('panel.tickets.*')) && !Request::routeIs('panel.tickets.settings*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Тикеты' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-ticket transition-transform duration-200 {{ (Request::routeIs('panel.tickets') || Request::routeIs('panel.tickets.*')) && !Request::routeIs('panel.tickets.settings*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Тикеты</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Все тикеты
                                        </div>
                                    </a>
                                @endcan

                                @can('panel.tickets.categories.manage')
                                    <a href="{{ route('panel.ticket-categories.index') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.ticket-categories.*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Категории' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-tags transition-transform duration-200 {{ Request::routeIs('panel.ticket-categories.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Категории</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Категории
                                        </div>
                                    </a>
                                @endcan

                                @can('panel.tickets.settings')
                                    <a href="{{ route('panel.tickets.settings') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.tickets.settings*')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Настройки' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-gear transition-transform duration-200 {{ Request::routeIs('panel.tickets.settings*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                               :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Настройки</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Настройки тикетов
                                        </div>
                                    </a>
                                @endcan
                            @else
                                <!-- Клиентская часть -->
                                @if($hasBusinessPermission('client.tickets.create'))
                                    <a href="{{ route('tickets.create') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('tickets.create')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Создать тикет' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-plus transition-transform duration-200 {{ Request::routeIs('tickets.create') ? 'scale-110' : 'group-hover:scale-110' }}"
                                                :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Создать</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Создать тикет
                                        </div>
                                    </a>
                                @endif
                                
                                @if($hasBusinessPermission('client.tickets.view'))
                                    <a href="{{ route('tickets.index') }}"
                                        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('tickets.index') || Request::routeIs('tickets.show') || Request::routeIs('tickets.edit')
                                            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                        :title="collapsed ? 'Мои тикеты' : ''"
                                        x-data="{ tooltip: false }"
                                        @mouseenter="if (collapsed) tooltip = true"
                                        @mouseleave="tooltip = false">
                                        <div class="flex items-center justify-center flex-shrink-0"
                                            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                            <i class="fa-solid fa-ticket transition-transform duration-200 {{ Request::routeIs('tickets.index') || Request::routeIs('tickets.show') || Request::routeIs('tickets.edit') ? 'scale-110' : 'group-hover:scale-110' }}"
                                                :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                        </div>
                                        <span x-show="!collapsed" x-cloak
                                            class="sidebar-text whitespace-nowrap font-medium">Мои</span>
                                        <div x-show="tooltip && collapsed" 
                                             x-transition
                                             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                            Мои тикеты
                                        </div>
                                    </a>
                                @endif
                            @endif
                        </nav>
                    </div>
                    @endif


                    <!-- Администрирование (админ-панель) -->
                    @if(Str::startsWith(Request::path(), 'panel'))
                    @php
                        $hasAdminAccess = $user && (
                            $user->can('panel.access') ||
                            $user->can('panel.users.view') ||
                            $user->can('panel.roles.view') ||
                            $user->can('panel.business.roles.manage') ||
                            $user->can('panel.broadcasts.send')
                        );
                    @endphp
                    @if($hasAdminAccess)
                    <div>
                        <button @click="adminOpen = !adminOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-user-shield text-[10px] opacity-60"></i>
                                <span>Админ</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': adminOpen }"></i>
                        </button>
                        <nav x-show="adminOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @can('panel.users.view')
                                <a href="{{ route('panel.users') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.users*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Пользователи' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-users-gear transition-transform duration-200 {{ Request::routeIs('panel.users*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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

                            @can('panel.roles.view')
                                <a href="{{ route('panel.roles') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.roles*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Роли' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-shield-halved transition-transform duration-200 {{ Request::routeIs('panel.roles*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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

                            @can('panel.roles.view')
                                <a href="{{ route('panel.permissions') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.permissions*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Права' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-key transition-transform duration-200 {{ Request::routeIs('panel.permissions*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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

                            @can('panel.business.roles.manage')
                                <a href="{{ route('panel.business-roles.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.business-roles.*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Роли орг.' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-user-shield transition-transform duration-200 {{ Request::routeIs('panel.business-roles.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Роли орг.</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Роли организации
                                    </div>
                                </a>
                            @endcan

                            @can('panel.broadcasts.send')
                                <a href="{{ route('panel.broadcasts.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.broadcasts.*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Рассылки' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-paper-plane transition-transform duration-200 {{ Request::routeIs('panel.broadcasts.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Рассылки</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Рассылки
                                    </div>
                                </a>
                            @endcan

                            @can('panel.access')
                                <a href="{{ route('panel.notifications.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.notifications*') || Request::routeIs('panel.settings.notifications*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Уведомления' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-bell transition-transform duration-200 {{ (Request::routeIs('panel.notifications*') || Request::routeIs('panel.settings.notifications*')) ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Уведомления</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Уведомления
                                    </div>
                                </a>
                            @endcan
                        </nav>
                    </div>
                    @endif
                    @endif

                    <!-- Платформа и платежи (админ-панель) -->
                    @if(Str::startsWith(Request::path(), 'panel'))
                    @php
                        $hasPlatformAccess = $user && (
                            $user->can('panel.businesses.view') ||
                            $user->can('panel.plans.view') ||
                            $user->can('panel.payments.view') ||
                            $user->can('panel.payments.settings')
                        );
                    @endphp
                    @if($hasPlatformAccess)
                    <div>
                        <button @click="platformOpen = !platformOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-[10px] opacity-60"></i>
                                <span>Платежи</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': platformOpen }"></i>
                        </button>
                        <nav x-show="platformOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @can('panel.businesses.view')
                                <a href="{{ route('panel.businesses') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.businesses*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Бизнесы' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-building transition-transform duration-200 {{ Request::routeIs('panel.businesses*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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

                            @can('panel.plans.view')
                                <a href="{{ route('panel.plans.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.plans.*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Тарифы' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-tags transition-transform duration-200 {{ Request::routeIs('panel.plans.*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Тарифы</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Тарифы
                                    </div>
                                </a>
                            @endcan

                            @can('panel.payments.view')
                                <a href="{{ route('panel.invoices') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.invoices*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Платежи' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-credit-card transition-transform duration-200 {{ Request::routeIs('panel.invoices*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Платежи</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Платежи
                                    </div>
                                </a>
                            @endcan

                            @can('panel.payments.settings')
                                <a href="{{ route('panel.settings.bepaid') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.settings.bepaid*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'bePaid' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-cog transition-transform duration-200 {{ Request::routeIs('panel.settings.bepaid*') ? 'scale-110' : 'group-hover:scale-110' }}" 
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">bePaid</span>
                                    <div x-show="tooltip && collapsed" 
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Настройки bePaid
                                    </div>
                                </a>
                            @endcan
                        </nav>
                    </div>
                    @endif
                    @endif


                    <!-- Интеграции (админ-панель) -->
                    @if(Str::startsWith(Request::path(), 'panel'))
                    @can('panel.telegram.manage')
                    <div>
                        <button @click="panelIntegrationsOpen = !panelIntegrationsOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-plug text-[10px] opacity-60"></i>
                                <span>Интеграции</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': panelIntegrationsOpen }"></i>
                        </button>
                        <nav x-show="panelIntegrationsOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            <a href="{{ route('panel.telegram.management') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.telegram.management*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Telegram Bot' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-brands fa-telegram transition-transform duration-200 {{ Request::routeIs('panel.telegram.management*') ? 'scale-110' : 'group-hover:scale-110' }}" 
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
                        </nav>
                    </div>
                    @endcan
                    @endif
                </div>
            </div>

            <!-- Нижняя часть сайдбара -->
            <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                <div :class="collapsed ? 'px-0' : 'px-4 lg:px-6'">
                    @if(!Str::startsWith(Request::path(), 'panel') && Auth::check())
                        @php
                            $user = Auth::user();
                            $subscription = $user ? $user->activeSubscription() : null;
                            $plan = $subscription && $subscription->plan ? $subscription->plan : null;
                            
                            // Используем уже полученные данные о бизнесе и роли
                            $business = $currentBusiness ?? null;
                            $businessRole = $currentBusinessRoleId ? \App\Models\BusinessRole::find($currentBusinessRoleId) : ($currentBusinessRole ?? null);
                            $businessRoleSlug = is_object($businessRole) ? $businessRole->slug : $businessRole;
                            $businessRoleName = is_object($businessRole) ? ($businessRole->name ?? ucfirst($businessRole->slug)) : null;
                        @endphp
                        
                        @if($subscription && $plan && $hasBusinessPermission('client.subscription.view'))
                            <!-- Информация о тарифе -->
                            <a href="{{ route('subscription.current') }}"
                                class="group block mb-4 p-2.5 bg-gradient-to-br from-indigo-50 to-indigo-100/50 dark:from-indigo-500/10 dark:to-indigo-500/5 border border-indigo-200 dark:border-indigo-500/20 rounded-lg hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500/30 transition-all duration-200 relative"
                                :class="collapsed ? 'px-2 py-2' : ''"
                                :title="collapsed ? '{{ $plan->name }}' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div x-show="!collapsed" x-cloak class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400 text-xs flex-shrink-0"></i>
                                        <span class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $plan->name }}</span>
                                    </div>
                                    @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                        <div class="flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400">
                                            <i class="fa-solid fa-clock text-[10px]"></i>
                                            <span>Пробный период до {{ $subscription->trial_ends_at->format('d.m.Y') }}</span>
                                        </div>
                                    @elseif($subscription->isCancelled())
                                        <div class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400">
                                            <i class="fa-solid fa-exclamation-triangle text-[10px]"></i>
                                            <span>Отменена</span>
                                        </div>
                                    @elseif($plan->price)
                                        <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400">
                                            <i class="fa-solid fa-check-circle text-[10px]"></i>
                                            <span>Активна</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Иконка при свернутом sidebar -->
                                <div x-show="collapsed" class="flex justify-center">
                                    <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400 text-base"></i>
                                </div>
                                
                                <div x-show="tooltip && collapsed" 
                                     x-transition
                                     class="absolute left-full ml-2 px-2 py-1.5 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                    <div class="font-semibold mb-0.5">{{ $plan->name }}</div>
                                    @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                        <div class="text-green-400 text-[10px]">Пробный до {{ $subscription->trial_ends_at->format('d.m.Y') }}</div>
                                    @elseif($subscription->isCancelled())
                                        <div class="text-amber-400 text-[10px]">Отменена</div>
                                    @elseif($plan->price)
                                        <div class="text-slate-400 text-[10px]">Активна</div>
                                    @endif
                                </div>
                            </a>
                        @endif
                    @endif
                    
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
