@extends('layouts.user')

@section('title', 'Главная - Cliently')
@section('page-title', 'Главная')
@section('page-description', 'Обзор вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
    
    <div class="space-y-4 md:space-y-6">
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
            
            <!-- Общая статистика -->
            @if($widgets['stats_header'])
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 mt-4 md:mt-6">
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 p-3 md:p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-calendar-day text-indigo-600 dark:text-indigo-400 text-xs"></i>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Сегодня</span>
                        </div>
                        <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['today'] }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 p-3 md:p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-xs"></i>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">За неделю</span>
                        </div>
                        <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['completed_week'] }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 p-3 md:p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="h-8 w-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-user-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Новые клиенты</span>
                        </div>
                        <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['new_clients_month'] }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        @if($widgets['quick_actions'])
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-3 md:mb-4 uppercase tracking-wide">Быстрые действия</h3>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <a href="{{ route('appointments.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Новая запись</span>
                    </a>
                    <a href="{{ route('clients.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Новый клиент</span>
                    </a>
                    <a href="{{ route('appointments.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-calendar text-xs"></i>
                        <span>Календарь</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Widget Grid -->
        <livewire:dashboard.widget-grid 
            :widgets="$widgets" 
            :widgetOrder="$widgetOrder" 
            :appointments="$appointments" 
            :clients="$clients" 
            :stats="$stats" 
            :business="$business"
        />
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

@endsection
