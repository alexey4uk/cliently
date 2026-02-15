@php
$csrfToken = csrf_token();
@endphp

<div x-data="notificationDropdown()" 
    x-init="init()" 
    class="relative">
    
    <!-- Кнопка уведомлений -->
    <button
        x-ref="notificationsButton"
        @click.stop="toggle()"
        type="button"
        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 relative group"
        aria-label="Уведомления"
        :class="{ 'bg-slate-100 dark:bg-slate-800': open }">
        <i class="fa-solid fa-bell text-base transition-transform duration-200 group-hover:scale-110"></i>
        <span x-show="unreadCount > 0"
              x-transition
              class="absolute -top-0.5 -right-0.5 h-4 w-4 bg-rose-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold ring-2 ring-white dark:ring-slate-900">
            <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
        </span>
    </button>

    <!-- Выпадающий список -->
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
        x-cloak
        class="fixed z-[100] w-[calc(100vw-2rem)] sm:w-80 max-w-sm rounded-lg border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900 shadow-xl"
        style="display: none;"
        x-init="
            $watch('open', value => {
                if (value) {
                    $nextTick(() => {
                        const button = $refs.notificationsButton;
                        const menu = $el;
                        if (button && menu) {
                            const buttonRect = button.getBoundingClientRect();
                            const viewportHeight = window.innerHeight;
                            const viewportWidth = window.innerWidth;
                            const menuHeight = 400;
                            const menuWidth = menu.offsetWidth || 320;
                            
                            let top = buttonRect.bottom + 8;
                            let right = viewportWidth - buttonRect.right;
                            
                            if (top + menuHeight > viewportHeight - 10) {
                                top = buttonRect.top - menuHeight - 8;
                            }
                            
                            if (right + menuWidth > viewportWidth - 10) {
                                right = 16;
                            }
                            
                            if (right < 0 || viewportWidth - right - menuWidth < 10) {
                                menu.style.left = '16px';
                                menu.style.right = 'auto';
                            } else {
                                menu.style.right = right + 'px';
                                menu.style.left = 'auto';
                            }
                            
                            menu.style.top = top + 'px';
                        }
                    });
                }
            });
        ">
        
        <!-- Заголовок -->
        <div class="px-4 py-3 border-b border-slate-200/50 dark:border-slate-800/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                Уведомления
            </h3>
            <button 
                x-show="notifications.length > 0 && unreadCount > 0"
                @click.stop="markAllAsRead()"
                type="button"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                Отметить все
            </button>
        </div>
        
        <!-- Список уведомлений -->
        <div class="max-h-96 overflow-y-auto" x-ref="notificationsList">
            <!-- Загрузка -->
            <div x-show="loading" class="px-4 py-8 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500 mx-auto"></div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Загрузка...</p>
            </div>

            <!-- Ошибка -->
            <div x-show="error && !loading" class="px-4 py-4 text-center">
                <p class="text-xs text-rose-600 dark:text-rose-400" x-text="error"></p>
            </div>

            <!-- Нет уведомлений -->
            <div x-show="!loading && !error && notifications.length === 0" class="px-4 py-8 text-center">
                <i class="fa-regular fa-bell text-4xl text-slate-300 dark:text-slate-600"></i>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">Новых уведомлений нет</p>
            </div>

            <!-- Список уведомлений -->
            <template x-for="notification in notifications" :key="notification.id">
                <div 
                    @click.stop="redirectToNotification(notification)"
                    class="px-4 py-3 border-b border-slate-100/50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer relative group">
                    
                    <div class="flex items-start gap-3">
                        <!-- Иконка -->
                        <div class="shrink-0 mt-0.5">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center"
                                 :class="getIconClass(notification.type)">
                                <i :class="'fa-solid ' + getIcon(notification.type) + ' text-xs'"></i>
                            </div>
                        </div>
                        
                        <!-- Контент -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="notification.title || 'Уведомление'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2" x-text="notification.message || ''"></p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1" x-text="formatDate(notification.created_at)"></p>
                        </div>
                        
                        <!-- Индикатор непрочитанного -->
                        <div class="shrink-0">
                            <span x-show="!notification.is_read" class="h-2 w-2 bg-rose-500 rounded-full block mt-1"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Футер -->
        <div class="px-4 py-3 border-t border-slate-200/50 dark:border-slate-800/50 bg-slate-50 dark:bg-slate-800/50">
            <a href="{{ Str::startsWith(Request::path(), 'panel') ? route('panel.notifications.index') : route('notifications.index') }}" 
               x-show="notifications.length > 0 || unreadCount > 0"
               class="text-sm text-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors block">
                Показать все уведомления
            </a>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        loading: false,
        error: null,
        csrfToken: '{{ $csrfToken }}',

        async init() {
            await this.loadUnreadCount();
            
            setInterval(() => {
                this.loadUnreadCount();
            }, 30000);
        },

        async toggle() {
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) {
                await this.loadNotifications();
            }
        },

        async loadUnreadCount() {
            try {
                const response = await fetch('/notifications/unread-count', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.data) {
                        this.unreadCount = data.data.count || 0;
                    }
                }
            } catch (error) {
                console.error('Failed to load unread count:', error);
            }
        },

        async loadNotifications() {
            this.loading = true;
            this.error = null;
            try {
                const response = await fetch('/notifications/unread', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.data) {
                        this.notifications = data.data.notifications || [];
                    } else {
                        this.notifications = [];
                    }
                } else {
                    this.error = 'Не удалось загрузить уведомления';
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
                this.error = 'Ошибка при загрузке уведомлений';
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch('/notifications/' + notificationId + '/read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    this.notifications = this.notifications.filter(n => n.id !== notificationId);
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    await this.loadUnreadCount();
                }
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    this.notifications = [];
                    this.unreadCount = 0;
                    await this.loadUnreadCount();
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Только что';
            if (diffMins < 60) return diffMins + ' мин назад';
            if (diffHours < 24) return diffHours + ' ч назад';
            if (diffDays < 7) return diffDays + ' дн назад';
            
            return date.toLocaleDateString('ru-RU', { day: '2-digit', month: 'short' });
        },

        getIcon(type) {
            const icons = {
                'ticket.created': 'fa-ticket',
                'ticket.updated': 'fa-pen',
                'ticket.comment': 'fa-comment',
                'ticket.assigned': 'fa-user-check',
                'appointment.created': 'fa-calendar-check',
                'appointment.updated': 'fa-calendar',
                'appointment.upcoming': 'fa-clock',
                'appointment.cancelled': 'fa-calendar-xmark',
                'subscription.limit': 'fa-exclamation-triangle',
                'subscription.expiring': 'fa-clock-rotate-left',
                'subscription.payment.success': 'fa-check-circle',
                'subscription.payment.failed': 'fa-xmark-circle',
                'subscription.plan.changed': 'fa-arrow-right-arrow-left',
                'subscription.renewed': 'fa-rotate',
                'subscription.trial.started': 'fa-gift',
                'subscription.trial.ending': 'fa-clock',
                'client.new': 'fa-user-plus',
                'business.user.invited': 'fa-user-plus',
                'business.user.joined': 'fa-user-check',
                'business.user.removed': 'fa-user-minus',
                'business.user.role_changed': 'fa-user-gear',
                'telegram.connected': 'fa-paper-plane',
                'telegram.disconnected': 'fa-paper-plane',
                'admin.business.created': 'fa-building',
                'admin.business.deleted': 'fa-building',
                'admin.business.inactive': 'fa-building',
                'admin.ticket.created': 'fa-ticket',
                'admin.ticket.critical': 'fa-exclamation-circle',
                'admin.user.created': 'fa-user-plus',
                'admin.subscription.expiring': 'fa-clock-rotate-left',
                'admin.subscription.limit.exceeded': 'fa-exclamation-triangle',
                'admin.system.error': 'fa-triangle-exclamation',
                'admin.broadcast': 'fa-paper-plane',
            };
            return icons[type] || 'fa-bell';
        },

        getIconClass(type) {
            const colors = {
                'ticket.created': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'ticket.updated': 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                'ticket.comment': 'bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400',
                'ticket.assigned': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'appointment.created': 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
                'appointment.updated': 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                'appointment.upcoming': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'appointment.cancelled': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'subscription.limit': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'subscription.expiring': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'subscription.payment.success': 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
                'subscription.payment.failed': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'subscription.plan.changed': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'subscription.renewed': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'subscription.trial.started': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'subscription.trial.ending': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'client.new': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'business.user.invited': 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                'business.user.joined': 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                'business.user.removed': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'business.user.role_changed': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'telegram.connected': 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
                'telegram.disconnected': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'admin.business.created': 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
                'admin.business.deleted': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'admin.business.inactive': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'admin.ticket.created': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                'admin.ticket.critical': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'admin.user.created': 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                'admin.subscription.expiring': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'admin.subscription.limit.exceeded': 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                'admin.system.error': 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                'admin.broadcast': 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
            };
            return colors[type] || 'bg-slate-100 dark:bg-slate-500/20 text-slate-600 dark:text-slate-400';
        },

        redirectToNotification(notification) {
            if (!notification.is_read) {
                this.markAsRead(notification.id);
            }
            
            if (notification.data && notification.data.url) {
                window.location.href = notification.data.url;
            }
        }
    };
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
