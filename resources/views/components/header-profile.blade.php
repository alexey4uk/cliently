@props([
    'showOnMobile' => false,
])

<div x-data="{ open: false }" class="relative {{ $showOnMobile ? '' : 'hidden lg:block' }}">
    @auth
        <button
            x-ref="profileButton"
            @click="open = !open"
            class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 overflow-hidden ring-1 ring-slate-200/50 dark:ring-slate-700/50 group"
            aria-label="Профиль"
            :class="{ 'bg-slate-100 dark:bg-slate-800 ring-slate-300 dark:ring-slate-600': open }">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                     alt="{{ Auth::user()->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            @else
                <span class="h-full w-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold">
                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                </span>
            @endif
        </button>
    @else
        <button
            x-ref="profileButton"
            @click="open = !open"
            class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-xs font-semibold text-slate-800 dark:text-slate-100 border border-slate-300/50 dark:border-slate-700/50 hover:bg-slate-300 dark:hover:bg-slate-700 transition-all duration-200"
            aria-label="Профиль"
            :class="{ 'bg-slate-300 dark:bg-slate-700': open }">
            АМ
        </button>
    @endauth
    
    <div
        x-show="open"
        @click.away="open = false"
        @keydown.escape.window="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
        class="fixed z-[100] w-[calc(100vw-1.5rem)] sm:w-64 max-w-xs rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm shadow-xl overflow-hidden"
        style="display: none;"
        x-init="
            $watch('open', value => {
                if (value) {
                    $nextTick(() => {
                        const button = $refs.profileButton;
                        const menu = $el;
                        if (button) {
                            const buttonRect = button.getBoundingClientRect();
                            const viewportHeight = window.innerHeight;
                            const viewportWidth = window.innerWidth;
                            
                            menu.style.top = (buttonRect.bottom + 8) + 'px';
                            menu.style.right = (viewportWidth - buttonRect.right) + 'px';
                            
                            const menuRect = menu.getBoundingClientRect();
                            if (menuRect.bottom > viewportHeight - 10) {
                                menu.style.top = (buttonRect.top - menuRect.height - 8) + 'px';
                            }
                            if (menuRect.right > viewportWidth - 10) {
                                menu.style.right = '0.5rem';
                            }
                            if (menuRect.left < 10) {
                                menu.style.left = '0.5rem';
                                menu.style.right = 'auto';
                            }
                        }
                    });
                }
            });
        ">
        <!-- Информация о пользователе -->
        @auth
            <div class="px-4 py-3.5 bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-800/50 dark:to-slate-900/50 border-b border-slate-200/50 dark:border-slate-800/50">
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 group hover:opacity-90 transition-opacity">
                    <div class="h-11 w-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-sm font-semibold text-white overflow-hidden shrink-0 ring-2 ring-white/50 dark:ring-slate-700/50 shadow-sm">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                </a>
            </div>
        @endauth
        
        <!-- Действия -->
        <div class="py-1.5">
            <a href="{{ route('profile.edit') }}"
                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-left text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-500/30 transition-colors">
                    <i class="fa-solid fa-user text-xs text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <span class="font-medium">Профиль</span>
            </a>
            
            <a href="{{ route('notifications.index') }}"
                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-left text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group relative">
                <div class="h-8 w-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center shrink-0 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                    <i class="fa-solid fa-bell text-xs text-purple-600 dark:text-purple-400"></i>
                </div>
                <span class="font-medium">Уведомления</span>
                @php
                    $unreadCount = \App\Services\NotificationService::getUnreadCount(Auth::id());
                @endphp
                @if($unreadCount > 0)
                    <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-rose-500 text-white rounded-full">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('settings.notifications.index') }}"
                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-left text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                <div class="h-8 w-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center shrink-0 group-hover:bg-blue-200 dark:group-hover:bg-blue-500/30 transition-colors">
                    <i class="fa-solid fa-bell-slash text-xs text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="font-medium">Настройки уведомлений</span>
            </a>
            
            @if(Auth::user()->can('panel.access') && !Str::startsWith(Request::path(), 'panel'))
                <a href="{{ route('panel.index') }}"
                    class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-left text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center shrink-0 group-hover:bg-amber-200 dark:group-hover:bg-amber-500/30 transition-colors">
                        <i class="fa-solid fa-shield-halved text-xs text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <span class="font-medium">Админка</span>
                </a>
            @endif
            
            @if(Auth::user()->can('client.access') && Str::startsWith(Request::path(), 'panel'))
                <a href="{{ route('dashboard') }}"
                    class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-left text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/30 transition-colors">
                        <i class="fa-solid fa-user text-xs text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <span class="font-medium">Клиентская часть</span>
                </a>
            @endif
            
            <div class="border-t border-slate-200/50 dark:border-slate-800/50 my-1.5"></div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-left text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors group">
                    <div class="h-8 w-8 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center shrink-0 group-hover:bg-rose-200 dark:group-hover:bg-rose-500/30 transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </div>
                    <span class="font-medium">Выйти</span>
                </button>
            </form>
        </div>
    </div>
</div>
