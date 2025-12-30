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

    <div class="space-y-4 md:space-y-6">
        <!-- Статистика -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <!-- Всего записей сегодня -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
                <div class="h-8 w-8 md:h-10 md:w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mb-2 md:mb-3">
                    <i class="fa-solid fa-calendar-day text-indigo-600 dark:text-indigo-400 text-sm md:text-base"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1">Записей сегодня</p>
                    <p class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $totalToday }}</p>
                </div>
            </div>

            <!-- Выполнено -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
                <div class="h-8 w-8 md:h-10 md:w-10 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-2 md:mb-3">
                    <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-sm md:text-base"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1">Выполнено</p>
                    <p class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $completedCount }}</p>
                </div>
            </div>

            <!-- Предстоящие -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
                <div class="h-8 w-8 md:h-10 md:w-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center mb-2 md:mb-3">
                    <i class="fa-solid fa-clock text-blue-600 dark:text-blue-400 text-sm md:text-base"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1">Предстоящие</p>
                    <p class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $upcomingAppointments->count() }}</p>
                </div>
            </div>

            <!-- Требуют внимания -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
                <div class="h-8 w-8 md:h-10 md:w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mb-2 md:mb-3">
                    <i class="fa-solid fa-exclamation-circle text-amber-600 dark:text-amber-400 text-sm md:text-base"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1">Требуют внимания</p>
                    <p class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $pendingAppointments->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
            <!-- Левая колонка: Записи на сегодня -->
            <div class="lg:col-span-2 space-y-4 md:space-y-6 order-2 lg:order-1">
                <!-- Записи на сегодня -->
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">
                                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                        Записи на сегодня
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                        {{ $todayDate }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('appointments.index') }}" 
                               class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors flex-shrink-0 whitespace-nowrap">
                                <span class="hidden sm:inline">Все записи</span>
                                <span class="sm:hidden">Все</span>
                                <span class="hidden sm:inline"> →</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        @php
                            // Если есть следующая запись и она единственная, показываем её в основном списке
                            $appointmentsToShow = $upcomingAppointments;
                            if ($nextAppointment && $upcomingAppointments->isEmpty() && $completedAppointments->isEmpty()) {
                                $appointmentsToShow = collect([$nextAppointment]);
                            }
                        @endphp
                        @if($appointmentsToShow->isNotEmpty())
                            @foreach($appointmentsToShow as $appointment)
                                <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <div class="flex items-center justify-between gap-2 md:gap-4">
                                        <div class="flex items-start gap-2 md:gap-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <div class="h-10 w-10 md:h-12 md:w-12 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                                    <span class="text-sm md:text-base font-bold text-indigo-600 dark:text-indigo-400">
                                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1 truncate">
                                                    {{ $appointment->service->name }}
                                                </h4>
                                                <div class="flex items-center gap-1.5 md:gap-2 text-xs text-slate-600 dark:text-slate-400 mb-0.5 md:mb-1">
                                                    <i class="fa-solid fa-user text-slate-400 text-[10px] md:text-xs"></i>
                                                    <span class="truncate font-medium">{{ $appointment->client->full_name }}</span>
                                                </div>
                                                @if($appointment->master)
                                                    <div class="flex items-center gap-1.5 md:gap-2 text-xs text-slate-500 dark:text-slate-500">
                                                        <i class="fa-solid fa-user-tie text-slate-400 text-[10px] md:text-xs"></i>
                                                        <span class="truncate">{{ trim($appointment->master->first_name . ' ' . $appointment->master->last_name) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 md:gap-1.5 flex-shrink-0">
                                            <button @click="openModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ $appointment->client->full_name }}')"
                                                class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center"
                                                title="Контакт">
                                                <i class="fa-solid fa-phone text-xs"></i>
                                            </button>
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open"
                                                    class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center"
                                                    title="Действия">
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
                                                    class="fixed sm:absolute right-4 sm:right-0 mt-2 w-[calc(100vw-2rem)] sm:w-48 max-w-xs sm:max-w-none rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl z-50"
                                                    style="display: none;"
                                                    x-init="
                                                        $watch('open', value => {
                                                            if (value) {
                                                                $nextTick(() => {
                                                                    const button = $el.previousElementSibling;
                                                                    if (button && window.innerWidth < 640) {
                                                                        const buttonRect = button.getBoundingClientRect();
                                                                        $el.style.top = (buttonRect.bottom + 8) + 'px';
                                                                        $el.style.right = (window.innerWidth - buttonRect.right) + 'px';
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    ">
                                                    <a href="{{ route('appointments.show', $appointment->id) }}" 
                                                       class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-t-lg transition-colors">
                                                        <i class="fa-regular fa-eye w-4 inline-block"></i> Просмотр
                                                    </a>
                                                    @if($appointment->status === 'pending' || $appointment->status === 'confirmed')
                                                        @if($appointment->status === 'confirmed')
                                                            <form method="POST" action="{{ route('appointments.complete', $appointment->id) }}" id="complete-form-{{ $appointment->id }}">
                                                                @csrf
                                                                @method('PATCH')
                                                            </form>
                                                            <button @click="open = false; $dispatch('open-confirm', { action: 'complete', message: 'Отметить запись как выполненную?', appointmentId: {{ $appointment->id }} })" 
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 transition-colors">
                                                                <i class="fa-solid fa-check w-4 inline-block"></i> Выполнено
                                                            </button>
                                                        @endif
                                                        <form method="POST" action="{{ route('appointments.cancel', $appointment->id) }}" id="cancel-form-{{ $appointment->id }}">
                                                            @csrf
                                                            @method('PATCH')
                                                        </form>
                                                        <button @click="open = false; $dispatch('open-confirm', { action: 'cancel', message: 'Вы уверены, что хотите отменить запись?', appointmentId: {{ $appointment->id }} })" 
                                                                class="w-full text-left px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 rounded-b-lg transition-colors">
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
                            <div class="px-4 md:px-6 pt-3 md:pt-4 pb-2 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Выполненные</p>
                            </div>
                            @foreach($completedAppointments as $appointment)
                                <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors opacity-75">
                                    <div class="flex items-center justify-between gap-2 md:gap-4">
                                        <div class="flex items-start gap-2 md:gap-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <div class="h-10 w-10 md:h-12 md:w-12 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                    <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs md:text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5 md:gap-2 mb-0.5 md:mb-1 flex-wrap">
                                                    <span class="text-xs md:text-sm font-semibold text-slate-500 dark:text-slate-400 line-through">
                                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 md:px-2 py-0.5 rounded text-[10px] md:text-xs font-medium bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                                        Выполнено
                                                    </span>
                                                </div>
                                                <h4 class="text-xs md:text-sm font-medium text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1 truncate">
                                                    {{ $appointment->service->name }}
                                                </h4>
                                                <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 truncate">
                                                    {{ $appointment->client->full_name }}
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('appointments.show', $appointment->id) }}" 
                                           class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center flex-shrink-0"
                                           title="Просмотр">
                                            <i class="fa-regular fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($appointmentsToShow->isEmpty() && $completedAppointments->isEmpty())
                            <div class="p-8 md:p-12 text-center">
                                <div class="h-12 w-12 md:h-16 md:w-16 rounded-full bg-slate-100 dark:bg-slate-800 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-xmark text-xl md:text-2xl text-slate-400 dark:text-slate-600"></i>
                                </div>
                                <p class="text-xs md:text-sm font-medium text-slate-500 dark:text-slate-400 mb-3 md:mb-4">Нет записей на сегодня</p>
                                <a href="{{ route('appointments.create') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Создать запись</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Записи, требующие внимания -->
                @if($pendingAppointments->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-lg border border-amber-200 dark:border-amber-800/50 shadow-sm overflow-hidden">
                        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-amber-200 dark:border-amber-800/50 bg-amber-50 dark:bg-amber-950/20">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">
                                    <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-exclamation-circle text-amber-600 dark:text-amber-400 text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                            Требуют внимания
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                            Записи ожидают подтверждения
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-[10px] md:text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 flex-shrink-0">
                                    {{ $pendingAppointments->count() }}
                                </span>
                            </div>
                        </div>
                        <div>
                            @foreach($pendingAppointments as $appointment)
                                <a href="{{ route('appointments.show', $appointment->id) }}" 
                                   class="block p-3 md:p-4 border-b border-amber-200 dark:border-amber-800/50 last:border-0 hover:bg-amber-50/30 dark:hover:bg-amber-900/10 transition-colors">
                                    <div class="flex items-center justify-between gap-2 md:gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 md:gap-2.5 mb-1 md:mb-1.5 flex-wrap">
                                                <span class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $appointment->date->locale('ru')->isoFormat('D MMMM') }}
                                                </span>
                                                <span class="text-xs md:text-sm font-semibold text-slate-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 truncate mb-0.5 md:mb-1">
                                                {{ $appointment->service->name }}
                                            </p>
                                            <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 truncate">
                                                {{ $appointment->client->full_name }}
                                            </p>
                                        </div>
                                        <i class="fa-solid fa-chevron-right text-xs text-slate-400 flex-shrink-0"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Правая колонка: Следующая запись и Быстрые действия -->
            <div class="space-y-4 md:space-y-6 order-1 lg:order-2">
                <!-- Следующая запись -->
                @php
                    // Показываем следующую запись в боковой панели только если есть еще записи в основном списке
                    $showNextInSidebar = $nextAppointment && ($upcomingAppointments->isNotEmpty() || $completedAppointments->isNotEmpty());
                @endphp
                @if($showNextInSidebar)
                    <a href="{{ route('appointments.show', $nextAppointment->id) }}" 
                       class="block bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-500/10 dark:to-indigo-600/10 rounded-lg border border-indigo-200 dark:border-indigo-500/20 shadow-sm p-3 md:p-4 lg:p-5 hover:shadow-md transition-all">
                        <div class="flex items-center gap-2 mb-2 md:mb-3">
                            <div class="h-8 w-8 rounded-lg bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clock text-white text-xs"></i>
                            </div>
                            <h3 class="text-xs font-semibold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide">
                                Следующая запись
                            </h3>
                        </div>
                        <div class="space-y-1.5 md:space-y-2">
                            <p class="text-xl md:text-2xl font-bold text-indigo-900 dark:text-indigo-100">
                                {{ \Carbon\Carbon::parse($nextAppointment->time)->format('H:i') }}
                            </p>
                            <p class="text-xs md:text-sm font-medium text-indigo-800 dark:text-indigo-200 truncate">
                                {{ $nextAppointment->service->name }}
                            </p>
                            <p class="text-[10px] md:text-xs text-indigo-600 dark:text-indigo-300 truncate">
                                {{ $nextAppointment->client->full_name }}
                            </p>
                            @if($nextAppointment->master)
                                <p class="text-[10px] md:text-xs text-indigo-500 dark:text-indigo-400 truncate">
                                    {{ trim($nextAppointment->master->first_name . ' ' . $nextAppointment->master->last_name) }}
                                </p>
                            @endif
                        </div>
                    </a>
                @elseif($appointmentsToShow->isEmpty() && $completedAppointments->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
                        <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 md:mb-3 uppercase tracking-wide">Сегодня</h3>
                        <div class="space-y-1">
                            <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                                Все записи выполнены
                            </p>
                            <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400">
                                {{ $completedAppointments->count() }} {{ $completedAppointments->count() === 1 ? 'запись' : 'записей' }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Быстрые действия -->
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 md:mb-3 uppercase tracking-wide">Быстрые действия</h3>
                    <div class="space-y-2">
                        <a href="{{ route('appointments.create') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 bg-indigo-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Новая запись</span>
                        </a>
                        <a href="{{ route('clients.create') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs md:text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-user-plus text-xs"></i>
                            <span>Новый клиент</span>
                        </a>
                    </div>
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
