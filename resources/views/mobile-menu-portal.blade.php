<!-- Overlay и меню выносятся за пределы header для корректного отображения -->
<div x-data="{ 
    open: false,
    resourcesOpen: {{ Request::routeIs('services.*') || Request::routeIs('settings.masters*') || Request::routeIs('settings.locations*') ? 'true' : 'false' }},
    settingsOpen: {{ Request::routeIs('settings.index') || Request::routeIs('settings.online-booking*') || Request::routeIs('settings.users*') || Request::routeIs('settings.roles*') || Request::routeIs('settings.telegram*') || Request::routeIs('settings.notifications.*') || Request::routeIs('panel.settings.notifications.*') ? 'true' : 'false' }},
    subscriptionOpen: {{ Request::routeIs('subscription.*') ? 'true' : 'false' }},
    analyticsOpen: {{ Request::routeIs('analytics.*') ? 'true' : 'false' }},
    supportOpen: {{ Request::routeIs('tickets.*') ? 'true' : 'false' }},
    init() {
        // Инициализируем переменные для корректной работы Alpine
        this.resourcesOpen = {{ Request::routeIs('services.*') || Request::routeIs('settings.masters*') || Request::routeIs('settings.locations*') ? 'true' : 'false' }};
        this.settingsOpen = {{ Request::routeIs('settings.index') || Request::routeIs('settings.online-booking*') || Request::routeIs('settings.users*') || Request::routeIs('settings.roles*') || Request::routeIs('settings.telegram*') || Request::routeIs('settings.notifications.*') || Request::routeIs('panel.settings.notifications.*') ? 'true' : 'false' }};
        this.subscriptionOpen = {{ Request::routeIs('subscription.*') ? 'true' : 'false' }};
        this.analyticsOpen = {{ Request::routeIs('analytics.*') ? 'true' : 'false' }};
        this.supportOpen = {{ Request::routeIs('tickets.*') ? 'true' : 'false' }};
        
        // Слушаем события от кнопки меню
        const self = this;
        document.addEventListener('mobile-menu-toggle', function(e) {
            self.open = e.detail.open;
            if (self.open) {
                document.body.style.overflow = 'hidden';
                // Предотвращаем сдвиг контента при открытии меню
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollbarWidth > 0) {
                    document.body.style.paddingRight = scrollbarWidth + 'px';
                }
            } else {
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        });
    },
    closeMenu() {
        this.open = false;
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}" 
@keydown.escape.window="closeMenu()"
class="lg:hidden fixed inset-0 pointer-events-none z-[9999]"
style="width: 0; height: 0;">
    <!-- Overlay -->
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0" 
         @click="closeMenu()" 
         style="display: none; position: fixed; inset: 0; z-index: 9998; pointer-events: auto;"
         class="bg-black/50 backdrop-blur-sm">
    </div>

    <!-- Боковое меню -->
    <aside x-show="open" 
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full" 
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform" 
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full" 
           @click.away="closeMenu()"
           style="display: none; position: fixed; left: 0; top: 0; bottom: 0; width: 18rem; max-width: 85vw; z-index: 9999; pointer-events: auto;"
           class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm shadow-xl overflow-y-auto border-r border-slate-200/50 dark:border-slate-800/50">
        <div class="flex flex-col h-full">
            <!-- Заголовок с кнопкой закрытия -->
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-slate-200/50 dark:border-slate-800/50 flex-shrink-0">
                <a href="{{ route('dashboard') }}" @click="closeMenu()"
                    class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <x-logo size="sm" />
                    <span
                        class="text-xl font-bold text-slate-900 dark:text-white tracking-tight uppercase font-display">CLIENTLY</span>
                </a>
                <button @click="closeMenu()"
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 group"
                    aria-label="Закрыть меню"
                    type="button">
                    <i class="fa-solid fa-times text-base group-hover:scale-110 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Навигация -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                @php
                    // Получаем бизнес и роль для проверки прав доступа
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
                @endphp

                <!-- Рабочий процесс -->
                <div>
                    <h3
                        class="px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-[10px] opacity-50"></i>
                        <span>Рабочий процесс</span>
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('dashboard')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Главная</span>
                        </a>

                        @if($hasBusinessPermission('client.appointments.view'))
                        <a href="{{ route('appointments.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.*') && !Request::routeIs('appointments.calendar')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-calendar-check text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Записи</span>
                        </a>

                        <a href="{{ route('appointments.calendar') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('appointments.calendar')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-calendar text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Календарь</span>
                        </a>
                        @endif

                        @if($hasBusinessPermission('client.clients.view'))
                        <a href="{{ route('clients.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('clients.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-users text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Клиенты</span>
                        </a>
                        @endif

                        <!-- Уведомления (доступны всем авторизованным пользователям) -->
                        <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.notifications.index') : route('notifications.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ (Request::routeIs('notifications.*') || Request::routeIs('panel.notifications.*'))
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-bell text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Уведомления</span>
                            @php
                                $unreadCount = \App\Services\NotificationService::getUnreadCount(Auth::id());
                            @endphp
                            @if($unreadCount > 0)
                                <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-rose-500 text-white rounded-full">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Ресурсы -->
                @php
                    $hasResourcesAccess = $hasBusinessPermission('client.locations.view') ||
                                         $hasBusinessPermission('client.services.view') ||
                                         $hasBusinessPermission('client.masters.view');
                @endphp
                @if($hasResourcesAccess)
                <div>
                    <button @click="resourcesOpen = !resourcesOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-folder-open text-[10px] opacity-60"></i>
                            <span>Ресурсы</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': resourcesOpen }"></i>
                    </button>
                    <div x-show="resourcesOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        @if($hasBusinessPermission('client.services.view'))
                        <a href="{{ route('services.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('services.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-scissors text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Услуги</span>
                        </a>
                        @endif

                        @if($hasBusinessPermission('client.masters.view'))
                        <a href="{{ route('settings.masters') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.masters*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-user-tie text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Мастера</span>
                        </a>
                        @endif

                        @if($hasBusinessPermission('client.locations.view'))
                        <a href="{{ route('settings.locations') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.locations*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Локации</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Настройки -->
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
                    $hasUsersPermission = false;
                    $hasRolesPermission = false;
                    if ($currentBusinessRoleId) {
                        $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                        $hasUsersPermission = $permissionService->hasPermission($currentBusinessRoleId, 'client.business.users.view');
                        $hasRolesPermission = $permissionService->hasPermission($currentBusinessRoleId, 'client.business.roles.manage');
                    }
                    $hasSettingsAccess = $hasBusinessPermission('client.businesses.update') ||
                                       $hasUsersPermission ||
                                       $hasRolesPermission ||
                                       ($hasBusinessPermission('client.telegram.manage') && $hasTelegramAccess);
                @endphp
                @if($hasSettingsAccess)
                <div>
                    <button @click="settingsOpen = !settingsOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-gear text-[10px] opacity-60"></i>
                            <span>Настройки</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': settingsOpen }"></i>
                    </button>
                    <div x-show="settingsOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        @if($hasBusinessPermission('client.businesses.update'))
                        <a href="{{ route('settings.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.index')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-building text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Бизнес</span>
                        </a>
                        @endif

                        @if($hasBusinessPermission('client.businesses.update'))
                        <a href="{{ route('settings.online-booking') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.online-booking*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-link text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Онлайн-запись</span>
                        </a>
                        @endif

                        @if($hasUsersPermission)
                        <a href="{{ route('settings.users.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.users*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-users-gear text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Пользователи</span>
                        </a>
                        @endif

                        @if($hasRolesPermission)
                        <a href="{{ route('settings.roles.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.roles*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-shield-halved text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Роли</span>
                        </a>
                        @endif

                        @if($hasBusinessPermission('client.telegram.manage') && $hasTelegramAccess)
                        <a href="{{ route('settings.telegram') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('settings.telegram*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-brands fa-telegram text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Telegram Bot</span>
                        </a>
                        @endif

                        <!-- Настройки уведомлений (доступны всем авторизованным пользователям) -->
                        <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.settings.notifications.index') : route('settings.notifications.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ (Request::routeIs('settings.notifications.*') || Request::routeIs('panel.settings.notifications.*'))
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-bell-slash text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Уведомления</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Настройки уведомлений (отдельный раздел, если нет доступа к другим настройкам) -->
                @if(!$hasSettingsAccess)
                <div>
                    <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.settings.notifications.index') : route('settings.notifications.index') }}" @click="closeMenu()"
                        class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ (Request::routeIs('settings.notifications.*') || Request::routeIs('panel.settings.notifications.*'))
                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <i class="fa-solid fa-bell-slash text-base"></i>
                        </div>
                        <span class="ml-3 whitespace-nowrap">Настройки уведомлений</span>
                    </a>
                </div>
                @endif

                <!-- Аналитика -->
                @if(!Str::startsWith(Request::path(), 'panel'))
                @php
                    // Для клиентской части проверяем подписку владельца бизнеса
                    $hasAnalyticsAccess = false;
                    if ($currentBusiness) {
                        $accessService = app(\App\Services\SubscriptionAccessService::class);
                        $hasAnalyticsAccess = $accessService->hasAccess($currentBusiness, 'analytics_enabled', 'client.analytics.view');
                    }
                @endphp
                @if($hasBusinessPermission('client.analytics.view') && $hasAnalyticsAccess)
                <div>
                    <button @click="analyticsOpen = !analyticsOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-chart-bar text-[10px] opacity-60"></i>
                            <span>Аналитика</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': analyticsOpen }"></i>
                    </button>
                    <div x-show="analyticsOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        <a href="{{ route('analytics.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('analytics.index')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-chart-bar text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Обзор</span>
                        </a>
                        <a href="{{ route('analytics.financial') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('analytics.financial')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-money-bill-wave text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Финансы</span>
                        </a>
                        <a href="{{ route('analytics.general') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('analytics.general')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Общая</span>
                        </a>
                    </div>
                </div>
                @endif
                @endif

                <!-- Поддержка -->
                @php
                    $hasSupportAccess = $hasBusinessPermission('client.tickets.create') ||
                                       $hasBusinessPermission('client.tickets.view');
                @endphp
                @if($hasSupportAccess)
                <div>
                    <button @click="supportOpen = !supportOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-ticket text-[10px] opacity-60"></i>
                            <span>Поддержка</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': supportOpen }"></i>
                    </button>
                    <div x-show="supportOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        @if($hasBusinessPermission('client.tickets.create'))
                        <a href="{{ route('tickets.create') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('tickets.create')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-plus text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Создать тикет</span>
                        </a>
                        @endif

                        @if($hasBusinessPermission('client.tickets.view'))
                        <a href="{{ route('tickets.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('tickets.index') || Request::routeIs('tickets.show') || Request::routeIs('tickets.edit')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-ticket text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Мои тикеты</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Подписка -->
                @if($hasBusinessPermission('client.subscription.view'))
                <div>
                    <button @click="subscriptionOpen = !subscriptionOpen"
                        class="w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-credit-card text-[10px] opacity-60"></i>
                            <span>Подписка</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': subscriptionOpen }"></i>
                    </button>
                    <div x-show="subscriptionOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1 overflow-hidden">
                        <a href="{{ route('subscription.index') }}" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ Request::routeIs('subscription.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-credit-card text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Тарифы</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- Нижняя часть: Профиль -->
            <div class="border-t border-slate-200/50 dark:border-slate-800/50 pt-4 mt-4 flex-shrink-0">
                <div class="px-3">
                    @auth
                        <!-- Информация о пользователе (кликабельная, ведет на профиль) -->
                        <a href="{{ route('profile.edit') }}" @click="closeMenu()"
                            class="block mb-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm overflow-hidden flex-shrink-0 ring-2 ring-indigo-500/30">
                                    @if (Auth::user()->getAvatarUrl())
                                        <img src="{{ Auth::user()->getAvatarUrl() }}"
                                            alt="{{ Auth::user()->name }}" class="w-full h-full object-cover"
                                            referrerpolicy="no-referrer">
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

                        <!-- Подписка -->
                        @if($hasBusinessPermission('client.subscription.view'))
                        @php
                            $user = Auth::user();
                            $subscription = $user ? $user->activeSubscription() : null;
                            $plan = $subscription && $subscription->plan ? $subscription->plan : null;
                        @endphp
                        @if($subscription && $plan)
                        <a href="{{ route('subscription.current') }}" @click="closeMenu()"
                            class="block mb-3 px-3 py-2.5 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100/50 dark:from-indigo-500/10 dark:to-indigo-500/5 border border-indigo-200 dark:border-indigo-500/20 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-crown text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                <span class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $plan->name }}</span>
                            </div>
                        </a>
                        @endif
                        @endif
                    @endauth

                    <!-- Панель управления (только для админов) -->
                    @if(!Str::startsWith(Request::path(), 'panel') && Auth::user()->can('panel.access'))
                        <a href="{{ route('panel.index') }}" target="_blank" rel="noopener noreferrer" @click="closeMenu()"
                            class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 bg-amber-500 hover:bg-amber-600 text-white mb-2">
                            <div class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <i class="fa-solid fa-shield-halved text-base"></i>
                            </div>
                            <span class="ml-3 whitespace-nowrap">Панель управления</span>
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
