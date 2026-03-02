{{-- Меню админ-панели: Главная, быстрый доступ, Каталог, Аналитика, Поддержка, Доступы, Коммуникации, Платформа, Интеграции --}}
@php $isMobile = isset($mobile) && $mobile; @endphp
@if($isMobile)<div class="mobile-menu-nav space-y-6">@endif
                    <!-- Главная -->
                    <div class="space-y-1">
                        <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.index') }}"
                            class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.index')
                                ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                            :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                            :title="collapsed ? 'Главная' : ''"
                            x-data="{ tooltip: false }"
                            @mouseenter="if (collapsed) tooltip = true"
                            @mouseleave="tooltip = false">
                            <div class="flex items-center justify-center flex-shrink-0"
                                :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                <i class="fa-solid fa-house transition-transform duration-200 {{ Request::routeIs('panel.index') ? 'scale-110' : 'group-hover:scale-110' }}"
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
                        $hasPanelOperationsAccess = $user && (
                            $user->can('panel.appointments.view') ||
                            $user->can('panel.clients.view') ||
                            $user->can('panel.businesses.view')
                        );
                    @endphp
                    @if($hasPanelOperationsAccess)
                    <!-- Быстрый доступ: записи, клиенты, бизнесы без группы -->
                    <div class="space-y-1">
                            @can('panel.appointments.view')
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.appointments') }}"
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

                            @can('panel.clients.view')
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.clients') }}"
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

                            @can('panel.businesses.view')
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.businesses') }}"
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
                    </div>
                    @endif

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
                                <i class="fa-solid fa-book-open text-[10px] opacity-60"></i>
                                <span>Каталог</span>
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
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.services') }}"
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
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.locations') }}"
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
                                <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.masters') }}"
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

                    @can('panel.analytics.view')
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
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.analytics') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.analytics') && !Request::routeIs('panel.analytics.*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Обзор' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-chart-pie transition-transform duration-200 {{ Request::routeIs('panel.analytics') && !Request::routeIs('panel.analytics.*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Обзор</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Обзор</div>
                            </a>
                            @can('panel.analytics.financial')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.analytics.financial') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.analytics.financial')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Финансы' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-money-bill-wave transition-transform duration-200 {{ Request::routeIs('panel.analytics.financial') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Финансы</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Финансы</div>
                            </a>
                            @endcan
                            @can('panel.analytics.general')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.analytics.general') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.analytics.general')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Общая' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-chart-line transition-transform duration-200 {{ Request::routeIs('panel.analytics.general') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Общая</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Общая</div>
                            </a>
                            @endcan
                            @can('panel.analytics.subscriptions')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.analytics.subscriptions') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.analytics.subscriptions')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Подписки' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-wallet transition-transform duration-200 {{ Request::routeIs('panel.analytics.subscriptions') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Подписки</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Подписки</div>
                            </a>
                            @endcan
                        </nav>
                    </div>
                    @endcan

                    @php
                        $hasSupportAccess = $user && (
                            $user->can('panel.access') ||
                            $user->can('panel.support.view') ||
                            $user->can('panel.tickets.view') ||
                            $user->can('panel.tickets.categories.manage')
                        );
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
                            @can('panel.access')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.tickets') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ (Request::routeIs('panel.tickets') || Request::routeIs('panel.tickets.*'))
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Тикеты' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-ticket transition-transform duration-200 {{ (Request::routeIs('panel.tickets') || Request::routeIs('panel.tickets.*')) ? 'scale-110' : 'group-hover:scale-110' }}"
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
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.ticket-categories.index') }}"
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
                        </nav>
                    </div>
                    @endif

                    @php
                        $hasAdminAccess = $user && (
                            $user->can('panel.users.view') ||
                            $user->can('panel.roles.view') ||
                            $user->can('panel.permissions.view') ||
                            $user->can('panel.business.roles.manage')
                        );
                    @endphp
                    @if($hasAdminAccess)
                    <div>
                        <button @click="adminAccessOpen = !adminAccessOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-[10px] opacity-60"></i>
                                <span>Доступы и роли</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': adminAccessOpen }"></i>
                        </button>
                        <nav x-show="adminAccessOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @can('panel.users.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.users') }}"
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
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Пользователи</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Пользователи</div>
                            </a>
                            @endcan
                            @can('panel.roles.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.roles') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.roles*') && !Request::routeIs('panel.permissions*') && !Request::routeIs('panel.business-roles*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Роли' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-user-tag transition-transform duration-200 {{ Request::routeIs('panel.roles*') && !Request::routeIs('panel.permissions*') && !Request::routeIs('panel.business-roles*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Роли</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Роли</div>
                            </a>
                            @endcan
                            @can('panel.permissions.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.permissions') }}"
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
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Права</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Права</div>
                            </a>
                            @endcan
                            @can('panel.business.roles.manage')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.business-roles.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.business-roles*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Роли организаций' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-building-user transition-transform duration-200 {{ Request::routeIs('panel.business-roles*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Роли организаций</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Роли организаций</div>
                            </a>
                            @endcan
                        </nav>
                    </div>
                    @endif

                    @php
                        $hasCommsAccess = $user && (
                            $user->can('panel.broadcasts.send') ||
                            $user->can('panel.broadcasts.view') ||
                            $user->can('panel.notifications.view')
                        );
                    @endphp
                    @if($hasCommsAccess)
                    <div>
                        <button @click="commsOpen = !commsOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bullhorn text-[10px] opacity-60"></i>
                                <span>Коммуникации</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="{ 'rotate-180': commsOpen }"></i>
                        </button>
                        <nav x-show="commsOpen || collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="space-y-1 overflow-hidden">
                            @can('panel.broadcasts.send')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.broadcasts.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.broadcasts*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Рассылки' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-paper-plane transition-transform duration-200 {{ Request::routeIs('panel.broadcasts*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Рассылки</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Рассылки</div>
                            </a>
                            @endcan
                            @can('panel.notifications.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.notifications.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.notifications*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Уведомления' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-bell transition-transform duration-200 {{ Request::routeIs('panel.notifications*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Уведомления</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Уведомления</div>
                            </a>
                            @endcan
                        </nav>
                    </div>
                    @endif

                    @php
                        $hasPlatformAccess = $user && (
                            $user->can('panel.plans.view') ||
                            $user->can('panel.payments.view') ||
                            $user->can('panel.subscriptions.view')
                        );
                    @endphp
                    @if($hasPlatformAccess)
                    <div>
                        <button @click="platformOpen = !platformOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-[10px] opacity-60"></i>
                                <span>Платформа</span>
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
                            @can('panel.plans.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.plans.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.plans*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Тарифы' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-layer-group transition-transform duration-200 {{ Request::routeIs('panel.plans*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Тарифы</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Тарифы</div>
                            </a>
                            @endcan
                            @can('panel.subscriptions.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.subscriptions.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.subscriptions*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Подписки' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-repeat transition-transform duration-200 {{ Request::routeIs('panel.subscriptions*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Подписки</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Подписки</div>
                            </a>
                            @endcan
                            @can('panel.payments.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.invoices') }}"
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
                                    <i class="fa-solid fa-receipt transition-transform duration-200 {{ Request::routeIs('panel.invoices*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Платежи</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Платежи</div>
                            </a>
                            @endcan
                            @can('panel.payments.settings')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.settings.payments') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.settings.payments*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Настройки платежей' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-gear transition-transform duration-200 {{ Request::routeIs('panel.settings.payments*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Настройки платежей</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Настройки платежей</div>
                            </a>
                            @endcan
                            @can('panel.countries.view')
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.countries.index') }}"
                                class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ Request::routeIs('panel.countries*')
                                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
                                :class="collapsed ? 'justify-center mx-2' : 'px-3'"
                                :title="collapsed ? 'Страны' : ''"
                                x-data="{ tooltip: false }"
                                @mouseenter="if (collapsed) tooltip = true"
                                @mouseleave="tooltip = false">
                                <div class="flex items-center justify-center flex-shrink-0"
                                    :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
                                    <i class="fa-solid fa-globe transition-transform duration-200 {{ Request::routeIs('panel.countries*') ? 'scale-110' : 'group-hover:scale-110' }}"
                                       :class="collapsed ? 'text-lg' : 'text-base'"></i>
                                </div>
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Страны</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Страны</div>
                            </a>
                            @endcan
                        </nav>
                    </div>
                    @endif

                    @can('panel.telegram.manage')
                    <div>
                        <button @click="panelIntegrationsOpen = !panelIntegrationsOpen" x-show="!collapsed" x-cloak
                            class="sidebar-section-title w-full flex items-center justify-between px-3 mb-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-300 transition-all duration-200 rounded-lg py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-puzzle-piece text-[10px] opacity-60"></i>
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
                            <a @if($isMobile) @click="closeMenu()" @endif href="{{ route('panel.telegram.management') }}"
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
                                <span x-show="!collapsed" x-cloak class="sidebar-text whitespace-nowrap font-medium">Telegram Bot</span>
                                <div x-show="tooltip && collapsed" x-transition
                                     class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">Telegram Bot</div>
                            </a>
                        </nav>
                    </div>
                    @endcan
@if($isMobile)</div>@endif
