@props([
    'showProfile' => true,
    'showMobileMenu' => true,
    'showRoleBadge' => false,
    'showNotificationsDropdown' => false,
    'currentBusiness' => null,
    'userBusinesses' => null,
])

<header class="border-b border-slate-200/50 dark:border-slate-800/50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm w-full sticky top-0 z-30">
    <div class="w-full px-3 sm:px-4 md:px-6 lg:px-8 py-2.5 sm:py-3 md:py-3.5">
        <div class="flex items-center justify-between gap-2 sm:gap-3 md:gap-4 min-w-0">
            <!-- Левая часть -->
            <div class="flex items-center gap-2 sm:gap-3 md:gap-4 shrink-0">
                <!-- Логотип (только мобильные) -->
                <a href="{{ route(Str::startsWith(Request::path(), 'panel') ? 'panel.index' : 'dashboard') }}" 
                   class="lg:hidden flex items-center gap-2.5 shrink-0 hover:opacity-80 transition-opacity duration-200">
                    <x-logo size="sm" />
                    <span class="text-lg font-bold text-slate-900 dark:text-white tracking-tight uppercase font-display">CLIENTLY</span>
                </a>
            </div>

            <!-- Правая часть: Действия -->
            <div class="flex items-center gap-1 sm:gap-1.5 md:gap-2 shrink-0">
                <!-- Бейдж роли пользователя (системная роль) -->
                @if($showRoleBadge && Auth::check() && Auth::user()->roles->isNotEmpty())
                    @php
                        $primaryRole = Auth::user()->roles->first();
                        $roleName = $primaryRole->name;
                        $roleDisplayName = ucfirst($roleName);
                        
                        // Определяем цвета в зависимости от роли
                        $bgColor = match($roleName) {
                            'admin' => 'bg-red-100/80 dark:bg-red-500/20 text-red-700 dark:text-red-400 border-red-200/50 dark:border-red-600/30',
                            'support' => 'bg-amber-100/80 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 border-amber-200/50 dark:border-amber-600/30',
                            'manager' => 'bg-blue-100/80 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 border-blue-200/50 dark:border-blue-600/30',
                            default => 'bg-slate-100/80 dark:bg-slate-500/20 text-slate-700 dark:text-slate-400 border-slate-200/50 dark:border-slate-600/30',
                        };
                        
                        $icon = match($roleName) {
                            'admin' => 'fa-shield-halved',
                            'support' => 'fa-headset',
                            'manager' => 'fa-user-tie',
                            default => 'fa-user',
                        };
                    @endphp
                    <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-semibold {{ $bgColor }} rounded-full border">
                        <i class="fa-solid {{ $icon }} text-[9px]"></i>
                        <span class="hidden lg:inline">{{ $roleDisplayName }}</span>
                    </span>
                @endif

                <!-- Бейдж роли работника в бизнесе (только для клиентской части) -->
                @if(!Str::startsWith(Request::path(), 'panel') && Auth::check())
                    @php
                        $user = Auth::user();
                        $businessRole = null;
                        $businessRoleSlug = null;
                        $businessRoleName = null;
                        $business = $currentBusiness ?? $user?->businesses->first();
                        if ($user && $business) {
                            $pivot = $user->businesses()->where('business_id', $business->id)->first();
                            $businessRoleId = $pivot?->pivot->role_id;
                            if ($businessRoleId) {
                                $businessRole = \App\Models\BusinessRole::find($businessRoleId);
                                $businessRoleSlug = $businessRole?->slug;
                                $businessRoleName = $businessRole?->name;
                            } else {
                                $businessRoleSlug = null;
                            }
                        }
                    @endphp
                    @if($businessRoleSlug)
                        @php
                            $bgColor = match($businessRoleSlug) {
                                'owner' => 'bg-amber-100/80 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 border-amber-200/50 dark:border-amber-600/30',
                                'admin' => 'bg-indigo-100/80 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 border-indigo-200/50 dark:border-indigo-600/30',
                                'master' => 'bg-purple-100/80 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400 border-purple-200/50 dark:border-purple-600/30',
                                default => 'bg-slate-100/80 dark:bg-slate-500/20 text-slate-700 dark:text-slate-400 border-slate-200/50 dark:border-slate-600/30',
                            };
                            
                            $icon = match($businessRoleSlug) {
                                'owner' => 'fa-crown',
                                'admin' => 'fa-user-shield',
                                'master' => 'fa-user',
                                default => 'fa-user',
                            };
                            
                            $roleDisplayName = $businessRoleName ?? match($businessRoleSlug) {
                                'owner' => 'Владелец',
                                'admin' => 'Администратор',
                                'master' => 'Мастер',
                                default => ucfirst($businessRoleSlug),
                            };
                        @endphp
                        <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-semibold {{ $bgColor }} rounded-full border">
                            <i class="fa-solid {{ $icon }} text-[9px]"></i>
                            <span class="hidden lg:inline">{{ $roleDisplayName }}</span>
                        </span>
                    @endif
                @endif

                <!-- Переключатель темы -->
                <button id="theme-toggle"
                    class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 group"
                    aria-label="Переключить тему">
                    <x-icon name="sun" size="md" variant="solid" class="hidden dark:block group-hover:scale-110 transition-transform duration-200" />
                    <x-icon name="moon" size="md" variant="solid" class="block dark:hidden group-hover:scale-110 transition-transform duration-200" />
                </button>

                <!-- Уведомления -->
                @if($showNotificationsDropdown)
                    <x-header-notifications />
                @endif

                <!-- Профиль пользователя -->
                @if($showProfile)
                    <x-header-profile />
                @endif

                <!-- Кнопка меню (только мобильные) -->
                @if($showMobileMenu)
                    @include('mobile-menu')
                @endif
            </div>
        </div>
    </div>
</header>
