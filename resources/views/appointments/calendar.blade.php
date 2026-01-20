@extends('layouts.user')

@section('title', 'Календарь записей - Cliently')
@section('page-title', 'Календарь записей')
@section('page-description', 'Календарный вид записей клиентов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Календарь записей', 'url' => null]]" />
@endpush

@section('content')

<!-- Flash сообщения -->
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="mb-6 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg p-4 flex items-center gap-3">
        <div class="flex-shrink-0">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
        </div>
        <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        <button @click="show = false"
            class="ml-auto flex-shrink-0 text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="mb-6 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-4 flex items-center gap-3">
        <div class="flex-shrink-0">
            <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400"></i>
        </div>
        <p class="text-sm font-medium text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
        <button @click="show = false"
            class="ml-auto flex-shrink-0 text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<div class="space-y-4 md:space-y-6" x-data="{
    showPhoneModal: false,
    phone: '',
    phoneDisplay: '',
    client: '',
    showFilters: {{ $date || $status || request('service_id') || request('master_id') ? 'true' : 'false' }},
    openPhoneModal(phone, phoneDisplay, client) {
        this.phone = phone;
        this.phoneDisplay = phoneDisplay;
        this.client = client;
        this.showPhoneModal = true;
    },
    closePhoneModal() {
        this.showPhoneModal = false;
    },
    toggleFilters() {
        this.showFilters = !this.showFilters;
    }
}">

    <!-- Заголовок страницы -->
    <div class="flex flex-col gap-6">
        <!-- Заголовок -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white">
                    Календарь записей
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Календарный вид всех записей клиентов
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Фильтры -->
                <button @click="toggleFilters()"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Фильтры</span>
                </button>
                <!-- Кнопка экспорта -->
                <a href="{{ route('appointments.export', request()->query()) }}"
                    class="inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span class="hidden sm:inline">Экспорт</span>
                </a>
                <a href="{{ route('appointments.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 md:px-5 py-2.5 md:py-3 text-xs md:text-sm font-semibold text-white bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transform hover:scale-105">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Создать запись</span>
                </a>
            </div>
        </div>

        <!-- Фильтры -->
        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96"
             x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <form method="GET" action="{{ route('appointments.calendar') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Фильтр по дате -->
                        <div class="space-y-2">
                            <label for="date-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Дата
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                </div>
                                <input id="date-filter" type="date" name="date" value="{{ $date }}"
                                    class="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                    onchange="this.form.submit()">
                            </div>
                        </div>

                        <!-- Фильтр по статусу -->
                        <div class="space-y-2">
                            <label for="status-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Статус
                            </label>
                            <select name="status" id="status-filter"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="">Все статусы</option>
                                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Подтвержденные</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидающие</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Завершенные</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                            </select>
                        </div>

                        <!-- Фильтр по услуге -->
                        <div class="space-y-2">
                            <label for="service-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Услуга
                            </label>
                            <select name="service_id" id="service-filter"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
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
                        <div class="space-y-2">
                            <label for="master-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Мастер
                            </label>
                            <select name="master_id" id="master-filter"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="">Все мастера</option>
                                @foreach(\App\Models\Master::where('business_id', $business->id)->orderBy('first_name')->get() as $master)
                                    <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                        {{ $master->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Кнопки управления фильтрами -->
                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center gap-2">
                            @if ($date || $status || request('service_id') || request('master_id'))
                                <a href="{{ route('appointments.calendar') }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                    <span>Сбросить фильтры</span>
                                </a>
                            @endif
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            <span>Применить</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Календарное представление -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
        <!-- Навигация по месяцам -->
        <div class="bg-gradient-to-r from-indigo-500 via-indigo-600 to-purple-600 dark:from-indigo-950/50 dark:via-indigo-900/30 dark:to-purple-900/30 border-b border-indigo-200 dark:border-indigo-800/50 px-3 md:px-6 py-4 md:py-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 md:gap-4">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Кнопка назад -->
                    <a href="{{ route('appointments.calendar', [
                        'month' => $selectedDate->copy()->subMonth()->format('Y-m'),
                        'search' => request('search'),
                        'status' => request('status'),
                        'service_id' => request('service_id'),
                        'master_id' => request('master_id')
                    ]) }}"
                        class="h-10 w-10 md:h-12 md:w-12 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-300 bg-gradient-to-r from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 border border-slate-300 dark:border-slate-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 dark:hover:from-indigo-900/50 dark:hover:to-indigo-800/50 hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-lg hover:shadow-xl flex-shrink-0 transform hover:scale-110">
                        <i class="fa-solid fa-chevron-left text-sm md:text-base"></i>
                    </a>

                    <!-- Поле выбора месяца -->
                    <div class="flex-1 sm:flex-initial">
                        <input type="month" name="month" value="{{ $currentMonth }}"
                            onchange="window.location.href = '{{ route('appointments.calendar') }}' + '?month=' + this.value + '{{ request()->has('search') ? '&search=' . urlencode(request('search')) : '' }}{{ request()->has('status') ? '&status=' . urlencode(request('status')) : '' }}{{ request()->has('service_id') ? '&service_id=' . urlencode(request('service_id')) : '' }}{{ request()->has('master_id') ? '&master_id=' . urlencode(request('master_id')) : '' }}'"
                            class="w-full sm:w-auto px-4 md:px-5 py-2.5 md:py-3 text-xs md:text-sm bg-gradient-to-r from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-slate-900 dark:text-white font-semibold cursor-pointer shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-xl">
                    </div>

                    <!-- Кнопка вперед -->
                    <a href="{{ route('appointments.calendar', [
                        'month' => $selectedDate->copy()->addMonth()->format('Y-m'),
                        'search' => request('search'),
                        'status' => request('status'),
                        'service_id' => request('service_id'),
                        'master_id' => request('master_id')
                    ]) }}"
                        class="h-10 w-10 md:h-12 md:w-12 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-300 bg-gradient-to-r from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 border border-slate-300 dark:border-slate-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 dark:hover:from-indigo-900/50 dark:hover:to-indigo-800/50 hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-lg hover:shadow-xl flex-shrink-0 transform hover:scale-110">
                        <i class="fa-solid fa-chevron-right text-sm md:text-base"></i>
                    </a>
                </div>
                <button onclick="window.location.href='{{ route('appointments.calendar', ['month' => \Carbon\Carbon::now()->format('Y-m'), 'search' => request('search'), 'status' => request('status'), 'service_id' => request('service_id'), 'master_id' => request('master_id')]) }}'"
                    class="w-full sm:w-auto px-4 md:px-5 py-2.5 md:py-3 text-xs md:text-sm font-semibold text-indigo-700 dark:text-indigo-300 bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/50 dark:to-indigo-800/30 border border-indigo-300 dark:border-indigo-700 rounded-xl hover:bg-gradient-to-r hover:from-indigo-100 hover:to-indigo-200 dark:hover:from-indigo-800/50 dark:hover:to-indigo-700/50 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fa-solid fa-calendar-day mr-2 md:mr-3"></i>
                    <span>Сегодня</span>
                </button>
            </div>
        </div>

        <!-- Календарь -->
        @php
            $startOfMonth = $selectedDate->copy()->startOfMonth();
            $endOfMonth = $selectedDate->copy()->endOfMonth();
            $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon\Carbon::MONDAY);
            $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon\Carbon::SUNDAY);
            $daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        @endphp

        <div class="p-3 md:p-6">
            <!-- Дни недели -->
            <div class="grid grid-cols-7 gap-1.5 md:gap-2 mb-2 md:mb-2">
                @foreach ($daysOfWeek as $day)
                    <div class="py-2 md:py-2 text-center">
                        <span class="text-[11px] md:text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            {{ $day }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Календарная сетка -->
            <div class="grid grid-cols-7 gap-1.5 md:gap-2">
                @for ($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay())
                    @php
                        $dateKey = $date->format('Y-m-d');
                        $dayAppointments = $appointmentsByDate->get($dateKey, collect());
                        $isCurrentMonth = $date->month === $selectedDate->month;
                        $isToday = $date->isToday();
                        $tableParams = [
                            'date' => $date->format('Y-m-d'),
                        ];
                        if (request()->has('search') && request()->search) {
                            $tableParams['search'] = request()->search;
                        }
                        if (request()->has('status') && request()->status) {
                            $tableParams['status'] = request()->status;
                        }
                        if (request()->has('service_id') && request()->service_id) {
                            $tableParams['service_id'] = request()->service_id;
                        }
                        if (request()->has('master_id') && request()->master_id) {
                            $tableParams['master_id'] = request()->master_id;
                        }
                    @endphp
                    <div class="aspect-square md:min-h-[120px] rounded-xl md:rounded-2xl transition-all duration-300 shadow-lg hover:shadow-2xl
                        {{ $isCurrentMonth ? 'border-2 border-slate-200 dark:border-slate-700' : 'border-2 border-slate-100 dark:border-slate-800/50' }}
                        {{ $isToday ? 'bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/30 border-blue-400 dark:border-blue-700 shadow-blue-100 dark:shadow-blue-900/20' : ($isCurrentMonth ? 'bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/50 dark:to-slate-700/30' : 'bg-gradient-to-br from-slate-50/50 to-slate-100/50 dark:from-slate-900/30 dark:to-slate-800/20') }}
                        {{ $dayAppointments->count() > 0 ? 'hover:border-indigo-500 dark:hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-200 dark:hover:shadow-indigo-900/40 cursor-pointer active:scale-95 md:active:scale-100 transform hover:scale-[1.02]' : 'hover:border-slate-300 dark:hover:border-slate-600' }}"
                        @if ($dayAppointments->count() > 0) onclick="window.location.href = '{{ route('appointments.index', $tableParams) }}'" @endif>
                        <div class="h-full flex flex-col justify-center md:justify-start p-2 md:p-2">
                            <!-- Мобильная версия: только дата -->
                            <div class="md:hidden flex items-center justify-center h-full relative">
                                <span class="text-lg font-bold {{ $isCurrentMonth ? ($isToday ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-900 dark:text-white') : 'text-slate-400 dark:text-slate-500' }}">
                                    {{ $date->day }}
                                </span>
                                @if ($dayAppointments->count() > 0)
                                    <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-indigo-500 dark:bg-indigo-400"></span>
                                @endif
                            </div>

                            <!-- Десктопная версия: полная информация -->
                            <div class="hidden md:block">
                                <!-- Заголовок дня -->
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base font-semibold {{ $isCurrentMonth ? ($isToday ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-900 dark:text-white') : 'text-slate-400 dark:text-slate-500' }}">
                                        {{ $date->day }}
                                    </span>
                                    @if ($dayAppointments->count() > 0)
                                        <a href="{{ route('appointments.index', $tableParams) }}" @click.stop
                                            class="h-7 w-7 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 text-white text-sm flex items-center justify-center font-bold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-sm hover:shadow-md hover:scale-110 flex-shrink-0"
                                            title="{{ $dayAppointments->count() }} {{ $dayAppointments->count() === 1 ? 'запись' : ($dayAppointments->count() < 5 ? 'записи' : 'записей') }}">
                                            {{ $dayAppointments->count() }}
                                        </a>
                                    @endif
                                </div>

                                <!-- Записи -->
                                <div class="flex-1 space-y-1.5 overflow-hidden">
                                    @foreach ($dayAppointments->take(2) as $appointment)
                                        <a href="{{ route('appointments.show', $appointment) }}" @click.stop
                                            class="block px-2 py-1.5 rounded-md text-xs font-medium truncate transition-all hover:scale-[1.02] hover:shadow-sm
                                            {{ $appointment->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : ($appointment->status === 'cancelled' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : ($appointment->status === 'confirmed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800')) }}"
                                            title="{{ $appointment->client->full_name }} - {{ $appointment->service->name }}">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                                                <span class="truncate">{{ $appointment->client->first_name }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                    @if ($dayAppointments->count() > 2)
                                        <a href="{{ route('appointments.index', $tableParams) }}" @click.stop
                                            class="block text-xs text-indigo-600 dark:text-indigo-400 font-semibold px-2 py-1 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                            +{{ $dayAppointments->count() - 2 }} еще
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Легенда статусов -->
        <div class="hidden md:block px-3 md:px-6 pb-3 md:pb-6 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex flex-wrap items-center gap-2 md:gap-6 pt-3 md:pt-4">
                <span class="text-[10px] md:text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide w-full sm:w-auto mb-1 sm:mb-0">Статусы:</span>
                <div class="flex items-center gap-1.5 md:gap-2">
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-amber-500"></div>
                    <span class="text-[10px] md:text-xs text-slate-600 dark:text-slate-400">Ожидает</span>
                </div>
                <div class="flex items-center gap-1.5 md:gap-2">
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-blue-500"></div>
                    <span class="text-[10px] md:text-xs text-slate-600 dark:text-slate-400">Подтверждена</span>
                </div>
                <div class="flex items-center gap-1.5 md:gap-2">
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-emerald-500"></div>
                    <span class="text-[10px] md:text-xs text-slate-600 dark:text-slate-400">Завершена</span>
                </div>
                <div class="flex items-center gap-1.5 md:gap-2">
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded bg-rose-500"></div>
                    <span class="text-[10px] md:text-xs text-slate-600 dark:text-slate-400">Отменена</span>
                </div>
            </div>
        </div>
    </div>

@endsection