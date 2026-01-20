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

    init() {
        this.$nextTick(() => {
            const sortable = new Sortable(document.getElementById('widget-sort-list'), {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'opacity-50',
                onEnd: (evt) => {
                    const newOrder = [];
                    const items = evt.to.querySelectorAll('[data-widget]');
                    items.forEach(item => {
                        newOrder.push(item.dataset.widget);
                    });
                    this.widgetOrder = newOrder;
                }
            });
        });
    },

    async saveSettings() {
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
                    widget_order: this.widgetOrder
                })
            });

            const data = await response.json();

            if (data.success) {
                this.saved = true;
                setTimeout(() => {
                    this.saved = false;
                }, 3000);
            } else {
                this.error = 'Произошла ошибка при сохранении';
            }
        } catch (e) {
            this.error = 'Ошибка сети или сервера';
        } finally {
            this.saving = false;
        }
    }
}">

    <div class="space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">
                        Настройки Dashboard
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Настройте отображение виджетов и их порядок
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Назад</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <!-- Widget Visibility -->
            <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                    Видимость виджетов
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.stats_header"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Общая статистика</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Записи сегодня, выполнено за неделю, новые клиенты</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.quick_actions"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Быстрые действия</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Новая запись, новый клиент, календарь</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.next_appointment"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Следующая запись</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Акцентный виджет с градиентом</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.today_appointments"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Записи на сегодня</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Список с временем, клиентом, услугой</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.pending_appointments"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Требуют внимания</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Записи со статусом pending</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.recent_clients"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Недавние клиенты</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Последние 5 добавленных клиентов</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               x-model="widgets.weekly_chart"
                               class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Недельная статистика</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">График записей за неделю (в разработке)</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Widget Order -->
            <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                    Порядок виджетов
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Перетащите виджеты, чтобы изменить их порядок на dashboard
                </p>
                <div id="widget-sort-list" class="space-y-2">
                    <template x-for="widget in widgetOrder" :key="widget">
                        <div :data-widget="widget" 
                             class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 cursor-move drag-handle hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex-shrink-0 text-slate-400">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="{
                                    'next_appointment': 'Следующая запись',
                                    'today_appointments': 'Записи на сегодня',
                                    'pending_appointments': 'Требуют внимания',
                                    'recent_clients': 'Недавние клиенты',
                                    'weekly_chart': 'Недельная статистика'
                                }[widget]"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">
                                    Виджет
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-4 md:p-6 bg-slate-50 dark:bg-slate-800/30">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <template x-if="saved">
                            <div class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 text-sm font-medium">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>Настройки сохранены</span>
                            </div>
                        </template>
                        <template x-if="error">
                            <div class="inline-flex items-center gap-2 text-rose-600 dark:text-rose-400 text-sm font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span x-text="error"></span>
                            </div>
                        </template>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Отмена</span>
                        </a>
                        <button @click="saveSettings()" 
                                :disabled="saving"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <i class="fa-solid fa-floppy-disk text-xs" x-show="!saving"></i>
                            <i class="fa-solid fa-spinner fa-spin text-xs" x-show="saving"></i>
                            <span x-text="saving ? 'Сохранение...' : 'Сохранить настройки'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
