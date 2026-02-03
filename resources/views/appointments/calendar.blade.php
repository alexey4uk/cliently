@extends('layouts.user')

@section('title', 'Календарь записей - Cliently')
@section('page-title', 'Календарь записей')
@section('page-description', 'Календарный вид записей клиентов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Календарь записей', 'url' => null]]" />
@endpush

@section('content')

@php
    $hasActiveFilters = $date || $status || request('service_id') || request('master_id');
    $startOfMonth = $selectedDate->copy()->startOfMonth();
    $endOfMonth = $selectedDate->copy()->endOfMonth();
    $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon\Carbon::MONDAY);
    $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon\Carbon::SUNDAY);
    $daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    $monthNames = [
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
    ];
@endphp

<div class="max-w-[1800px] mx-auto">
    <div class="flex flex-col gap-3 sm:gap-4" x-data="{
    showFilters: {{ $hasActiveFilters ? 'true' : 'false' }},
    showDayModal: false,
    selectedDate: '',
    selectedDateAppointments: [],
    toggleFilters() {
        this.showFilters = !this.showFilters;
    },
    openDayModal(date, appointments) {
        this.selectedDate = date;
        this.selectedDateAppointments = appointments;
        this.showDayModal = true;
    },
    formatDate(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        const months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    },
    closeDayModal() {
        this.showDayModal = false;
    },
    createAppointment(date) {
        window.location.href = '{{ route('appointments.create') }}?date=' + date;
    }
}">

    <!-- Фильтры -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 max-h-0 -translate-y-4"
         x-transition:enter-end="opacity-100 max-h-[500px] translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 max-h-[500px] translate-y-0"
         x-transition:leave-end="opacity-0 max-h-0 -translate-y-4"
         class="shrink-0 overflow-hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-2.5 sm:p-4 md:p-6 shadow-sm mb-2.5 sm:mb-4">
        <form method="GET" action="{{ route('appointments.calendar') }}" class="space-y-2.5 sm:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
                <!-- Фильтр по дате -->
                <div class="space-y-1.5">
                    <label for="date-filter" class="block text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
                        Дата
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                        </div>
                        <input id="date-filter" type="date" name="date" value="{{ $date }}"
                            class="w-full pl-9 pr-2.5 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs sm:text-sm text-slate-900 dark:text-white"
                            onchange="this.form.submit()">
                    </div>
                </div>

                <!-- Фильтр по статусу -->
                <div class="space-y-1.5">
                    <label for="status-filter" class="block text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
                        Статус
                    </label>
                    <select name="status" id="status-filter"
                        class="w-full px-2.5 sm:px-3 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs sm:text-sm text-slate-900 dark:text-white"
                        onchange="this.form.submit()">
                        <option value="">Все статусы</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Подтвержденные</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидающие</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Завершенные</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                    </select>
                </div>

                <!-- Фильтр по услуге -->
                <div class="space-y-1.5">
                    <label for="service-filter" class="block text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
                        Услуга
                    </label>
                    <select name="service_id" id="service-filter"
                        class="w-full px-2.5 sm:px-3 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs sm:text-sm text-slate-900 dark:text-white"
                        onchange="this.form.submit()">
                        <option value="">Все услуги</option>
                        @foreach(\App\Models\Service::where('business_id', $business->id)->orderBy('name')->get() as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Фильтр по мастеру -->
                <div class="space-y-1.5">
                    <label for="master-filter" class="block text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
                        Мастер
                    </label>
                    <select name="master_id" id="master-filter"
                        class="w-full px-2.5 sm:px-3 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs sm:text-sm text-slate-900 dark:text-white"
                        onchange="this.form.submit()">
                        <option value="">Все мастера</option>
                        <option value="unassigned" {{ request('master_id') === 'unassigned' ? 'selected' : '' }}>Без мастера</option>
                        @foreach(\App\Models\Master::where('business_id', $business->id)->orderBy('first_name')->get() as $master)
                            <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                {{ $master->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Кнопки управления фильтрами -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 pt-1.5 sm:pt-2">
                <div class="flex items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('appointments.calendar') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-2.5 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                            <span>Сбросить</span>
                        </a>
                    @endif
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm hover:shadow">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    <span>Применить</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Календарное представление -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden flex flex-col sm:flex-1 sm:min-h-0 flex-1">
        <!-- Навигация по месяцам (на мобиле — липкая) -->
        <div class="sticky sm:static top-0 z-10 border-b border-slate-200 dark:border-slate-800 px-3 sm:px-4 py-3 sm:py-3 bg-white dark:bg-slate-900 shrink-0">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-3">
                <!-- Навигация по месяцам (слева) -->
                <div class="flex items-center justify-between sm:justify-start gap-2">
                    <!-- Кнопка назад -->
                    <a href="{{ route('appointments.calendar', array_merge([
                        'month' => $selectedDate->copy()->subMonth()->format('Y-m')
                    ], request()->only(['search', 'status', 'service_id', 'master_id', 'date']))) }}"
                        class="h-10 w-10 sm:h-7 sm:w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 transition-all duration-200 shrink-0 touch-manipulation"
                        aria-label="Предыдущий месяц">
                        <i class="fa-solid fa-chevron-left text-sm sm:text-xs"></i>
                    </a>

                    <!-- Текущий месяц и год (только на мобильных) -->
                    <div class="flex-1 sm:hidden text-center">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $monthNames[$selectedDate->month] }} {{ $selectedDate->year }}
                        </h2>
                    </div>

                    <!-- Поле выбора месяца (только на десктопе) -->
                    <div class="hidden sm:block">
                        <input type="month" name="month" value="{{ $currentMonth }}"
                            onchange="window.location.href = '{{ route('appointments.calendar') }}' + '?month=' + this.value + '{{ request()->has('search') ? '&search=' . urlencode(request('search')) : '' }}{{ request()->has('status') ? '&status=' . urlencode(request('status')) : '' }}{{ request()->has('service_id') ? '&service_id=' . urlencode(request('service_id')) : '' }}{{ request()->has('master_id') ? '&master_id=' . urlencode(request('master_id')) : '' }}{{ request()->has('date') ? '&date=' . urlencode(request('date')) : '' }}'"
                            class="px-2 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-slate-900 dark:text-white font-medium cursor-pointer min-w-[120px]">
                    </div>

                    <!-- Кнопка вперед -->
                    <a href="{{ route('appointments.calendar', array_merge([
                        'month' => $selectedDate->copy()->addMonth()->format('Y-m')
                    ], request()->only(['search', 'status', 'service_id', 'master_id', 'date']))) }}"
                        class="h-10 w-10 sm:h-7 sm:w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 transition-all duration-200 shrink-0 touch-manipulation"
                        aria-label="Следующий месяц">
                        <i class="fa-solid fa-chevron-right text-sm sm:text-xs"></i>
                    </a>
                </div>

                <!-- Кнопки управления (справа) -->
                <div class="grid grid-cols-4 sm:flex sm:items-center gap-2 sm:gap-1.5">
                    <!-- Кнопка фильтров -->
                    <button @click="toggleFilters()"
                            class="relative inline-flex items-center justify-center gap-1 min-h-[44px] sm:min-h-0 px-2 sm:px-2 py-2.5 sm:py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 rounded-lg transition-all duration-200 touch-manipulation">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span class="hidden lg:inline">Фильтры</span>
                        @if ($hasActiveFilters)
                            <span class="absolute top-1 right-1 sm:relative sm:top-0 sm:right-0 sm:ml-0.5 px-1.5 py-0.5 text-[10px] font-bold bg-indigo-500 text-white rounded-full">{{ 
                                ($date ? 1 : 0) + ($status ? 1 : 0) + (request('service_id') ? 1 : 0) + (request('master_id') ? 1 : 0)
                            }}</span>
                        @endif
                    </button>
                    <!-- Кнопка экспорта -->
                    <a href="{{ route('appointments.export', request()->query()) }}"
                        class="inline-flex items-center justify-center gap-1 min-h-[44px] sm:min-h-0 px-2 sm:px-2 py-2.5 sm:py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 rounded-lg transition-all duration-200 touch-manipulation relative"
                        title="Экспорт">
                        <i class="fa-solid fa-download text-xs"></i>
                        <span class="hidden lg:inline">Экспорт</span>
                    </a>
                    <!-- Кнопка создания -->
                    @php
                        $canCreateAppointment = false;
                        if (Auth::check()) {
                            $subscriptionService = app(\App\Services\SubscriptionService::class);
                            $canCreateAppointment = $subscriptionService->canCreateAppointment(Auth::user());
                        }
                    @endphp
                    @if($canCreateAppointment)
                        <a href="{{ route('appointments.create') }}"
                            class="inline-flex items-center justify-center gap-1 min-h-[44px] sm:min-h-0 px-2 sm:px-2 py-2.5 sm:py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 rounded-lg transition-all duration-200 touch-manipulation">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span class="hidden lg:inline">Создать</span>
                        </a>
                    @else
                        <button disabled
                            class="inline-flex items-center justify-center gap-1 min-h-[44px] sm:min-h-0 px-2 sm:px-2 py-2.5 sm:py-1.5 text-xs font-semibold text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed"
                            title="Достигнут месячный лимит записей для вашего тарифа. Обновите тариф для увеличения лимита.">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span class="hidden lg:inline">Создать</span>
                        </button>
                    @endif
                    <!-- Кнопка "Сегодня" -->
                    <button onclick="window.location.href='{{ route('appointments.calendar', array_merge(['month' => \Carbon\Carbon::now()->format('Y-m')], request()->only(['search', 'status', 'service_id', 'master_id', 'date']))) }}'"
                        class="min-h-[44px] sm:min-h-0 px-2 sm:px-2 py-2.5 sm:py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 rounded-lg transition-all touch-manipulation">
                        <i class="fa-solid fa-calendar-day text-xs"></i>
                        <span class="hidden lg:inline ml-1">Сегодня</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Календарь -->
        <div class="flex flex-col sm:flex-1 sm:min-h-0 sm:overflow-y-auto">
            <!-- Дни недели -->
            <div class="grid grid-cols-7 border-b border-slate-200 dark:border-slate-800 shrink-0">
                @foreach ($daysOfWeek as $index => $day)
                    <div class="p-1.5 sm:p-2 md:p-3 text-center border-r border-slate-200 dark:border-slate-800 last:border-r-0">
                        <span class="text-[10px] sm:text-[10px] md:text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide {{ $index >= 5 ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                            {{ $day }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Календарная сетка -->
            <div class="grid grid-cols-7 sm:flex-1 sm:min-h-0 gap-0 [grid-auto-rows:48px] sm:[grid-auto-rows:minmax(0,1fr)]">
                @for ($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay())
                    @php
                        $dateKey = $date->format('Y-m-d');
                        $dayAppointments = $appointmentsByDate->get($dateKey, collect());
                        $isCurrentMonth = $date->month === $selectedDate->month;
                        $isToday = $date->isToday();
                        $isWeekend = $date->isWeekend();
                        $tableParams = array_merge([
                            'date' => $date->format('Y-m-d'),
                        ], request()->only(['search', 'status', 'service_id', 'master_id']));
                    @endphp
                    @php
                        $isPast = $date->isPast() && !$date->isToday();
                        $hasAppointments = $dayAppointments->count() > 0;
                        $dayData = [
                            'date' => $date->format('Y-m-d'),
                            'appointments' => $dayAppointments->map(function($apt) {
                                return [
                                    'id' => $apt->id,
                                    'time' => \Carbon\Carbon::parse($apt->time)->format('H:i'),
                                    'client' => $apt->client->full_name,
                                    'service' => $apt->service->name,
                                    'master' => $apt->master ? $apt->master->name : null,
                                    'status' => $apt->status,
                                    'url' => route('appointments.show', $apt)
                                ];
                            })->toArray()
                        ];
                    @endphp
                    <div class="relative min-h-[48px] sm:min-h-0 h-[48px] sm:h-full cursor-pointer transition-colors border-b border-r border-slate-200 dark:border-slate-800 last:border-r-0 active:bg-slate-100 dark:active:bg-slate-700/50 touch-manipulation
                        {{ $isCurrentMonth ? ($isPast ? 'bg-slate-50/50 dark:bg-slate-800/20' : 'bg-white dark:bg-slate-800/50') : 'bg-slate-50 dark:bg-slate-900/30' }}
                        {{ $isToday ? 'bg-blue-50/50 dark:bg-blue-900/10 [box-shadow:inset_0_0_0_1px_rgba(147,197,253,0.5)] dark:[box-shadow:inset_0_0_0_1px_rgba(59,130,246,0.5)]' : '' }}"
                        hover:bg-slate-50 dark:hover:bg-slate-800/70"
                        @if ($hasAppointments)
                            @click="openDayModal('{{ $date->format('Y-m-d') }}', @js($dayData['appointments']))"
                        @else
                            @click="createAppointment('{{ $date->format('Y-m-d') }}')"
                        @endif>
                        <div class="h-full w-full flex flex-col p-0.5 sm:p-1.5 sm:p-2 md:p-2.5 min-h-0">
                            <!-- Заголовок дня -->
                            <div class="flex items-center justify-between gap-0.5 sm:mb-1.5 shrink-0">
                                <span class="text-[11px] sm:text-xs md:text-sm lg:text-base font-medium flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 transition-colors
                                    {{ $isCurrentMonth ? ($isToday ? 'text-blue-600 dark:text-blue-400 font-semibold' : ($isWeekend ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white')) : 'text-slate-400 dark:text-slate-500' }}">
                                    {{ $date->day }}
                                </span>
                                @if ($dayAppointments->count() > 0)
                                    <!-- Мобильная версия: видимый счётчик записей -->
                                    <span class="sm:hidden flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 shrink-0"
                                        title="{{ $dayAppointments->count() }} {{ $dayAppointments->count() === 1 ? 'запись' : ($dayAppointments->count() < 5 ? 'записи' : 'записей') }}">
                                        {{ $dayAppointments->count() }}
                                    </span>
                                    <!-- Планшетная и десктопная версия: минимальный счетчик -->
                                    <span class="hidden sm:inline text-[10px] md:text-xs text-slate-500 dark:text-slate-400 font-medium shrink-0"
                                        title="{{ $dayAppointments->count() }} {{ $dayAppointments->count() === 1 ? 'запись' : ($dayAppointments->count() < 5 ? 'записи' : 'записей') }}">
                                        {{ $dayAppointments->count() }}
                                    </span>
                                @endif
                            </div>

                            <!-- Записи (планшеты и десктоп) -->
                            <div class="hidden md:flex flex-1 flex-col space-y-1.5 overflow-y-auto min-h-0">
                                @php
                                    $tabletCount = 2; // Планшеты: 2 записи
                                    $desktopCount = 3; // Десктоп: 3 записи
                                @endphp
                                {{-- Планшеты: показываем 2 записи в одну строку --}}
                                <div class="md:block lg:hidden space-y-0.5">
                                    @foreach ($dayAppointments->take($tabletCount) as $appointment)
                                        <a href="{{ route('appointments.show', $appointment) }}" @click.stop
                                            class="group block px-1.5 py-0.5 rounded text-[10px] font-medium transition-all hover:shadow-sm shrink-0
                                            {{ $appointment->status === 'completed' 
                                                ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 border-l-2 border-emerald-500' 
                                                : ($appointment->status === 'cancelled' 
                                                    ? 'bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300 border-l-2 border-rose-500' 
                                                    : ($appointment->status === 'confirmed' 
                                                        ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border-l-2 border-blue-500' 
                                                        : 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 border-l-2 border-amber-500')) }}"
                                            title="{{ $appointment->client->full_name }} - {{ $appointment->service->name }}">
                                            <div class="flex items-center gap-1 whitespace-nowrap">
                                                <span class="font-semibold text-[10px] shrink-0">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                                                <span class="truncate text-[10px]">{{ $appointment->client->first_name }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                    @if ($dayAppointments->count() > $tabletCount)
                                        <button @click.stop="openDayModal('{{ $date->format('Y-m-d') }}', @js($dayData['appointments']))"
                                            class="block w-full text-[10px] text-indigo-600 dark:text-indigo-400 font-medium px-1.5 py-0.5 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors shrink-0 text-left">
                                            +{{ $dayAppointments->count() - $tabletCount }} еще
                                        </button>
                                    @endif
                                </div>
                                {{-- Десктоп: показываем 3 записи в одну строку --}}
                                <div class="hidden lg:block space-y-0.5">
                                    @foreach ($dayAppointments->take($desktopCount) as $appointment)
                                        <a href="{{ route('appointments.show', $appointment) }}" @click.stop
                                            class="group block px-1.5 py-0.5 rounded text-xs font-medium transition-all hover:shadow-sm shrink-0
                                            {{ $appointment->status === 'completed' 
                                                ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 border-l-2 border-emerald-500' 
                                                : ($appointment->status === 'cancelled' 
                                                    ? 'bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300 border-l-2 border-rose-500' 
                                                    : ($appointment->status === 'confirmed' 
                                                        ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border-l-2 border-blue-500' 
                                                        : 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 border-l-2 border-amber-500')) }}"
                                            title="{{ $appointment->client->full_name }} - {{ $appointment->service->name }}@if($appointment->master) ({{ $appointment->master->name }})@endif">
                                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                                <span class="font-semibold text-xs shrink-0">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                                                <span class="truncate text-xs">{{ $appointment->client->first_name }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                    @if ($dayAppointments->count() > $desktopCount)
                                        <button @click.stop="openDayModal('{{ $date->format('Y-m-d') }}', @js($dayData['appointments']))"
                                            class="block w-full text-xs text-indigo-600 dark:text-indigo-400 font-medium px-1.5 py-0.5 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors shrink-0 text-left">
                                            +{{ $dayAppointments->count() - $desktopCount }} еще
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Легенда статусов -->
        <div class="shrink-0 px-3 sm:px-6 py-2 sm:py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <span class="text-[9px] sm:text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Статусы:</span>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded bg-amber-500"></div>
                    <span class="text-[9px] sm:text-xs text-slate-600 dark:text-slate-400">Ожидает</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded bg-blue-500"></div>
                    <span class="text-[9px] sm:text-xs text-slate-600 dark:text-slate-400">Подтверждена</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded bg-emerald-500"></div>
                    <span class="text-[9px] sm:text-xs text-slate-600 dark:text-slate-400">Завершена</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded bg-rose-500"></div>
                    <span class="text-[9px] sm:text-xs text-slate-600 dark:text-slate-400">Отменена</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно записей дня (на мобиле — почти на весь экран) -->
    <div x-show="showDayModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4"
         @click.self="closeDayModal()"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-t-2xl sm:rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] sm:max-h-[90vh] overflow-hidden flex flex-col w-full sm:max-w-2xl">
            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Записи на день</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1" x-text="formatDate(selectedDate)"></p>
                </div>
                <button @click="closeDayModal()" class="min-w-[44px] min-h-[44px] flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors touch-manipulation -mr-2"
                    aria-label="Закрыть">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-3 sm:p-6">
                <template x-if="selectedDateAppointments.length === 0">
                    <div class="text-center py-8 sm:py-12">
                        <i class="fa-solid fa-calendar-xmark text-3xl sm:text-4xl text-slate-300 dark:text-slate-600 mb-3 sm:mb-4"></i>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Нет записей на этот день</p>
                        <button @click="createAppointment(selectedDate); closeDayModal();" 
                            class="mt-3 sm:mt-4 inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Создать запись</span>
                        </button>
                    </div>
                </template>
                <div class="space-y-2 sm:space-y-3" x-show="selectedDateAppointments.length > 0">
                    <template x-for="appointment in selectedDateAppointments" :key="appointment.id">
                        <a :href="appointment.url" 
                           class="block p-3 sm:p-4 rounded-lg border-2 transition-all hover:shadow-lg hover:scale-[1.01] sm:hover:scale-[1.02]"
                           :class="{
                               'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800': appointment.status === 'completed',
                               'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800': appointment.status === 'cancelled',
                               'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': appointment.status === 'confirmed',
                               'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800': appointment.status === 'pending'
                           }">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-1.5 sm:mb-2">
                                        <span class="text-base sm:text-lg font-bold text-slate-900 dark:text-white" x-text="appointment.time"></span>
                                        <span class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white truncate" x-text="appointment.client"></span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-1 truncate" x-text="appointment.service"></p>
                                    <template x-if="appointment.master">
                                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-500 truncate">
                                            <i class="fa-solid fa-user-tie mr-1 text-[9px]"></i>
                                            <span x-text="appointment.master"></span>
                                        </p>
                                    </template>
                                </div>
                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-xs font-medium rounded-full shrink-0"
                                    :class="{
                                        'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300': appointment.status === 'completed',
                                        'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300': appointment.status === 'cancelled',
                                        'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300': appointment.status === 'confirmed',
                                        'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300': appointment.status === 'pending'
                                    }"
                                    x-text="appointment.status === 'completed' ? 'Завершена' : (appointment.status === 'cancelled' ? 'Отменена' : (appointment.status === 'confirmed' ? 'Подтверждена' : 'Ожидает'))"></span>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
            <div class="p-3 sm:p-6 border-t border-slate-200 dark:border-slate-800 shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-0">
                <a :href="'{{ route('appointments.index') }}?date=' + selectedDate" 
                   class="text-xs sm:text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium text-center sm:text-left">
                    <i class="fa-solid fa-list mr-1"></i>
                    Все записи дня
                </a>
                <button @click="createAppointment(selectedDate); closeDayModal();" 
                    class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Создать запись</span>
                </button>
            </div>
        </div>
    </div>
    </div>

</div>

@endsection
