<div class="sidebar-container hidden lg:flex lg:flex-shrink-0 fixed left-0 top-0 bottom-0 z-20" x-data="{
    // Переменные для клиентской части (группировка по задачам)
    clientWorkOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('appointments.*') ||
            Request::routeIs('clients.*')
        )) 
        ? 'true' : 'false' 
    }},
    clientCatalogOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('services.*') ||
            Request::routeIs('settings.masters*') ||
            Request::routeIs('settings.locations*')
        )) 
        ? 'true' : 'false' 
    }},
    clientOnlineOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('settings.online-booking*')
        )) 
        ? 'true' : 'false' 
    }},
    clientTeamOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('settings.users*') ||
            Request::routeIs('settings.roles*')
        )) 
        ? 'true' : 'false' 
    }},
    clientIntegrationsOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
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
    businessSettingsOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('settings.index') ||
            Request::routeIs('settings.online-booking*') ||
            Request::routeIs('services.*') ||
            Request::routeIs('settings.masters*') ||
            Request::routeIs('settings.locations*') ||
            Request::routeIs('settings.users*') ||
            Request::routeIs('settings.roles*') ||
            Request::routeIs('settings.telegram*')
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
    panelOperationsOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/appointments') ||
            Str::startsWith(Request::path(), 'panel/clients') ||
            Str::startsWith(Request::path(), 'panel/businesses')
        )) 
        ? 'true' : 'false' 
    }},
    adminAccessOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/users') ||
            Str::startsWith(Request::path(), 'panel/roles') ||
            Str::startsWith(Request::path(), 'panel/permissions') ||
            Str::startsWith(Request::path(), 'panel/business-roles')
        )) 
        ? 'true' : 'false' 
    }},
    commsOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/broadcasts') ||
            Str::startsWith(Request::path(), 'panel/notifications') ||
            Str::startsWith(Request::path(), 'panel/settings/notifications')
        )) 
        ? 'true' : 'false' 
    }},
    platformOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/plans') ||
            Str::startsWith(Request::path(), 'panel/invoices')
        )) 
        ? 'true' : 'false' 
    }},
    panelIntegrationsOpen: {{ 
        (Str::startsWith(Request::path(), 'panel') && (
            Str::startsWith(Request::path(), 'panel/telegram-management')
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
            this.clientWorkOpen = true;
            this.clientCatalogOpen = true;
            this.clientOnlineOpen = true;
            this.clientTeamOpen = true;
            this.clientIntegrationsOpen = true;
            this.subscriptionOpen = true;
            this.businessSettingsOpen = true;
            this.analyticsOpen = true;
            this.supportOpen = true;
            this.panelOperationsOpen = true;
            this.adminAccessOpen = true;
            this.commsOpen = true;
            this.platformOpen = true;
            this.panelIntegrationsOpen = true;
            this.contentOpen = true;
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
                    this.clientWorkOpen = true;
                    this.clientCatalogOpen = true;
                    this.clientOnlineOpen = true;
                    this.clientTeamOpen = true;
                    this.clientIntegrationsOpen = true;
                    this.subscriptionOpen = true;
                    this.businessSettingsOpen = true;
                    this.analyticsOpen = true;
                    this.supportOpen = true;
                    this.panelOperationsOpen = true;
                    this.adminAccessOpen = true;
                    this.commsOpen = true;
                    this.platformOpen = true;
                    this.panelIntegrationsOpen = true;
                    this.contentOpen = true;
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
                                $currentBusinessRole = $pivot?->pivot->role_id ? \App\Models\BusinessRole::find($pivot->pivot->role_id)?->slug : null;
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

                    @if(Str::startsWith(Request::path(), 'panel'))
                        @include('sidebar.panel')
                    @else
                        @include('sidebar.client')
                    @endif
                </div>
            </div>

            @include('sidebar.bottom')
        </div>
    </div>
</div>
