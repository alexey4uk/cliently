@props([
    'hasNewNotifications' => true,
])

<div x-data="{ open: false }" class="relative">
    <button
        x-ref="notificationsButton"
        @click="open = !open"
        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 relative group"
        aria-label="Уведомления"
        :class="{ 'bg-slate-100 dark:bg-slate-800': open }">
        <i class="fa-solid fa-bell text-base transition-transform duration-200 group-hover:scale-110"></i>
        @if($hasNewNotifications)
            <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-slate-900 animate-pulse"></span>
        @endif
    </button>
    <div
        x-show="open"
        @click.away="open = false"
        @keydown.escape.window="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
        class="fixed z-[100] w-[calc(100vw-1.5rem)] sm:w-80 max-w-sm rounded-lg border border-slate-200/80 dark:border-slate-800/80 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm shadow-lg"
        style="display: none;"
        x-init="
            $watch('open', value => {
                if (value) {
                    $nextTick(() => {
                        const button = $refs.notificationsButton;
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
        <!-- Заголовок -->
        <div class="px-4 py-3 border-b border-slate-200/50 dark:border-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                Уведомления
            </h3>
            <button class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                Отметить все
            </button>
        </div>
        
        <!-- Список уведомлений -->
        <div class="max-h-96 overflow-y-auto">
            <!-- Пример уведомления -->
            <div class="px-4 py-3 border-b border-slate-100/50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 mt-0.5">
                        <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            Новая запись
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            У вас новая запись на завтра в 14:00
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                            2 часа назад
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="h-2 w-2 bg-rose-500 rounded-full block"></span>
                    </div>
                </div>
            </div>
            
            <!-- Пример уведомления -->
            <div class="px-4 py-3 border-b border-slate-100/50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 mt-0.5">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            Запись выполнена
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Запись от 15:00 была отмечена как выполненная
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                            5 часов назад
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Пример уведомления -->
            <div class="px-4 py-3 border-b border-slate-100/50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 mt-0.5">
                        <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation text-amber-600 dark:text-amber-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            Требуется внимание
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            У вас есть записи, требующие подтверждения
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                            Вчера
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Футер -->
        <div class="px-4 py-3 border-t border-slate-200/50 dark:border-slate-800/50">
            <a href="#" class="text-sm text-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors block">
                Показать все уведомления
            </a>
        </div>
    </div>
</div>
