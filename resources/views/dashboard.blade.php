@extends('layouts.user')

@section('title', 'Главная - Cliently')
@section('page-title', 'Главная')
@section('page-description', 'Обзор вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
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
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Основной контент: Записи на сегодня -->
        <div class="lg:col-span-2 order-2 lg:order-1">
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm">
                <!-- Заголовок -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                            Записи на сегодня
                        </h2>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $todayDate }}</p>
                            @if($totalToday > 0)
                                <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ $completedCount }}/{{ $totalToday }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('appointments.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                        Все записи →
                    </a>
                </div>

                <!-- Список записей -->
                <div>
                    @if($upcomingAppointments->isNotEmpty())
                        @foreach($upcomingAppointments as $appointment)
                            <div class="p-4 border-b border-slate-200/60 dark:border-slate-700/60 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-base font-semibold text-slate-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                            </span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate">{{ $appointment->service->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                                            <span class="truncate font-medium">{{ $appointment->client->full_name }}</span>
                                            @if($appointment->master)
                                                <span class="text-slate-400 dark:text-slate-500">•</span>
                                                <span class="truncate">{{ trim($appointment->master->first_name . ' ' . $appointment->master->last_name) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button @click="openModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ $appointment->client->full_name }}')"
                                        class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open"
                                            class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                        </button>
                                        <div x-show="open" 
                                            @click.away="open = false" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200/60 dark:border-slate-700/60 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm shadow-xl z-10"
                                            style="display: none;">
                                            <a href="{{ route('appointments.show', $appointment->id) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-t-lg">
                                                <i class="fa-regular fa-eye w-4 inline-block"></i> Просмотр
                                            </a>
                                            @if($appointment->status === 'pending' || $appointment->status === 'confirmed')
                                                @if($appointment->status === 'confirmed')
                                                    <form method="POST" action="{{ route('appointments.complete', $appointment->id) }}" id="complete-form-{{ $appointment->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                    </form>
                                                    <button @click="open = false; $dispatch('open-confirm', { action: 'complete', message: 'Отметить запись как выполненную?', appointmentId: {{ $appointment->id }} })" class="w-full text-left px-4 py-2 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/20">
                                                        <i class="fa-solid fa-check w-4 inline-block"></i> Выполнено
                                                    </button>
                                                @endif
                                                <form method="POST" action="{{ route('appointments.cancel', $appointment->id) }}" id="cancel-form-{{ $appointment->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <button @click="open = false; $dispatch('open-confirm', { action: 'cancel', message: 'Вы уверены, что хотите отменить запись?', appointmentId: {{ $appointment->id }} })" class="w-full text-left px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 rounded-b-lg">
                                                    <i class="fa-solid fa-xmark w-4 inline-block"></i> Отменить
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    @if($completedAppointments->isNotEmpty())
                        <div class="px-5 pt-4 pb-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Выполненные</p>
                        </div>
                        @foreach($completedAppointments as $appointment)
                            <div class="p-4 border-b border-slate-200/60 dark:border-slate-700/60 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors opacity-70">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-base font-semibold text-slate-500 dark:text-slate-400 line-through">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                            </span>
                                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400 truncate">{{ $appointment->service->name }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                                Выполнено
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                            <span class="truncate font-medium">{{ $appointment->client->full_name }}</span>
                                            @if($appointment->master)
                                                <span class="text-slate-400 dark:text-slate-500">•</span>
                                                <span class="truncate">{{ trim($appointment->master->first_name . ' ' . $appointment->master->last_name) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ route('appointments.show', $appointment->id) }}" class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center">
                                            <i class="fa-regular fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($todayAppointments->isEmpty())
                        <div class="p-12 text-center">
                            <i class="fa-solid fa-calendar-xmark text-5xl text-slate-300 dark:text-slate-700 mb-4"></i>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-4">Нет записей на сегодня</p>
                            <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Создать запись</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Записи, требующие внимания -->
            @if($pendingAppointments->isNotEmpty())
                <div class="mt-6 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl border border-amber-200/60 dark:border-amber-800/60 shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-amber-200/60 dark:border-amber-800/60">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>
                            <span>Требуют внимания</span>
                            @if($pendingAppointments->count() > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300">
                                    {{ $pendingAppointments->count() }}
                                </span>
                            @endif
                        </h3>
                        <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                            Все →
                        </a>
                    </div>
                    <div>
                        @foreach($pendingAppointments as $appointment)
                            <a href="{{ route('appointments.show', $appointment->id) }}" class="block p-4 border-b border-amber-200/60 dark:border-amber-800/60 last:border-0 hover:bg-amber-50/30 dark:hover:bg-amber-900/10 transition-colors">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2.5 mb-1.5">
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $appointment->date->locale('ru')->isoFormat('D MMMM') }}
                                            </span>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate mb-1">{{ $appointment->service->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $appointment->client->full_name }}</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 flex-shrink-0"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Боковая панель: Следующая запись и Быстрые действия -->
        <div class="space-y-6 order-1 lg:order-2">
            <!-- Следующая запись -->
            @if($nextAppointment)
                <a href="{{ route('appointments.show', $nextAppointment->id) }}" class="block bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-500/10 dark:to-indigo-600/10 rounded-xl border border-indigo-200/60 dark:border-indigo-500/20 shadow-sm p-5 hover:shadow-md transition-all">
                    <h3 class="text-xs font-semibold text-indigo-700 dark:text-indigo-300 mb-3 uppercase tracking-wide">Следующая запись</h3>
                    <div class="space-y-2">
                        <p class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">
                            {{ \Carbon\Carbon::parse($nextAppointment->time)->format('H:i') }}
                        </p>
                        <p class="text-sm font-medium text-indigo-800 dark:text-indigo-200 truncate">{{ $nextAppointment->service->name }}</p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-300 truncate">{{ $nextAppointment->client->full_name }}</p>
                        @if($nextAppointment->master)
                            <p class="text-xs text-indigo-500 dark:text-indigo-400 truncate">{{ trim($nextAppointment->master->first_name . ' ' . $nextAppointment->master->last_name) }}</p>
                        @endif
                    </div>
                </a>
            @elseif($upcomingAppointments->isEmpty() && $completedAppointments->isNotEmpty())
                <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm p-5">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wide">Сегодня</h3>
                    <div class="space-y-1">
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            Все записи выполнены
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $completedAppointments->count() }} {{ $completedAppointments->count() === 1 ? 'запись' : 'записей' }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Быстрые действия -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm p-5">
                <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wide">Быстрые действия</h3>
                <div class="space-y-2">
                    <a href="{{ route('appointments.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Новая запись</span>
                    </a>
                    <a href="{{ route('clients.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Новый клиент</span>
                    </a>
                </div>
            </div>
        </div>
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
            class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm rounded-xl shadow-xl border border-slate-200/60 dark:border-slate-800/60 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                <button @click="closeModal()" 
                    class="h-8 w-8 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
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
            class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm rounded-xl shadow-xl border border-slate-200/60 dark:border-slate-800/60 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Подтверждение</h3>
                <button @click="closeConfirmModal()" 
                    class="h-8 w-8 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 mb-6" x-text="confirmMessage"></p>
                <div class="flex gap-3">
                    <button @click="closeConfirmModal()" 
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
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
