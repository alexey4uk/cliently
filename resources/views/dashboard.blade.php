@extends('layouts.user')

@section('title', 'Главная - Cliently')
@section('page-title', 'Главная')
@section('page-description', 'Обзор вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div x-data="{ 
    showModal: false, 
    phone: '', 
    phoneDisplay: '', 
    client: '',
    showConfirmModal: false,
    confirmAction: '',
    confirmMessage: '',
    appointmentId: null,
    sortableInstance: null,
    isDragging: false,
    widgetOrder: @js($widgetOrder),
    saving: false,
    openModal(phone, phoneDisplay, client) {
        this.phone = phone;
        this.phoneDisplay = phoneDisplay;
        this.client = client;
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
    },
    openConfirmModal(action, message, appointmentId) {
        this.confirmAction = action;
        this.confirmMessage = message;
        this.appointmentId = appointmentId;
        this.showConfirmModal = true;
    },
    init() {
        this.$watch('showConfirmModal', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Инициализируем Sortable после загрузки страницы и Alpine.js
        this.$nextTick(() => {
            // Ждем загрузки Sortable.js
            const checkSortable = () => {
                if (typeof Sortable !== 'undefined') {
                    setTimeout(() => {
                        this.initSortable();
                    }, 300);
                } else {
                    setTimeout(checkSortable, 100);
                }
            };
            checkSortable();
        });
    },
    initSortable() {
        // Ждем полной загрузки DOM
        setTimeout(() => {
            const container = document.getElementById('dashboard-widgets-container');
            if (!container) {
                console.error('Dashboard container not found');
                return;
            }
            
            if (typeof Sortable === 'undefined') {
                console.error('Sortable.js is not loaded');
                return;
            }
            
            // Удаляем предыдущий экземпляр
            if (this.sortableInstance) {
                this.sortableInstance.destroy();
                this.sortableInstance = null;
            }
            
            // Проверяем наличие элементов для сортировки
            const items = container.querySelectorAll('[data-widget-key]');
            console.log('Found widgets:', items.length);
            
            if (items.length === 0) {
                console.warn('No widgets found for sorting');
                return;
            }
            
            try {
                let scrollInterval = null;
                let mouseMoveHandler = null;
                
                this.sortableInstance = new Sortable(container, {
                    animation: 150,
                    handle: '.drag-handle',
                    draggable: '> [data-widget-key]',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    forceFallback: false,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    preventOnFilter: false,
                    scroll: true,
                    scrollSensitivity: 100,
                    scrollSpeed: 20,
                    bubbleScroll: true,
                    group: {
                        name: 'widgets',
                        pull: true,
                        put: true
                    },
                    onStart: (evt) => {
                        this.isDragging = true;
                        evt.item.classList.add('sortable-dragging');
                        // Предотвращаем перетаскивание вложенных элементов
                        evt.item.style.pointerEvents = 'none';
                        console.log('Drag started for:', evt.item.getAttribute('data-widget-key'));
                        
                        // Добавляем обработчик движения мыши для прокрутки
                        mouseMoveHandler = (e) => {
                            if (!this.isDragging) return;
                            
                            const scrollThreshold = 200; // Увеличена область прокрутки до 200px
                            const scrollSpeed = 25;
                            const mouseY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : null);
                            
                            if (!mouseY) return;
                            
                            // Останавливаем предыдущую прокрутку
                            if (scrollInterval) {
                                clearInterval(scrollInterval);
                                scrollInterval = null;
                            }
                            
                            // Прокрутка вниз
                            if (mouseY > window.innerHeight - scrollThreshold) {
                                const distance = window.innerHeight - mouseY;
                                const speed = Math.max(15, Math.min(40, (scrollThreshold - distance) / 3));
                                scrollInterval = setInterval(() => {
                                    window.scrollBy({
                                        top: speed,
                                        left: 0,
                                        behavior: 'auto'
                                    });
                                }, 16);
                            }
                            // Прокрутка вверх
                            else if (mouseY < scrollThreshold) {
                                const distance = mouseY;
                                const speed = Math.max(15, Math.min(40, (scrollThreshold - distance) / 3));
                                scrollInterval = setInterval(() => {
                                    window.scrollBy({
                                        top: -speed,
                                        left: 0,
                                        behavior: 'auto'
                                    });
                                }, 16);
                            }
                        };
                        
                        document.addEventListener('mousemove', mouseMoveHandler, { passive: true });
                        document.addEventListener('touchmove', mouseMoveHandler, { passive: true });
                    },
                    onEnd: (evt) => {
                        // Удаляем обработчики движения мыши
                        if (mouseMoveHandler) {
                            document.removeEventListener('mousemove', mouseMoveHandler);
                            document.removeEventListener('touchmove', mouseMoveHandler);
                            mouseMoveHandler = null;
                        }
                        
                        // Останавливаем прокрутку
                        if (scrollInterval) {
                            clearInterval(scrollInterval);
                            scrollInterval = null;
                        }
                        
                        this.isDragging = false;
                        evt.item.classList.remove('sortable-dragging');
                        evt.item.style.pointerEvents = '';
                        console.log('Drag ended');
                        
                        const newOrder = [];
                        // Используем только прямые дочерние элементы контейнера
                        const allItems = Array.from(container.children).filter(child => 
                            child.hasAttribute('data-widget-key') && child.parentElement === container
                        );
                        
                        allItems.forEach((item) => {
                            const widgetKey = item.getAttribute('data-widget-key');
                            if (widgetKey) {
                                newOrder.push(widgetKey);
                            }
                        });
                        
                        console.log('New order (displayed):', newOrder);
                        console.log('Old order (full):', this.widgetOrder);
                        
                        if (newOrder.length > 0) {
                            // Сохраняем новый порядок (теперь все виджеты отображаются, так что порядок полный)
                            this.widgetOrder = newOrder;
                            this.saveWidgetOrder(newOrder);
                        }
                    }
                });
                
                console.log('Sortable initialized successfully');
            } catch (error) {
                console.error('Error initializing Sortable:', error);
            }
        }, 500);
    },
    async saveWidgetOrder(newOrder) {
        this.saving = true;
        try {
            const response = await fetch('{{ route('settings.dashboard.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    widgets: @js($widgets),
                    widget_order: newOrder
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('success', 'Порядок виджетов изменен и сохранен');
                // Не перезагружаем страницу - порядок уже обновлен через Sortable
            } else {
                this.showToast('error', data.message || 'Произошла ошибка при сохранении');
            }
        } catch (e) {
            this.showToast('error', 'Ошибка сети или сервера. Попробуйте еще раз.');
        } finally {
            this.saving = false;
        }
    },
    showToast(type, message) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-4 right-4 z-50 space-y-3';
            document.body.appendChild(container);
        }

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
        };

        const style = config[type] || config.success;
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
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
        
        this.$nextTick(() => {
            if (window.Alpine && window.Alpine.initTree) {
                window.Alpine.initTree(toast);
            }
        });
    },
    closeConfirmModal() {
        this.showConfirmModal = false;
        this.confirmAction = '';
        this.confirmMessage = '';
        this.appointmentId = null;
    },
    handleConfirm() {
        if (this.confirmAction === 'complete' && this.appointmentId) {
            const form = document.getElementById('complete-form-' + this.appointmentId);
            if (form) {
                form.submit();
            }
        } else if (this.confirmAction === 'cancel' && this.appointmentId) {
            const form = document.getElementById('cancel-form-' + this.appointmentId);
            if (form) {
                form.submit();
            }
        }
        this.closeConfirmModal();
    }
}" 
@open-confirm.window="openConfirmModal($event.detail.action, $event.detail.message, $event.detail.appointmentId)">
    
    <div class="space-y-6 md:space-y-8">
        <!-- Header Section -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">
                        Добро пожаловать, {{ auth()->user()->first_name ?? 'Мастер' }}!
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        {{ $business->name }} — {{ $lastUpdated->locale('ru')->isoFormat('D MMMM, HH:mm') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('dashboard.refresh') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-rotate text-xs"></i>
                            <span>Обновить</span>
                        </button>
                    </form>
                    <a href="{{ route('settings.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-gear text-xs"></i>
                        <span>Настройки</span>
                    </a>
                </div>
            </div>
        </div>
        
        @php
            // Виджеты, которые обрабатываются через WidgetGrid (отображаются в сетке)
            $gridWidgets = ['next_appointment', 'today_appointments', 'pending_appointments', 'recent_clients', 'weekly_chart'];
            // Флаг для отслеживания, был ли уже отображен блок графиков
            $chartsDisplayed = false;
        @endphp
        
        @php
            // Создаем простой список виджетов для отображения
            // Отображаем все виджеты из widgetOrder, даже если данных нет
            $displayWidgets = [];
            foreach ($widgetOrder as $widgetKey) {
                // Пропускаем только отключенные виджеты
                if (!isset($widgets[$widgetKey]) || !$widgets[$widgetKey]) {
                    continue;
                }
                $displayWidgets[] = $widgetKey;
            }
        @endphp
        
        {{-- Контейнер для сортировки виджетов --}}
        <div id="dashboard-widgets-container" class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 auto-rows-max">
        @foreach($displayWidgets as $widgetKey)
            @php
                $isGridWidget = in_array($widgetKey, $gridWidgets);
            @endphp
            <div data-widget-key="{{ $widgetKey }}" class="widget-item relative group {{ $isGridWidget ? 'widget-grid' : 'widget-full' }}" style="position: relative; {{ $widgetKey === 'stats_header' ? 'grid-column: 1 / -1;' : '' }}">
                <div class="drag-handle absolute top-2 right-2 z-[9999] cursor-move opacity-0 group-hover:opacity-100 transition-opacity bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm rounded-lg p-2.5 shadow-xl border-2 border-indigo-300 dark:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 pointer-events-auto flex items-center justify-center" style="top: 0.5rem; right: 0.5rem; width: 2.5rem; height: 2.5rem; min-width: 2.5rem; max-width: 2.5rem; flex-shrink: 0;">
                    <i class="fa-solid fa-grip-vertical text-indigo-600 dark:text-indigo-400 text-base"></i>
                </div>
                @switch($widgetKey)
                    @case('stats_header')
                        @include('dashboard.widgets.stats-header', ['widgets' => $widgets, 'stats' => $stats])
                        @break
                    @case('quick_actions')
                        @include('dashboard.widgets.quick-actions', ['widgets' => $widgets])
                        @break
                    @case('appointments_chart')
                    @case('clients_chart')
                        @if(!$chartsDisplayed)
                            @php $chartsDisplayed = true; @endphp
                            @include('dashboard.widgets.charts', ['widgets' => $widgets])
                        @endif
                        @break
                    @case('next_appointment')
                        @include('dashboard.widgets.next-appointment', ['appointments' => $appointments])
                        @break
                    @case('today_appointments')
                        @include('dashboard.widgets.today-appointments', ['appointments' => $appointments])
                        @break
                    @case('pending_appointments')
                        @include('dashboard.widgets.pending-appointments', ['appointments' => $appointments])
                        @break
                    @case('recent_clients')
                        @include('dashboard.widgets.recent-clients', ['clients' => $clients])
                        @break
                    @case('weekly_chart')
                        @include('dashboard.widgets.weekly-chart', ['business' => $business])
                        @break
                @endswitch
            </div>
        @endforeach
        </div>

    <!-- Модальное окно для номера телефона -->
    <div x-show="showModal" 
         @click.away="closeModal()"
         @keydown.escape.window="closeModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display: none;">
        <div @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                <button @click="closeModal()" 
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <div class="mb-4">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Клиент</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Телефон</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-300"></i>
                        </div>
                        <p class="text-xl font-bold text-slate-900 dark:text-white" x-text="phoneDisplay"></p>
                    </div>
                </div>
                <div class="space-y-2">
                    <a :href="`tel:${phone}`"
                        class="md:hidden w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors">
                        <i class="fa-solid fa-phone text-sm"></i>
                        <span>Позвонить</span>
                    </a>
                    <button @click="navigator.clipboard.writeText(phone); closeModal();"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 md:bg-slate-100 md:dark:bg-slate-800 px-4 py-3 text-sm font-medium text-white md:text-slate-700 md:dark:text-slate-300 hover:bg-indigo-700 md:hover:bg-slate-200 md:dark:hover:bg-slate-700 active:bg-indigo-800 transition-colors">
                        <i class="fa-regular fa-copy text-sm"></i>
                        <span>Копировать номер</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения -->
    <div x-show="showConfirmModal" 
         @click.away="closeConfirmModal()"
         @keydown.escape.window="closeConfirmModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display: none;">
        <div @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Подтверждение</h3>
                <button @click="closeConfirmModal()" 
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 mb-6" x-text="confirmMessage"></p>
                <div class="flex gap-3">
                    <button @click="closeConfirmModal()" 
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Нет
                    </button>
                    <button @click="handleConfirm()" 
                        :class="confirmAction === 'complete' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white'"
                        class="flex-1 px-4 py-2.5 rounded-lg font-medium transition-colors">
                        <span x-show="confirmAction === 'complete'">Подтвердить</span>
                        <span x-show="confirmAction === 'cancel'">Да, отменить</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? []);
        const widgets = @json($widgets ?? []);
        
        const isDarkMode = () => {
            return document.documentElement.classList.contains('dark');
        };

        const getThemeColors = () => {
            return {
                text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                grid: 'rgba(148, 163, 184, 0.1)'
            };
        };

        // График записей
        if (widgets.appointments_chart) {
            const appointmentsCtx = document.getElementById('appointmentsChart');
            const appointmentsEmpty = document.getElementById('appointmentsChartEmpty');
            if (appointmentsCtx) {
                if (chartData && chartData.labels) {
                    if (appointmentsEmpty) appointmentsEmpty.classList.add('hidden');
                    const colors = getThemeColors();
                    new Chart(appointmentsCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Записи',
                                data: chartData.appointments || [],
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        color: colors.text
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: colors.textSecondary
                                    },
                                    grid: {
                                        color: colors.grid
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: colors.textSecondary
                                    },
                                    grid: {
                                        color: colors.grid
                                    }
                                }
                            }
                        }
                    });
                } else {
                    if (appointmentsEmpty) appointmentsEmpty.classList.remove('hidden');
                }
            }
        }

        // График клиентов
        if (widgets.clients_chart) {
            const clientsCtx = document.getElementById('clientsChart');
            const clientsEmpty = document.getElementById('clientsChartEmpty');
            if (clientsCtx) {
                if (chartData && chartData.labels) {
                    if (clientsEmpty) clientsEmpty.classList.add('hidden');
                    const colors = getThemeColors();
                    new Chart(clientsCtx, {
                        type: 'bar',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Новые клиенты',
                                data: chartData.clients || [],
                                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1
                            }]
                        },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: colors.text
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textSecondary,
                                    stepSize: 1
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
                } else {
                    if (clientsEmpty) clientsEmpty.classList.remove('hidden');
                }
            }
        }

        // График по статусам
        if (widgets.appointments_chart) {
            const statusCtx = document.getElementById('statusChart');
            const statusEmpty = document.getElementById('statusChartEmpty');
            if (statusCtx) {
                if (chartData && chartData.completed && chartData.cancelled && chartData.labels) {
                    if (statusEmpty) statusEmpty.classList.add('hidden');
                    const colors = getThemeColors();
                    new Chart(statusCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: 'Завершено',
                                    data: chartData.completed || [],
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Отменено',
                                    data: chartData.cancelled || [],
                                    borderColor: 'rgb(244, 63, 94)',
                                    backgroundColor: 'rgba(244, 63, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: colors.text
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
                } else {
                    if (statusEmpty) statusEmpty.classList.remove('hidden');
                }
            }
        }

        // График активности по дням недели
        if (widgets.appointments_chart) {
            const weekdayCtx = document.getElementById('weekdayChart');
            const weekdayEmpty = document.getElementById('weekdayChartEmpty');
            if (weekdayCtx) {
                if (chartData && chartData.weekday_data && chartData.weekday_labels) {
                    if (weekdayEmpty) weekdayEmpty.classList.add('hidden');
                    const colors = getThemeColors();
                    new Chart(weekdayCtx, {
                        type: 'bar',
                        data: {
                            labels: chartData.weekday_labels,
                            datasets: [{
                                label: 'Записи',
                                data: chartData.weekday_data || [],
                                backgroundColor: [
                                    'rgba(99, 102, 241, 0.7)',
                                    'rgba(99, 102, 241, 0.7)',
                                    'rgba(99, 102, 241, 0.7)',
                                    'rgba(99, 102, 241, 0.7)',
                                    'rgba(99, 102, 241, 0.7)',
                                    'rgba(139, 92, 246, 0.7)',
                                    'rgba(139, 92, 246, 0.7)'
                                ],
                                borderColor: 'rgb(99, 102, 241)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textSecondary,
                                    stepSize: 1
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
                } else {
                    if (weekdayEmpty) weekdayEmpty.classList.remove('hidden');
                }
            }
        }
    });
</script>
@endpush

@endsection
