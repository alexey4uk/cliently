{{-- Клиентское меню: Главная, быстрый доступ, Управление, Аналитика, Поддержка, Подписка --}}
@php $isMobile = isset($mobile) && $mobile; @endphp
@if($isMobile)<div class="mobile-menu-nav space-y-6">@endif
                    <!-- Главная -->
                    <div class="space-y-1">
                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('dashboard') }}"
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
                                <i class="fa-solid fa-house transition-transform duration-200 {{ Request::routeIs('dashboard') ? 'scale-110' : 'group-hover:scale-110' }}"
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
                    </div>

                    @php
                        $hasDailyAccess = $hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.clients.view');
                    @endphp
                    @if($hasDailyAccess)
                    <!-- Быстрый доступ: записи (выпадающее), календарь, клиенты без группы -->
                    <div class="space-y-1">
                            @if($hasBusinessPermission('client.appointments.view'))
                                <!-- Записи: выпадающее меню -->
                                <div>
                                    <button @click="appointmentsMenuOpen = !appointmentsMenuOpen" x-show="!collapsed" x-cloak
                                        class="sidebar-section-title w-full flex items-center justify-between py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100"
                                        :class="collapsed ? 'justify-center mx-2' : 'px-3'">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="flex items-center justify-center flex-shrink-0 w-5 h-5">
                                                <i class="fa-solid fa-calendar-check text-base"></i>
                                            </div>
                                            <span class="sidebar-text whitespace-nowrap font-medium">Записи</span>
                                        </div>
                                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300 flex-shrink-0"
                                            :class="{ 'rotate-180': appointmentsMenuOpen }"></i>
                                    </button>
                                    <nav x-show="appointmentsMenuOpen || collapsed"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-1"
                                         class="space-y-1 overflow-hidden mt-0.5">
                                        @if($hasBusinessPermission('client.appointments.create'))
                                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.create') }}"
                                                class="group flex items-center py-2 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('appointments.create')
                                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                                :class="collapsed ? 'justify-center mx-2' : 'pl-8 pr-3'"
                                                :title="collapsed ? 'Создать' : ''"
                                                x-data="{ tooltip: false }"
                                                @mouseenter="if (collapsed) tooltip = true"
                                                @mouseleave="tooltip = false">
                                                <div class="flex items-center justify-center flex-shrink-0" :class="collapsed ? 'mx-auto w-6 h-6' : 'w-4 h-4 mr-2.5'">
                                                    <i class="fa-solid fa-plus text-xs"></i>
                                                </div>
                                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap">Создать</span>
                                                <div x-show="tooltip && collapsed" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Создать</div>
                                            </a>
                                        @endif
                                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.index') }}"
                                            class="group flex items-center py-2 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('appointments.index') && !request('status')
                                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                            :class="collapsed ? 'justify-center mx-2' : 'pl-8 pr-3'"
                                            :title="collapsed ? 'Все записи' : ''"
                                            x-data="{ tooltip: false }"
                                            @mouseenter="if (collapsed) tooltip = true"
                                            @mouseleave="tooltip = false">
                                            <div class="flex items-center justify-center flex-shrink-0" :class="collapsed ? 'mx-auto w-6 h-6' : 'w-4 h-4 mr-2.5'">
                                                <i class="fa-solid fa-list text-xs"></i>
                                            </div>
                                            <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap">Все записи</span>
                                            <div x-show="tooltip && collapsed" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Все записи</div>
                                        </a>
                                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.index', ['status' => 'pending']) }}"
                                            class="group flex items-center py-2 text-sm font-medium rounded-lg transition-all duration-200 relative {{ request('status') === 'pending'
                                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                            :class="collapsed ? 'justify-center mx-2' : 'pl-8 pr-3'"
                                            :title="collapsed ? 'Ожидающие' : ''"
                                            x-data="{ tooltip: false }"
                                            @mouseenter="if (collapsed) tooltip = true"
                                            @mouseleave="tooltip = false">
                                            <div class="flex items-center justify-center flex-shrink-0" :class="collapsed ? 'mx-auto w-6 h-6' : 'w-4 h-4 mr-2.5'">
                                                <i class="fa-solid fa-clock text-xs text-amber-500"></i>
                                            </div>
                                            <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap">Ожидающие</span>
                                            <div x-show="tooltip && collapsed" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Ожидающие</div>
                                        </a>
                                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.index', ['status' => 'confirmed']) }}"
                                            class="group flex items-center py-2 text-sm font-medium rounded-lg transition-all duration-200 relative {{ request('status') === 'confirmed'
                                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                            :class="collapsed ? 'justify-center mx-2' : 'pl-8 pr-3'"
                                            :title="collapsed ? 'Подтвержденные' : ''"
                                            x-data="{ tooltip: false }"
                                            @mouseenter="if (collapsed) tooltip = true"
                                            @mouseleave="tooltip = false">
                                            <div class="flex items-center justify-center flex-shrink-0" :class="collapsed ? 'mx-auto w-6 h-6' : 'w-4 h-4 mr-2.5'">
                                                <i class="fa-solid fa-circle-check text-xs text-blue-500"></i>
                                            </div>
                                            <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap">Подтвержденные</span>
                                            <div x-show="tooltip && collapsed" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Подтвержденные</div>
                                        </a>
                                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.index', ['status' => 'completed']) }}"
                                            class="group flex items-center py-2 text-sm font-medium rounded-lg transition-all duration-200 relative {{ request('status') === 'completed'
                                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                            :class="collapsed ? 'justify-center mx-2' : 'pl-8 pr-3'"
                                            :title="collapsed ? 'Завершенные' : ''"
                                            x-data="{ tooltip: false }"
                                            @mouseenter="if (collapsed) tooltip = true"
                                            @mouseleave="tooltip = false">
                                            <div class="flex items-center justify-center flex-shrink-0" :class="collapsed ? 'mx-auto w-6 h-6' : 'w-4 h-4 mr-2.5'">
                                                <i class="fa-solid fa-check text-xs text-emerald-500"></i>
                                            </div>
                                            <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap">Завершенные</span>
                                            <div x-show="tooltip && collapsed" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Завершенные</div>
                                        </a>
                                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.index', ['status' => 'cancelled']) }}"
                                            class="group flex items-center py-2 text-sm font-medium rounded-lg transition-all duration-200 relative {{ request('status') === 'cancelled'
                                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                            :class="collapsed ? 'justify-center mx-2' : 'pl-8 pr-3'"
                                            :title="collapsed ? 'Отмененные' : ''"
                                            x-data="{ tooltip: false }"
                                            @mouseenter="if (collapsed) tooltip = true"
                                            @mouseleave="tooltip = false">
                                            <div class="flex items-center justify-center flex-shrink-0" :class="collapsed ? 'mx-auto w-6 h-6' : 'w-4 h-4 mr-2.5'">
                                                <i class="fa-solid fa-xmark text-xs text-rose-500"></i>
                                            </div>
                                            <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap">Отмененные</span>
                                            <div x-show="tooltip && collapsed" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Отмененные</div>
                                        </a>
                                    </nav>
                                </div>

                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('appointments.calendar') }}"
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

                            @if($hasBusinessPermission('client.clients.view'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('clients.index') }}"
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
                    </div>
                    @endif

                    <!-- Уведомления (доступны всем авторизованным) -->
                    <div class="space-y-1">
                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('notifications.index') }}"
                            class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('notifications.*')
                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                            :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                            :title="collapsed ? 'Уведомления' : ''"
                            x-data="{ tooltip: false }"
                            @mouseenter="if (collapsed) tooltip = true"
                            @mouseleave="tooltip = false">
                            <div class="flex items-center justify-center flex-shrink-0"
                                :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                <i class="fa-solid fa-bell transition-transform duration-200 {{ Request::routeIs('notifications.*') ? 'scale-110' : 'group-hover:scale-110' }}"
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
                    </div>

                    @php
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
                        $hasResourcesAccess = $hasBusinessPermission('client.locations.view') ||
                                             $hasBusinessPermission('client.services.view') ||
                                             $hasBusinessPermission('client.masters.view');
                        $hasBusinessSettingsAccess = $hasBusinessPermission('client.businesses.update') || $hasResourcesAccess ||
                                         $hasBusinessPermission('client.business.users.view') ||
                                         $hasBusinessPermission('client.business.roles.manage') ||
                                         $hasBusinessPermission('client.online_booking.manage') ||
                                         ($hasBusinessPermission('client.telegram.manage') && $hasTelegramAccess);
                    @endphp

                    @if($hasBusinessSettingsAccess)
                    <!-- Управление: бизнес, онлайн-запись, услуги, мастера, локации, пользователи, роли, Telegram -->
                    <div>
                        <button @click="businessSettingsOpen = !businessSettingsOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-gears text-[10px] opacity-60"></i>
                                <span>Управление</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': businessSettingsOpen }"></i>
                        </button>
                        <nav x-show="businessSettingsOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.index') }}"
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
                                @if($hasBusinessPermission('client.online_booking.manage'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.online-booking') }}"
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
                            @if($hasBusinessPermission('client.services.view'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('services.index') }}"
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
                            @if($hasBusinessPermission('client.masters.view'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.masters') }}"
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
                            @if($hasBusinessPermission('client.locations.view'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.locations') }}"
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
                            @php
                                $hasUsersPermission = false;
                                if ($currentBusinessRoleId) {
                                    $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                                    $hasUsersPermission = $permissionService->hasPermission($currentBusinessRoleId, 'client.business.users.view');
                                }
                            @endphp
                            @if($hasUsersPermission)
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.users.index') }}"
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
                            @php
                                $hasRolesPermission = false;
                                if ($currentBusinessRoleId) {
                                    $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                                    $hasRolesPermission = $permissionService->hasPermission($currentBusinessRoleId, 'client.business.roles.manage');
                                }
                            @endphp
                            @if($hasRolesPermission)
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.roles.index') }}"
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
                            @if($hasBusinessPermission('client.telegram.manage') && $hasTelegramAccess)
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('settings.telegram') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('settings.telegram*')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Telegram' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-brands fa-telegram transition-transform duration-200 {{ Request::routeIs('settings.telegram*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                           :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Telegram</span>
                                    <div x-show="tooltip && collapsed"
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Telegram
                                    </div>
                                </a>
                            @endif
                        </nav>
                    </div>
                    @endif

                    @php
                        $hasAnalyticsAccess = false;
                        if ($hasBusinessPermission('client.analytics.view') && $currentBusiness) {
                            $accessService = app(\App\Services\SubscriptionAccessService::class);
                            $hasAnalyticsAccess = $accessService->hasAccess($currentBusiness, 'analytics_enabled', 'client.analytics.view');
                        }
                    @endphp
                    @if($hasBusinessPermission('client.analytics.view') && $hasAnalyticsAccess)
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
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('analytics.index') }}"
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
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Обзор</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Обзор</div>
                            </a>
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('analytics.financial') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('analytics.financial')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Финансы' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-money-bill-wave transition-transform duration-200 {{ Request::routeIs('analytics.financial') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Финансы</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Финансы</div>
                            </a>
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('analytics.general') }}"
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
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Общая</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Общая</div>
                            </a>
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('analytics.clients') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('analytics.clients')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Клиенты' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-users transition-transform duration-200 {{ Request::routeIs('analytics.clients') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Клиенты</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Клиенты</div>
                            </a>
                        </nav>
                    </div>
                    @endif

                    @php
                        $hasSupportAccess = $hasBusinessPermission('client.tickets.create') || $hasBusinessPermission('client.tickets.view');
                    @endphp
                    @if($hasSupportAccess)
                    <div>
                        <button @click="supportOpen = !supportOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-headset text-[10px] opacity-60"></i>
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
                            @if($hasBusinessPermission('client.tickets.create'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('tickets.create') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('tickets.create')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Написать' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-plus transition-transform duration-200 {{ Request::routeIs('tickets.create') ? 'scale-110' : 'group-hover:scale-110' }}"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Написать</span>
                                    <div x-show="tooltip && collapsed"
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Написать
                                    </div>
                                </a>
                            @endif
                            @if($hasBusinessPermission('client.tickets.view'))
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('tickets.index') }}"
                                    class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('tickets.index') || Request::routeIs('tickets.show') || Request::routeIs('tickets.edit')
                                        ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                    :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                    :title="collapsed ? 'Мои запросы' : ''"
                                    x-data="{ tooltip: false }"
                                    @mouseenter="if (collapsed) tooltip = true"
                                    @mouseleave="tooltip = false">
                                    <div class="flex items-center justify-center flex-shrink-0"
                                        :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                        <i class="fa-solid fa-ticket transition-transform duration-200 {{ Request::routeIs('tickets.index') || Request::routeIs('tickets.show') || Request::routeIs('tickets.edit') ? 'scale-110' : 'group-hover:scale-110' }}"
                                            :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                    </div>
                                    <span x-show="!collapsed" x-cloak
                                        class="sidebar-text whitespace-nowrap font-medium">Мои запросы</span>
                                    <div x-show="tooltip && collapsed"
                                         x-transition
                                         class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                        Мои запросы
                                    </div>
                                </a>
                            @endif
                        </nav>
                    </div>
                    @endif

                    @if($hasBusinessPermission('client.subscription.view'))
                    <div>
                        <button @click="subscriptionOpen = !subscriptionOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-[10px] opacity-60"></i>
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
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('subscription.current') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('subscription.current')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Текущая' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-circle-check transition-transform duration-200 {{ Request::routeIs('subscription.current') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak
                                    class="sidebar-text whitespace-nowrap font-medium">Текущая</span>
                                <div x-show="tooltip && collapsed"
                                     x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                    Текущая
                                </div>
                            </a>
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('subscription.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('subscription.index') || Request::routeIs('subscription.show')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Тарифы' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-credit-card transition-transform duration-200 {{ (Request::routeIs('subscription.index') || Request::routeIs('subscription.show')) ? 'scale-110' : 'group-hover:scale-110' }}"
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
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('invoices.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('invoices.*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Мои счета' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-file-invoice transition-transform duration-200 {{ Request::routeIs('invoices.*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak
                                    class="sidebar-text whitespace-nowrap font-medium">Мои счета</span>
                                <div x-show="tooltip && collapsed"
                                     x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                    Мои счета
                                </div>
                            </a>
                        </nav>
                    </div>
                    @endif
@if($isMobile)</div>@endif
