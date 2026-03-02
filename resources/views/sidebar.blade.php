<div class="sidebar-container hidden lg:flex lg:flex-shrink-0 fixed left-0 top-0 bottom-0 z-20" x-data="{
    // Переменные для клиентской части (группировка по задачам)
    clientWorkOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && (
            Request::routeIs('appointments.*') ||
            Request::routeIs('clients.*')
        )) 
        ? 'true' : 'false' 
    }},
    appointmentsMenuOpen: {{ 
        (!Str::startsWith(Request::path(), 'panel') && Request::routeIs('appointments.*') && !Request::routeIs('appointments.calendar'))
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
            Request::routeIs('subscription.*') ||
            Request::routeIs('invoices.*')
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
        (Request::routeIs('panel.analytics*') || Request::routeIs('analytics*')) 
        ? 'true' : 'false' 
    }},
    supportOpen: {{ 
        (Request::routeIs('panel.tickets*') || Request::routeIs('panel.ticket-categories*') || Request::routeIs('panel.support*') || Request::routeIs('tickets*')) 
        ? 'true' : 'false' 
    }},
    // Переменные для админ-панели
    panelOperationsOpen: {{ 
        (Request::routeIs('panel.appointments*') || Request::routeIs('panel.clients*') || Request::routeIs('panel.businesses*')) 
        ? 'true' : 'false' 
    }},
    adminAccessOpen: {{ 
        (Request::routeIs('panel.users*') || Request::routeIs('panel.roles*') || Request::routeIs('panel.permissions*') || Request::routeIs('panel.business-roles*')) 
        ? 'true' : 'false' 
    }},
    commsOpen: {{ 
        (Request::routeIs('panel.broadcasts*') || Request::routeIs('panel.notifications*') || Request::routeIs('panel.settings.notifications*')) 
        ? 'true' : 'false' 
    }},
    platformOpen: {{ 
        (Request::routeIs('panel.plans*') || Request::routeIs('panel.subscriptions*') || Request::routeIs('panel.invoices*') || Request::routeIs('panel.settings.payments*') || Request::routeIs('panel.countries*')) 
        ? 'true' : 'false' 
    }},
    panelIntegrationsOpen: {{ 
        Request::routeIs('panel.telegram.management*') 
        ? 'true' : 'false' 
    }},
    contentOpen: {{ 
        (Request::routeIs('panel.services*') || Request::routeIs('panel.locations*') || Request::routeIs('panel.masters*')) 
        ? 'true' : 'false' 
    }},
    collapsed: false
}"
    class="w-64">
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

            @if(!Str::startsWith(Request::path(), 'panel') && isset($userBusinesses))
            @if($userBusinesses->count() > 1)
            <!-- Переключатель бизнеса -->
            <div class="px-4 lg:px-6 mb-4" x-data="{ businessSwitcherOpen: false }">
                <div class="relative">
                    <button @click="businessSwitcherOpen = !businessSwitcherOpen" @click.outside="businessSwitcherOpen = false"
                        class="w-full flex items-center gap-2 rounded-lg px-3 py-2.5 text-left bg-slate-100/80 dark:bg-slate-800/60 hover:bg-slate-200/80 dark:hover:bg-slate-700/60 border border-slate-200/50 dark:border-slate-700/50 transition-colors"
                        :class="collapsed ? 'justify-center px-2' : ''">
                        <i class="fa-solid fa-briefcase text-slate-500 dark:text-slate-400 flex-shrink-0"></i>
                        <span x-show="!collapsed" x-cloak class="sidebar-text text-sm font-medium text-slate-700 dark:text-slate-300 truncate flex-1 min-w-0">{{ $currentBusiness?->name ?? 'Бизнес' }}</span>
                        <i x-show="!collapsed" x-cloak class="fa-solid fa-chevron-down text-xs text-slate-400 flex-shrink-0 transition-transform" :class="{ 'rotate-180': businessSwitcherOpen }"></i>
                    </button>
                    <div x-show="businessSwitcherOpen" x-cloak x-transition
                        class="absolute left-0 right-0 top-full mt-1 z-50 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-lg max-h-60 overflow-y-auto">
                        @foreach($userBusinesses as $b)
                            @if($b->id === $currentBusiness?->id)
                                <div class="px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-500/10">
                                    {{ $b->name }}
                                </div>
                            @else
                                <form method="POST" action="{{ route('settings.business.switch') }}" class="block">
                                    @csrf
                                    <input type="hidden" name="business_id" value="{{ $b->id }}">
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                                        {{ $b->name }}
                                    </button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <!-- Управление бизнесами (0 или 1 бизнес) -->
            <div class="px-4 lg:px-6 mb-4">
                <a href="{{ route('settings.businesses.index') }}"
                   class="w-full flex items-center gap-2 rounded-lg px-3 py-2.5 text-left bg-slate-100/80 dark:bg-slate-800/60 hover:bg-slate-200/80 dark:hover:bg-slate-700/60 border border-slate-200/50 dark:border-slate-700/50 transition-colors"
                   :class="collapsed ? 'justify-center px-2' : ''">
                    <i class="fa-solid fa-briefcase text-slate-500 dark:text-slate-400 flex-shrink-0"></i>
                    <span x-show="!collapsed" x-cloak class="sidebar-text text-sm font-medium text-slate-700 dark:text-slate-300 truncate">{{ $userBusinesses->count() === 0 ? 'Управление бизнесами' : ($currentBusiness?->name ?? 'Управление бизнесами') }}</span>
                </a>
            </div>
            @endif
            @endif

            <!-- Основная навигация -->
            <div class="flex-grow flex flex-col">
                <div class="flex-1 space-y-6" :class="collapsed ? 'px-0' : 'px-4 lg:px-6'">
                    @php
                        // Текущий бизнес и список бизнесов передаются из View Composer (layouts.user)
                        $user = Auth::user();
                        $currentBusinessRole = null;
                        $currentBusinessRoleId = null;
                        $permissionService = null;
                        if ($user && isset($currentBusiness) && $currentBusiness) {
                            $user->load('businesses');
                            $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
                            $currentBusinessRole = $pivot?->pivot->role_id ? \App\Models\BusinessRole::find($pivot->pivot->role_id)?->slug : null;
                            $currentBusinessRoleId = $pivot?->pivot->role_id;
                            if ($currentBusinessRoleId) {
                                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                            }
                        }
                        $userBusinesses = $userBusinesses ?? ($user ? $user->businesses : collect());

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
