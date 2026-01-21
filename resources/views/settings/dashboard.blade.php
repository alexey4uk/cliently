@extends('layouts.user')

@section('title', 'Настройки Dashboard - Cliently')
@section('page-title', 'Настройки Dashboard')
@section('page-description', 'Настройка отображения виджетов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => '#'],
        ['title' => 'Dashboard', 'url' => '#'],
    ]" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')

<div x-data="{
    widgets: @js($widgets),
    widgetOrder: @js($widgetOrder),
    saving: false,
    saved: false,
    error: null,
    activeTab: 'visibility',
    sortableInstance: null,

    init() {
        // Убеждаемся, что widgetOrder - это массив
        if (!Array.isArray(this.widgetOrder)) {
            this.widgetOrder = [];
        }
        
        this.$watch('activeTab', (value) => {
            if (value === 'order') {
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.initSortable();
                    }, 150);
                });
            }
        });
        
        if (this.activeTab === 'order') {
            this.$nextTick(() => {
                this.initSortable();
            });
        }
    },

    initSortable() {
        const sortList = document.getElementById('widget-sort-list');
        if (!sortList || typeof Sortable === 'undefined') return;
        
        // Удаляем предыдущий экземпляр
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
            this.sortableInstance = null;
        }
        
        this.sortableInstance = new Sortable(sortList, {
            animation: 200,
            handle: '.drag-handle',
            ghostClass: 'opacity-50',
            chosenClass: 'ring-indigo-500',
            onEnd: (evt) => {
                const newOrder = [];
                const items = evt.to.querySelectorAll('[data-widget]');
                items.forEach(item => {
                    newOrder.push(item.dataset.widget);
                });
                this.widgetOrder = newOrder;
                // Автоматически сохраняем изменения без показа полного сообщения
                this.saveSettings(false);
            }
        });
    },

    showToast(type, message) {
        // Получаем или создаем контейнер для toast
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-4 right-4 z-50 space-y-3';
            document.body.appendChild(container);
        }

        // Конфигурация стилей
        const config = {
            success: {
                icon: 'fa-check-circle',
                iconColor: 'text-emerald-500',
                borderColor: 'border-emerald-500/30',
            },
            error: {
                icon: 'fa-exclamation-circle',
                iconColor: 'text-rose-500',
                borderColor: 'border-rose-500/30',
            },
            info: {
                icon: 'fa-info-circle',
                iconColor: 'text-blue-500',
                borderColor: 'border-blue-500/30',
            },
            warning: {
                icon: 'fa-triangle-exclamation',
                iconColor: 'text-amber-500',
                borderColor: 'border-amber-500/30',
            },
        };

        const style = config[type] || config.info;

        // Создаем элемент toast с Alpine.js
        const toast = document.createElement('div');
        toast.setAttribute('x-data', '{ show: false }');
        toast.setAttribute('x-init', 'setTimeout(() => { show = true; setTimeout(() => { show = false; setTimeout(() => $el.remove(), 300); }, 5000); }, 10)');
        toast.setAttribute('x-show', 'show');
        toast.setAttribute('x-transition:enter', 'transition ease-out duration-400');
        toast.setAttribute('x-transition:enter-start', 'opacity-0 translate-y-4 translate-x-full scale-95');
        toast.setAttribute('x-transition:enter-end', 'opacity-100 translate-y-0 translate-x-0 scale-100');
        toast.setAttribute('x-transition:leave', 'transition ease-in duration-250');
        toast.setAttribute('x-transition:leave-start', 'opacity-100 translate-y-0 translate-x-0 scale-100');
        toast.setAttribute('x-transition:leave-end', 'opacity-0 translate-y-4 translate-x-full scale-95');
        toast.className = 'toast-notification relative flex items-center gap-3 rounded-xl backdrop-blur-xl bg-white/80 dark:bg-slate-900/80 border ' + style.borderColor + ' p-4 min-w-[280px] max-w-md';
        toast.setAttribute('role', 'alert');

        const iconDiv = document.createElement('div');
        iconDiv.className = 'flex-shrink-0';
        const iconInner = document.createElement('div');
        iconInner.className = 'flex h-8 w-8 items-center justify-center';
        const icon = document.createElement('i');
        icon.className = 'fa-solid ' + style.icon + ' text-lg ' + style.iconColor;
        iconInner.appendChild(icon);
        iconDiv.appendChild(iconInner);
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex-1 min-w-0';
        const messageP = document.createElement('p');
        messageP.className = 'text-sm font-medium text-slate-900 dark:text-slate-100 leading-relaxed';
        messageP.textContent = message;
        messageDiv.appendChild(messageP);
        
        toast.appendChild(iconDiv);
        toast.appendChild(messageDiv);

        container.appendChild(toast);

        // Инициализируем Alpine.js для нового элемента
        this.$nextTick(() => {
            if (window.Alpine && window.Alpine.initTree) {
                window.Alpine.initTree(toast);
            }
        });
    },

    async saveSettings(showToast = true) {
        this.saving = true;
        this.error = null;
        this.saved = false;

        try {
            const response = await fetch('{{ route('settings.dashboard.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    widgets: this.widgets,
                    widget_order: this.widgetOrder || []
                })
            });

            const data = await response.json();

            if (data.success) {
                if (showToast) {
                    this.showToast('success', 'Настройки успешно сохранены! Изменения применены к вашему дашборду.');
                } else {
                    this.showToast('success', 'Порядок виджетов изменен и сохранен');
                }
            } else {
                this.showToast('error', data.message || 'Произошла ошибка при сохранении настроек');
            }
        } catch (e) {
            this.showToast('error', 'Ошибка сети или сервера. Попробуйте еще раз.');
        } finally {
            this.saving = false;
        }
    },

    getWidgetInfo(key) {
        const info = {
            'stats_header': { name: 'Общая статистика', icon: 'fa-chart-line', color: 'indigo', category: 'Основные' },
            'quick_actions': { name: 'Быстрые действия', icon: 'fa-bolt', color: 'emerald', category: 'Основные' },
            'stat_today': { name: 'Сегодня', icon: 'fa-calendar-day', color: 'indigo', category: 'Метрики' },
            'stat_week': { name: 'За неделю', icon: 'fa-check-circle', color: 'emerald', category: 'Метрики' },
            'stat_new_clients': { name: 'Новые клиенты', icon: 'fa-user-plus', color: 'blue', category: 'Метрики' },
            'stat_total_clients': { name: 'Всего клиентов', icon: 'fa-users', color: 'purple', category: 'Метрики' },
            'stat_pending': { name: 'Ожидают', icon: 'fa-hourglass-half', color: 'amber', category: 'Метрики' },
            'stat_completed': { name: 'Завершено', icon: 'fa-check-double', color: 'blue', category: 'Метрики' },
            'stat_cancelled': { name: 'Отменено', icon: 'fa-xmark-circle', color: 'rose', category: 'Метрики' },
            'stat_avg_per_day': { name: 'Среднее/день', icon: 'fa-chart-bar', color: 'slate', category: 'Метрики' },
            'appointments_chart': { name: 'График активности записей', icon: 'fa-chart-area', color: 'purple', category: 'Графики' },
            'clients_chart': { name: 'График новых клиентов', icon: 'fa-chart-bar', color: 'blue', category: 'Графики' },
            'next_appointment': { name: 'Следующая запись', icon: 'fa-clock', color: 'amber', category: 'Списки' },
            'today_appointments': { name: 'Записи на сегодня', icon: 'fa-calendar-day', color: 'indigo', category: 'Списки' },
            'pending_appointments': { name: 'Требуют внимания', icon: 'fa-exclamation-circle', color: 'rose', category: 'Списки' },
            'recent_clients': { name: 'Недавние клиенты', icon: 'fa-users', color: 'emerald', category: 'Списки' },
            'weekly_chart': { name: 'Недельная статистика', icon: 'fa-chart-line', color: 'purple', category: 'Графики' }
        };
        return info[key] || { name: key, icon: 'fa-square', color: 'slate', category: 'Другие' };
    }
}">

    <div class="space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Настройки Dashboard
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                        Настройте отображение виджетов и их порядок на главной странице
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Назад к дашборду</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 dark:border-slate-800">
                <div class="flex overflow-x-auto">
                    <button @click="activeTab = 'visibility'"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
                            :class="activeTab === 'visibility' 
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' 
                                : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fa-solid fa-eye mr-2"></i>
                        Видимость виджетов
                    </button>
                    <button @click="activeTab = 'order'"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
                            :class="activeTab === 'order' 
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' 
                                : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fa-solid fa-sort mr-2"></i>
                        Порядок отображения
                    </button>
                </div>
            </div>

            <!-- Tab Content: Visibility -->
            <div x-show="activeTab === 'visibility'" class="p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                        Управление виджетами
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Включите или отключите виджеты, которые будут отображаться на вашем дашборде
                    </p>
                </div>

                <!-- Основные виджеты -->
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-star text-amber-500"></i>
                        Основные виджеты
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="key in ['stats_header', 'quick_actions']" :key="key">
                            <div class="relative">
                                <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all hover:shadow-md"
                                       :class="widgets[key] 
                                           ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10' 
                                           : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" 
                                               x-model="widgets[key]"
                                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="h-8 w-8 rounded-lg flex items-center justify-center"
                                                 :class="{
                                                     'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400': getWidgetInfo(key).color === 'indigo',
                                                     'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400': getWidgetInfo(key).color === 'emerald'
                                                 }">
                                                <i :class="'fa-solid ' + getWidgetInfo(key).icon" class="text-sm"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="getWidgetInfo(key).name"></p>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed" x-text="{
                                            'stats_header': 'Карточки с ключевыми метриками: записи сегодня, за неделю, новые клиенты',
                                            'quick_actions': 'Кнопки быстрого доступа: новая запись, новый клиент, календарь'
                                        }[key]"></p>
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Настройки карточек метрик -->
                <div class="mb-8" x-show="widgets.stats_header">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                        Карточки метрик
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Выберите, какие карточки метрик отображать в верхнем блоке
                    </p>
                    
                    <!-- Предупреждение, если все карточки отключены -->
                    <div x-show="!widgets.stat_today && !widgets.stat_week && !widgets.stat_new_clients && !widgets.stat_total_clients && !widgets.stat_pending && !widgets.stat_completed && !widgets.stat_cancelled && !widgets.stat_avg_per_day" 
                         class="mb-4 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-amber-900 dark:text-amber-100">
                                    Все карточки метрик отключены
                                </p>
                                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                                    Если все карточки отключены, блок "Общая статистика" не будет отображаться на дашборде, даже если он включен.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <template x-for="key in ['stat_today', 'stat_week', 'stat_new_clients', 'stat_total_clients', 'stat_pending', 'stat_completed', 'stat_cancelled', 'stat_avg_per_day']" :key="key">
                            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all hover:shadow-sm"
                                   :class="widgets[key] 
                                       ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-900/10' 
                                       : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="checkbox" 
                                       x-model="widgets[key]"
                                       class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-slate-900 dark:text-white" x-text="{
                                        'stat_today': 'Сегодня',
                                        'stat_week': 'За неделю',
                                        'stat_new_clients': 'Новые клиенты',
                                        'stat_total_clients': 'Всего клиентов',
                                        'stat_pending': 'Ожидают',
                                        'stat_completed': 'Завершено',
                                        'stat_cancelled': 'Отменено',
                                        'stat_avg_per_day': 'Среднее/день'
                                    }[key]"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Графики -->
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-purple-500"></i>
                        Графики и аналитика
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="key in ['appointments_chart', 'clients_chart', 'weekly_chart']" :key="key">
                            <div class="relative">
                                <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all hover:shadow-md"
                                       :class="widgets[key] 
                                           ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10' 
                                           : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" 
                                               x-model="widgets[key]"
                                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="h-8 w-8 rounded-lg flex items-center justify-center"
                                                 :class="{
                                                     'bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400': getWidgetInfo(key).color === 'purple',
                                                     'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400': getWidgetInfo(key).color === 'blue'
                                                 }">
                                                <i :class="'fa-solid ' + getWidgetInfo(key).icon" class="text-sm"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="getWidgetInfo(key).name"></p>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed" x-text="{
                                            'appointments_chart': 'Линейный график активности записей за последние 30 дней',
                                            'clients_chart': 'Столбчатый график новых клиентов за последние 30 дней',
                                            'weekly_chart': 'Недельная статистика записей (в разработке)'
                                        }[key]"></p>
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Списки -->
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-list text-slate-500"></i>
                        Списки и детали
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="key in ['next_appointment', 'today_appointments', 'pending_appointments', 'recent_clients']" :key="key">
                            <div class="relative">
                                <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all hover:shadow-md"
                                       :class="widgets[key] 
                                           ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10' 
                                           : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" 
                                               x-model="widgets[key]"
                                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="h-8 w-8 rounded-lg flex items-center justify-center"
                                                 :class="{
                                                     'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400': getWidgetInfo(key).color === 'amber',
                                                     'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400': getWidgetInfo(key).color === 'indigo',
                                                     'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400': getWidgetInfo(key).color === 'rose',
                                                     'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400': getWidgetInfo(key).color === 'emerald'
                                                 }">
                                                <i :class="'fa-solid ' + getWidgetInfo(key).icon" class="text-sm"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="getWidgetInfo(key).name"></p>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed" x-text="{
                                            'next_appointment': 'Акцентный виджет с информацией о следующей записи',
                                            'today_appointments': 'Полный список записей на сегодня с деталями',
                                            'pending_appointments': 'Записи, ожидающие подтверждения',
                                            'recent_clients': 'Последние 5 добавленных клиентов'
                                        }[key]"></p>
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Order -->
            <div x-show="activeTab === 'order'" class="p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                        Порядок отображения виджетов
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Перетащите виджеты, чтобы изменить их порядок на дашборде. Виджеты отображаются сверху вниз.
                    </p>
                </div>

                <div id="widget-sort-list" class="space-y-3">
                    <template x-for="(widget, index) in widgetOrder" :key="widget">
                        <div :data-widget="widget" 
                             class="flex items-center gap-4 p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 hover:border-indigo-300 dark:hover:border-indigo-700 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 transition-all group">
                            <div class="flex-shrink-0 mt-1">
                                <div class="drag-handle text-slate-400 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors cursor-move">
                                    <i class="fa-solid fa-grip-vertical text-lg"></i>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-lg flex items-center justify-center"
                                     :class="{
                                         'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400': getWidgetInfo(widget).color === 'indigo',
                                         'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400': getWidgetInfo(widget).color === 'emerald',
                                         'bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400': getWidgetInfo(widget).color === 'purple',
                                         'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400': getWidgetInfo(widget).color === 'blue',
                                         'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400': getWidgetInfo(widget).color === 'amber',
                                         'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400': getWidgetInfo(widget).color === 'rose'
                                     }">
                                    <i :class="'fa-solid ' + getWidgetInfo(widget).icon" class="text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="getWidgetInfo(widget).name"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="getWidgetInfo(widget).category"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                    <span x-text="index + 1"></span>
                                </span>
                            </div>
                            <div class="flex-shrink-0 text-slate-400">
                                <i class="fa-solid fa-chevron-up text-xs"></i>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Совет</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                Перетащите виджеты за иконку слева, чтобы изменить их порядок. Виджеты, которые отключены в разделе "Видимость", не будут отображаться независимо от порядка.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Изменения применяются сразу после сохранения
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <span>Отмена</span>
                    </a>
                    <button @click="saveSettings()" 
                            :disabled="saving"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                        <i class="fa-solid fa-floppy-disk text-xs" x-show="!saving"></i>
                        <i class="fa-solid fa-spinner fa-spin text-xs" x-show="saving"></i>
                        <span x-text="saving ? 'Сохранение...' : 'Сохранить изменения'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
