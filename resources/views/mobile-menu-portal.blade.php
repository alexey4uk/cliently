<!-- Мобильное меню: та же структура и пункты, что и в sidebar (client/panel) -->
<div x-data="{
    open: false,
    collapsed: false,
    clientWorkOpen: {{ (!Str::startsWith(Request::path(), 'panel') && (Request::routeIs('appointments.*') || Request::routeIs('clients.*'))) ? 'true' : 'false' }},
    appointmentsMenuOpen: {{ (!Str::startsWith(Request::path(), 'panel') && Request::routeIs('appointments.*') && !Request::routeIs('appointments.calendar')) ? 'true' : 'false' }},
    clientCatalogOpen: {{ (!Str::startsWith(Request::path(), 'panel') && (Request::routeIs('services.*') || Request::routeIs('settings.masters*') || Request::routeIs('settings.locations*'))) ? 'true' : 'false' }},
    clientOnlineOpen: {{ (!Str::startsWith(Request::path(), 'panel') && Request::routeIs('settings.online-booking*')) ? 'true' : 'false' }},
    clientTeamOpen: {{ (!Str::startsWith(Request::path(), 'panel') && (Request::routeIs('settings.users*') || Request::routeIs('settings.roles*'))) ? 'true' : 'false' }},
    clientIntegrationsOpen: {{ (!Str::startsWith(Request::path(), 'panel') && Request::routeIs('settings.telegram*')) ? 'true' : 'false' }},
    subscriptionOpen: {{ (!Str::startsWith(Request::path(), 'panel') && (Request::routeIs('subscription.*') || Request::routeIs('invoices.*'))) ? 'true' : 'false' }},
    businessSettingsOpen: {{ (!Str::startsWith(Request::path(), 'panel') && (Request::routeIs('settings.index') || Request::routeIs('settings.online-booking*') || Request::routeIs('services.*') || Request::routeIs('settings.masters*') || Request::routeIs('settings.locations*') || Request::routeIs('settings.users*') || Request::routeIs('settings.roles*') || Request::routeIs('settings.telegram*'))) ? 'true' : 'false' }},
    analyticsOpen: {{ (Request::routeIs('panel.analytics*') || Request::routeIs('analytics*')) ? 'true' : 'false' }},
    supportOpen: {{ (Request::routeIs('panel.tickets*') || Request::routeIs('panel.ticket-categories*') || Request::routeIs('panel.support*') || Request::routeIs('tickets*')) ? 'true' : 'false' }},
    panelOperationsOpen: {{ (Request::routeIs('panel.appointments*') || Request::routeIs('panel.clients*') || Request::routeIs('panel.businesses*')) ? 'true' : 'false' }},
    adminAccessOpen: {{ (Request::routeIs('panel.users*') || Request::routeIs('panel.roles*') || Request::routeIs('panel.permissions*') || Request::routeIs('panel.business-roles*')) ? 'true' : 'false' }},
    commsOpen: {{ (Request::routeIs('panel.broadcasts*') || Request::routeIs('panel.notifications*') || Request::routeIs('panel.settings.notifications*')) ? 'true' : 'false' }},
    platformOpen: {{ (Request::routeIs('panel.plans*') || Request::routeIs('panel.subscriptions*') || Request::routeIs('panel.invoices*') || Request::routeIs('panel.settings.payments*') || Request::routeIs('panel.countries*')) ? 'true' : 'false' }},
    panelIntegrationsOpen: {{ Request::routeIs('panel.telegram.management*') ? 'true' : 'false' }},
    contentOpen: {{ (Request::routeIs('panel.services*') || Request::routeIs('panel.locations*') || Request::routeIs('panel.masters*')) ? 'true' : 'false' }},
    closeMenu() {
        this.open = false;
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    },
    init() {
        const self = this;
        document.addEventListener('mobile-menu-toggle', function(e) {
            self.open = e.detail.open;
            if (self.open) {
                document.body.style.overflow = 'hidden';
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollbarWidth > 0) {
                    document.body.style.paddingRight = scrollbarWidth + 'px';
                }
            } else {
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        });
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
           class="bg-white dark:bg-slate-900 shadow-xl overflow-y-auto border-r border-slate-200 dark:border-slate-800 flex flex-col">
        <!-- Заголовок с логотипом и кнопкой закрытия -->
        <div class="flex items-center justify-between px-4 lg:px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex-shrink-0">
            <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.index') : route('dashboard') }}"
               @click="closeMenu()"
               class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <x-logo size="sm" />
                <span class="text-xl font-bold text-slate-900 dark:text-white tracking-tight uppercase font-display">CLIENTLY</span>
            </a>
            <button @click="closeMenu()"
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200"
                    aria-label="Закрыть меню"
                    type="button">
                <i class="fa-solid fa-times text-base"></i>
            </button>
        </div>

        <!-- Навигация: те же блоки, что и в sidebar -->
        <nav class="flex-1 overflow-y-auto px-4 lg:px-6 py-4">
            @php
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
                $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
                    if (!$currentBusinessRoleId || !$permissionService) {
                        return false;
                    }
                    return $permissionService->hasPermission($currentBusinessRoleId, $permission);
                };
                $hasAnyPermission = function($permissions) use ($hasBusinessPermission, $user) {
                    foreach ($permissions as $permission) {
                        if (str_starts_with($permission, 'client.') || str_starts_with($permission, 'panel.')) {
                            if (str_starts_with($permission, 'client.')) {
                                if ($hasBusinessPermission($permission)) return true;
                            } else {
                                if ($user && $user->can($permission)) return true;
                            }
                        } else {
                            if ($user && $user->can($permission)) return true;
                        }
                    }
                    return false;
                };
            @endphp

            @if(Str::startsWith(Request::path(), 'panel'))
                @include('sidebar.panel', ['mobile' => true])
            @else
                @include('sidebar.client', ['mobile' => true])
            @endif
        </nav>

        <!-- Нижняя часть: профиль, подписка, панель, выход -->
        <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-800 pt-4 pb-4 px-4 lg:px-6">
            @auth
                <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.profile.edit') : route('profile.edit') }}"
                   @click="closeMenu()"
                   class="block mb-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm overflow-hidden flex-shrink-0">
                            @if (Auth::user()->getAvatarUrl())
                                <img src="{{ Auth::user()->getAvatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                            @else
                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                    </div>
                </a>

                @if(!Str::startsWith(Request::path(), 'panel') && $hasBusinessPermission('client.subscription.view'))
                    @php
                        $subscription = Auth::user()->activeSubscription();
                        $plan = $subscription ? $subscription->getEffectivePlan() : null;
                    @endphp
                    @if($subscription && $plan)
                        <a href="{{ route('subscription.current') }}" @click="closeMenu()"
                           class="block mb-3 px-3 py-2.5 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100/50 dark:from-indigo-500/10 dark:to-indigo-500/5 border border-indigo-200 dark:border-indigo-500/20 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                <span class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $plan->name }}</span>
                            </div>
                        </a>
                    @endif
                    <a href="{{ route('invoices.index') }}" @click="closeMenu()"
                       class="block mb-3 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all duration-200">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-slate-600 dark:text-slate-400 text-sm"></i>
                            <span class="text-xs font-semibold text-slate-900 dark:text-white truncate">Мои счета</span>
                        </div>
                    </a>
                @endif
            @endauth

            @if(!Str::startsWith(Request::path(), 'panel') && Auth::user() && Auth::user()->can('panel.access'))
                <a href="{{ route('panel.index') }}" @click="closeMenu()"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 bg-amber-500 hover:bg-amber-600 text-white mb-2">
                    <i class="fa-solid fa-shield-halved w-6 h-6 flex-shrink-0 flex items-center justify-center"></i>
                    <span class="ml-3 whitespace-nowrap">Панель управления</span>
                </a>
                <div class="border-t border-slate-200 dark:border-slate-800 my-2"></div>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                        @click="closeMenu()"
                        class="w-full flex items-center px-3 py-3 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-right-from-bracket w-6 h-6 flex-shrink-0 flex items-center justify-center"></i>
                    <span class="ml-3 whitespace-nowrap">Выйти</span>
                </button>
            </form>
        </div>
    </aside>
</div>
