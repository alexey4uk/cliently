{{-- Нижняя часть сайдбара: информация о тарифе (клиент) и кнопка «Выйти» --}}

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
