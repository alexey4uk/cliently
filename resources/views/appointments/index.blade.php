@extends('layouts.user')

@section('title', 'Записи - Cliently')
@section('page-title', 'Записи')
@section('page-description', 'Управление записями клиентов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Записи', 'url' => null]]" />
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
        view: '{{ $view }}',
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
        <!-- Заголовок страницы и статистика -->
        <div class="flex flex-col gap-6">
            <!-- Заголовок -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white">
                        Записи
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Управление записями и расписанием клиентов
                    </p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Переключатель вида -->
                    <div
                        class="inline-flex rounded-xl border border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 p-1 shadow-lg">
                        <button
                            @click="view = 'table'; window.location.href = '{{ route('appointments.index', array_merge(request()->query(), ['view' => 'table'])) }}'"
                            :class="view === 'table' ?
                                'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-md transform scale-105' :
                                'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                            class="px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-table text-sm"></i>
                            <span class="hidden sm:inline">Таблица</span>
                        </button>

                        <button
                            @click="view = 'calendar'; window.location.href = '{{ route('appointments.index', array_merge(request()->query(), ['view' => 'calendar'])) }}'"
                            :class="view === 'calendar' ?
                                'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-md transform scale-105' :
                                'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                            class="px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-calendar text-sm"></i>
                            <span class="hidden sm:inline">Календарь</span>
                        </button>
                    </div>
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


        </div>

        @if ($view === 'calendar')
            <!-- Календарное представление -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                <!-- Навигация по месяцам -->
                <div
                    class="bg-gradient-to-r from-indigo-500 via-indigo-600 to-purple-600 dark:from-indigo-950/50 dark:via-indigo-900/30 dark:to-purple-900/30 border-b border-indigo-200 dark:border-indigo-800/50 px-3 md:px-6 py-4 md:py-5">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 md:gap-4">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <!-- Кнопка назад -->
                            <form method="GET" action="{{ route('appointments.index') }}" class="inline">
                                <input type="hidden" name="view" value="calendar">
                                <input type="hidden" name="month"
                                    value="{{ $selectedDate->copy()->subMonth()->format('Y-m') }}">
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if (request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                @if (request('service_id'))
                                    <input type="hidden" name="service_id" value="{{ request('service_id') }}">
                                @endif
                                @if (request('master_id'))
                                    <input type="hidden" name="master_id" value="{{ request('master_id') }}">
                                @endif
                                <button type="submit"
                                    class="h-10 w-10 md:h-12 md:w-12 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-300 bg-gradient-to-r from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 border border-slate-300 dark:border-slate-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 dark:hover:from-indigo-900/50 dark:hover:to-indigo-800/50 hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-lg hover:shadow-xl flex-shrink-0 transform hover:scale-110">
                                    <i class="fa-solid fa-chevron-left text-sm md:text-base"></i>
                                </button>
                            </form>

                            <!-- Поле выбора месяца -->
                            <form method="GET" action="{{ route('appointments.index') }}" id="calendar-month-form"
                                class="flex-1 sm:flex-initial">
                                <input type="hidden" name="view" value="calendar">
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if (request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                @if (request('service_id'))
                                    <input type="hidden" name="service_id" value="{{ request('service_id') }}">
                                @endif
                                @if (request('master_id'))
                                    <input type="hidden" name="master_id" value="{{ request('master_id') }}">
                                @endif
                                <input type="month" name="month" value="{{ $currentMonth }}"
                                    onchange="this.form.submit()"
                                    class="w-full sm:w-auto px-4 md:px-5 py-2.5 md:py-3 text-xs md:text-sm bg-gradient-to-r from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-slate-900 dark:text-white font-semibold cursor-pointer shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-xl">
                            </form>

                            <!-- Кнопка вперед -->
                            <form method="GET" action="{{ route('appointments.index') }}" class="inline">
                                <input type="hidden" name="view" value="calendar">
                                <input type="hidden" name="month"
                                    value="{{ $selectedDate->copy()->addMonth()->format('Y-m') }}">
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if (request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                @if (request('service_id'))
                                    <input type="hidden" name="service_id" value="{{ request('service_id') }}">
                                @endif
                                @if (request('master_id'))
                                    <input type="hidden" name="master_id" value="{{ request('master_id') }}">
                                @endif
                                <button type="submit"
                                    class="h-10 w-10 md:h-12 md:w-12 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-300 bg-gradient-to-r from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 border border-slate-300 dark:border-slate-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 dark:hover:from-indigo-900/50 dark:hover:to-indigo-800/50 hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-lg hover:shadow-xl flex-shrink-0 transform hover:scale-110">
                                    <i class="fa-solid fa-chevron-right text-sm md:text-base"></i>
                                </button>
                            </form>
                        </div>
                        @php
                            $todayParams = ['view' => 'calendar', 'month' => \Carbon\Carbon::now()->format('Y-m')];
                            if (request()->has('search') && request()->search) {
                                $todayParams['search'] = request()->search;
                            }
                            if (request()->has('status') && request()->status) {
                                $todayParams['status'] = request()->status;
                            }
                            if (request()->has('service_id') && request()->service_id) {
                                $todayParams['service_id'] = request()->service_id;
                            }
                            if (request()->has('master_id') && request()->master_id) {
                                $todayParams['master_id'] = request()->master_id;
                            }
                        @endphp
                        <button onclick="window.location.href = '{{ route('appointments.index', $todayParams) }}'"
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
                                <span
                                    class="text-[11px] md:text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
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
                                    'view' => 'table',
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
                                        <span
                                            class="text-lg font-bold
                                        {{ $isCurrentMonth ? ($isToday ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-900 dark:text-white') : 'text-slate-400 dark:text-slate-500' }}">
                                            {{ $date->day }}
                                        </span>
                                        @if ($dayAppointments->count() > 0)
                                            <span
                                                class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-indigo-500 dark:bg-indigo-400"></span>
                                        @endif
                                    </div>

                                    <!-- Десктопная версия: полная информация -->
                                    <div class="hidden md:block">
                                        <!-- Заголовок дня -->
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-base font-semibold
                                            {{ $isCurrentMonth ? ($isToday ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-900 dark:text-white') : 'text-slate-400 dark:text-slate-500' }}">
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
                                               {{ $appointment->status === 'completed'
                                                   ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                                                   : ($appointment->status === 'cancelled'
                                                       ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800'
                                                       : ($appointment->status === 'confirmed'
                                                           ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                                                           : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800')) }}"
                                                    title="{{ $appointment->client->full_name }} - {{ $appointment->service->name }}">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="font-bold">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                                                        <span
                                                            class="truncate">{{ $appointment->client->first_name }}</span>
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
                <div
                    class="hidden md:block px-3 md:px-6 pb-3 md:pb-6 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                    <div class="flex flex-wrap items-center gap-2 md:gap-6 pt-3 md:pt-4">
                        <span
                            class="text-[10px] md:text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide w-full sm:w-auto mb-1 sm:mb-0">Статусы:</span>
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
        @else
            <!-- Разделитель -->
            <div class="border-t border-slate-200 dark:border-slate-800"></div>

            <!-- Табличное представление -->
            <!-- Фильтры -->
            <div x-data="{ showFilters: false }" class="space-y-4">
                <!-- Мобильная версия: поиск и кнопка фильтров -->
                <div class="md:hidden space-y-3">
                    <!-- Всегда видимый поиск -->
                    <form method="GET" action="{{ route('appointments.index') }}" class="flex gap-2">
                        <input type="hidden" name="view" value="table">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-search text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Поиск по клиенту или услуге..."
                                class="pl-9 sm:pl-10 pr-4 py-2 sm:py-2.5 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-200 text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400">
                        </div>
                        <button type="submit"
                            class="h-10 w-10 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center transition-colors flex-shrink-0">
                            <i class="fa-solid fa-search text-xs sm:text-sm"></i>
                        </button>
                        <button type="button" @click="showFilters = !showFilters"
                            class="h-10 w-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex-shrink-0">
                            <i class="fa-solid fa-filter text-xs sm:text-sm"></i>
                        </button>
                    </form>

                    <!-- Выпадающая панель дополнительных фильтров -->
                    <div x-show="showFilters" @click.away="showFilters = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 space-y-3"
                        style="display: none;">
                        <form method="GET" action="{{ route('appointments.index') }}" class="space-y-3">
                            <input type="hidden" name="view" value="table">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Дата</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                    </div>
                                    <input type="date" name="date" value="{{ $date }}"
                                        class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white"
                                        onchange="this.form.submit()">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Статус</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-info-circle text-slate-400 text-xs"></i>
                                    </div>
                                    <select name="status" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Все статусы</option>
                                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидает
                                        </option>
                                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>
                                            Подтверждена</option>
                                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>
                                            Завершена</option>
                                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отменена
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Услуга</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-scissors text-slate-400 text-xs"></i>
                                    </div>
                                    <select name="service_id" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Все услуги</option>
                                        @foreach ($business->services()->where('is_active', true)->orderBy('name')->get() as $service)
                                            <option value="{{ $service->id }}"
                                                {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Мастер</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-user text-slate-400 text-xs"></i>
                                    </div>
                                    <select name="master_id" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Все мастера</option>
                                        @foreach ($business->masters()->where('is_active', true)->orderBy('first_name')->get() as $master)
                                            <option value="{{ $master->id }}"
                                                {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                                {{ $master->first_name }} {{ $master->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Сортировка</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-sort text-slate-400 text-xs"></i>
                                    </div>
                                    <select name="sort" onchange="updateSortDirection(this); this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="date" data-direction="desc"
                                            {{ $sort === 'date' && $direction === 'desc' ? 'selected' : '' }}>По дате
                                            (новые)</option>
                                        <option value="date" data-direction="asc"
                                            {{ $sort === 'date' && $direction === 'asc' ? 'selected' : '' }}>По дате
                                            (старые)</option>
                                        <option value="client" data-direction="asc"
                                            {{ $sort === 'client' && $direction === 'asc' ? 'selected' : '' }}>По клиенту
                                            (А-Я)</option>
                                        <option value="client" data-direction="desc"
                                            {{ $sort === 'client' && $direction === 'desc' ? 'selected' : '' }}>По клиенту
                                            (Я-А)</option>
                                    </select>
                                    <input type="hidden" name="direction" value="{{ $direction }}"
                                        id="sort-direction-mobile">
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">На
                                    странице</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-list text-slate-400 text-xs"></i>
                                    </div>
                                    <select name="per_page" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                        <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Кнопка сброса фильтров -->
                        @if ($date || $status || request('service_id') || request('master_id') || $sort !== 'date' || $direction !== 'desc')
                            <div class="pt-2 border-t border-slate-200 dark:border-slate-700">
                                <a href="{{ route('appointments.index', ['view' => 'table']) }}"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                    <span>Сбросить фильтры</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Десктопная версия фильтров -->
                <div class="hidden md:flex flex-col gap-4">
                    <!-- Всегда видимый поиск -->
                    <form method="GET" action="{{ route('appointments.index') }}" class="flex items-end gap-3">
                        <input type="hidden" name="view" value="table">
                        <!-- Поиск -->
                        <div class="flex-1 max-w-md">
                            <label for="search-input"
                                class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Поиск
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                </div>
                                <input id="search-input" type="text" name="search" value="{{ $search }}"
                                    placeholder="Поиск по клиенту или услуге..."
                                    class="pl-9 pr-4 py-2.5 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                            </div>
                        </div>

                        <!-- Кнопка поиска -->
                        <button type="submit"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors flex-shrink-0">
                            <i class="fa-solid fa-search text-xs"></i>
                        </button>

                        <!-- Кнопка фильтров -->
                        <button @click="toggleFilters()" type="button"
                            class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex-shrink-0 ml-auto">
                            <i class="fa-solid fa-filter text-xs"></i>
                            <span x-text="showFilters ? 'Скрыть фильтры' : 'Показать фильтры'"></span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                                :class="showFilters ? 'rotate-180' : ''"></i>
                        </button>
                    </form>

                    <!-- Активные фильтры -->
                    @if ($date || $status || request('service_id') || request('master_id'))
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span>Активных фильтров:</span>
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">
                                {{ collect([$date, $status, request('service_id'), request('master_id')])->filter()->count() }}
                            </span>
                            <a href="{{ route('appointments.index', ['view' => 'table']) }}"
                                class="inline-flex items-center justify-center gap-1 px-2 py-1 text-xs font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded hover:bg-rose-100 dark:hover:bg-rose-500/30 transition-colors ml-2">
                                <i class="fa-solid fa-xmark text-xs"></i>
                                <span>Сбросить</span>
                            </a>
                        </div>
                    @endif

                    <!-- Панель дополнительных фильтров -->
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 p-4"
                        style="display: none;">
                        <!-- Дополнительные фильтры -->
                        <form method="GET" action="{{ route('appointments.index') }}"
                            class="flex items-end gap-3 overflow-x-auto pb-2">
                            <input type="hidden" name="view" value="table">
                            <input type="hidden" name="search" value="{{ $search }}">

                            <!-- Фильтр по дате -->
                            <div class="min-w-[140px]">
                                <label for="date-filter"
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    Дата
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                    </div>
                                    <input id="date-filter" type="date" name="date" value="{{ $date }}"
                                        class="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                        onchange="this.form.submit()">
                                </div>
                            </div>

                            <!-- Фильтр по статусу -->
                            <div class="min-w-[140px]">
                                <label for="status-filter"
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Статус</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-info-circle text-slate-400 text-xs"></i>
                                    </div>
                                    <select id="status-filter" name="status" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Все статусы</option>
                                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидает
                                        </option>
                                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>
                                            Подтверждена
                                        </option>
                                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>
                                            Завершена
                                        </option>
                                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отменена
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Фильтр по услуге -->
                            <div class="min-w-[140px]">
                                <label for="service-filter"
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Услуга</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-scissors text-slate-400 text-xs"></i>
                                    </div>
                                    <select id="service-filter" name="service_id" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Все услуги</option>
                                        @foreach ($business->services()->where('is_active', true)->orderBy('name')->get() as $service)
                                            <option value="{{ $service->id }}"
                                                {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Фильтр по мастеру -->
                            <div class="min-w-[140px]">
                                <label for="master-filter"
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Мастер</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-user text-slate-400 text-xs"></i>
                                    </div>
                                    <select id="master-filter" name="master_id" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Все мастера</option>
                                        @foreach ($business->masters()->where('is_active', true)->orderBy('first_name')->get() as $master)
                                            <option value="{{ $master->id }}"
                                                {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                                {{ $master->first_name }} {{ $master->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Сортировка -->
                            <div class="min-w-[160px]">
                                <label for="sort-filter"
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Сортировка</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-sort text-slate-400 text-xs"></i>
                                    </div>
                                    <select id="sort-filter" name="sort"
                                        onchange="updateSortDirection(this); this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="date" data-direction="desc"
                                            {{ $sort === 'date' && $direction === 'desc' ? 'selected' : '' }}>По дате
                                            (новые)</option>
                                        <option value="date" data-direction="asc"
                                            {{ $sort === 'date' && $direction === 'asc' ? 'selected' : '' }}>По дате
                                            (старые)</option>
                                        <option value="client" data-direction="asc"
                                            {{ $sort === 'client' && $direction === 'asc' ? 'selected' : '' }}>По клиенту
                                            (А-Я)</option>
                                        <option value="client" data-direction="desc"
                                            {{ $sort === 'client' && $direction === 'desc' ? 'selected' : '' }}>По клиенту
                                            (Я-А)</option>
                                    </select>
                                    <input type="hidden" name="direction" value="{{ $direction }}"
                                        id="sort-direction-desktop">
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- На странице -->
                            <div class="min-w-[100px]">
                                <label for="per-page-filter"
                                    class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">На
                                    странице</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-list text-slate-400 text-xs"></i>
                                    </div>
                                    <select id="per-page-filter" name="per_page" onchange="this.form.submit()"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                        <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Кнопка сброса фильтров -->
                            @if (
                                $date ||
                                    $status ||
                                    request('service_id') ||
                                    request('master_id') ||
                                    $sort !== 'date' ||
                                    $direction !== 'desc' ||
                                    $perPage != 20)
                                <div class="ml-auto flex items-end">
                                    <a href="{{ route('appointments.index', ['view' => 'table', 'search' => $search]) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                        <span>Сбросить</span>
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Список записей -->
                @if ($appointments->count() > 0)
                    <!-- Мобильная версия: карточки -->
                    <div class="md:hidden space-y-3">
                        @foreach ($appointments as $appointment)
                            <div
                                class="bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg hover:shadow-xl p-5 transition-all duration-200 hover:scale-[1.02]">
                                <div class="flex items-start justify-between gap-2 md:gap-3 mb-3">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <div
                                            class="h-10 w-10 md:h-12 md:w-12 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                            <span
                                                class="text-xs md:text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('appointments.show', $appointment) }}"
                                                class="block group">
                                                <h3
                                                    class="text-sm font-semibold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $appointment->client->full_name }}
                                                </h3>
                                            </a>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 truncate mt-0.5">
                                                {{ $appointment->service->name }}
                                            </p>
                                        </div>
                                    </div>
                                    @php
                                        $statusColors = [
                                            'pending' =>
                                                'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                                            'confirmed' =>
                                                'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                                            'completed' =>
                                                'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300',
                                            'cancelled' =>
                                                'bg-rose-100 dark:bg-rose-900/20 text-rose-800 dark:text-rose-300',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Ожидает',
                                            'confirmed' => 'Подтверждена',
                                            'completed' => 'Завершена',
                                            'cancelled' => 'Отменена',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium flex-shrink-0 {{ $statusColors[$appointment->status] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300' }}">
                                        {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <span>{{ $appointment->date->format('d.m.Y') }}</span>
                                        @if ($appointment->final_price)
                                            <span>•</span>
                                            <span
                                                class="font-medium">{{ number_format($appointment->final_price, 0, ',', ' ') }}
                                                Br</span>
                                        @endif
                                        @if ($appointment->final_duration)
                                            <span>•</span>
                                            <span>{{ $appointment->final_duration }} мин</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button
                                            @click="openPhoneModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ addslashes($appointment->client->full_name) }}')"
                                            class="h-9 w-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center"
                                            title="Контакт">
                                            <i class="fa-solid fa-phone text-xs"></i>
                                        </button>
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open"
                                                class="h-9 w-9 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center"
                                                title="Действия">
                                                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg z-50 py-1"
                                                style="display: none;">
                                                <a href="{{ route('appointments.show', $appointment) }}"
                                                    class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                                    <i class="fa-regular fa-eye w-4 text-center"></i>
                                                    <span>Просмотр</span>
                                                </a>
                                                @if ($appointment->status === 'pending')
                                                    <!-- Ожидает подтверждения: можно подтвердить или отменить -->
                                                    <form method="POST"
                                                        action="{{ route('appointments.confirm', $appointment) }}"
                                                        class="w-full">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" @click="open = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-500/20 transition-colors">
                                                            <i class="fa-solid fa-check-circle w-4 text-center"></i>
                                                            <span>Подтвердить</span>
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('appointments.cancel', $appointment) }}"
                                                        onsubmit="return confirm('Вы уверены, что хотите отменить запись?');"
                                                        class="w-full">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" @click="open = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-colors">
                                                            <i class="fa-solid fa-xmark w-4 text-center"></i>
                                                            <span>Отменить</span>
                                                        </button>
                                                    </form>
                                                @elseif($appointment->status === 'confirmed')
                                                    <!-- Подтверждена: можно выполнить или отменить -->
                                                    <form method="POST"
                                                        action="{{ route('appointments.complete', $appointment) }}"
                                                        class="w-full">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" @click="open = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 transition-colors">
                                                            <i class="fa-solid fa-check w-4 text-center"></i>
                                                            <span>Выполнить</span>
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('appointments.cancel', $appointment) }}"
                                                        onsubmit="return confirm('Вы уверены, что хотите отменить запись?');"
                                                        class="w-full">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" @click="open = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-colors">
                                                            <i class="fa-solid fa-xmark w-4 text-center"></i>
                                                            <span>Отменить</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Десктопная версия: таблица -->
                    <div
                        class="hidden md:block bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead
                                    class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Дата и время
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            Клиент
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell">
                                            Услуга
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell">
                                            Мастер
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden xl:table-cell">
                                            Статус
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">
                                            Действия
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                                    @foreach ($appointments as $appointment)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                            <td class="px-4 py-3.5">
                                                <div class="space-y-0.5">
                                                    <div
                                                        class="text-sm font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                                                        {{ $appointment->date->format('d.m.Y') }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <div class="space-y-0.5">
                                                    <a href="{{ route('appointments.show', $appointment) }}"
                                                        class="block group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                        <div
                                                            class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                                            {{ $appointment->client->full_name }}
                                                        </div>
                                                    </a>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                        {{ $appointment->client->phone }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5 hidden md:table-cell">
                                                <div
                                                    class="text-sm text-slate-700 dark:text-slate-300 truncate font-medium">
                                                    {{ $appointment->service->name }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                                <div class="text-sm text-slate-600 dark:text-slate-400 truncate">
                                                    @if ($appointment->master)
                                                        {{ $appointment->master->first_name }}
                                                        {{ $appointment->master->last_name }}
                                                    @else
                                                        <span class="text-slate-400 dark:text-slate-500 italic">Не
                                                            назначен</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5 hidden xl:table-cell">
                                                @php
                                                    $statusColors = [
                                                        'pending' =>
                                                            'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                                                        'confirmed' =>
                                                            'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                                                        'completed' =>
                                                            'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300',
                                                        'cancelled' =>
                                                            'bg-rose-100 dark:bg-rose-900/20 text-rose-800 dark:text-rose-300',
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'Ожидает',
                                                        'confirmed' => 'Подтверждена',
                                                        'completed' => 'Завершена',
                                                        'cancelled' => 'Отменена',
                                                    ];
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $statusColors[$appointment->status] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300' }}">
                                                    {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button
                                                        @click="openPhoneModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ addslashes($appointment->client->full_name) }}')"
                                                        class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center"
                                                        title="Позвонить клиенту">
                                                        <i class="fa-solid fa-phone text-xs"></i>
                                                    </button>
                                                    <div x-data="{
                                                        open: false,
                                                        updatePosition() {
                                                            if (!this.open) return;
                                                            $nextTick(() => {
                                                                const button = this.$el.querySelector('button');
                                                                const menu = this.$el.querySelector('[x-show]');
                                                                if (!button || !menu) return;
                                                                const rect = button.getBoundingClientRect();
                                                                const menuHeight = 300; // Approximate menu height
                                                                const menuWidth = 208; // 52 * 4 (w-52 in rem)
                                                                const viewportHeight = window.innerHeight;
                                                                const viewportWidth = window.innerWidth;
                                                                const scrollY = window.scrollY;
                                                    
                                                                menu.style.position = 'fixed';
                                                    
                                                                // Check vertical space
                                                                const spaceBelow = viewportHeight - (rect.bottom - scrollY);
                                                                const spaceAbove = rect.top - scrollY;
                                                    
                                                                if (spaceBelow >= menuHeight) {
                                                                    menu.style.top = (rect.bottom + 8) + 'px';
                                                                    menu.style.bottom = 'auto';
                                                                } else if (spaceAbove >= menuHeight) {
                                                                    menu.style.bottom = (viewportHeight - rect.top + 8) + 'px';
                                                                    menu.style.top = 'auto';
                                                                } else {
                                                                    // Default to below if not enough space anywhere
                                                                    menu.style.top = (rect.bottom + 8) + 'px';
                                                                    menu.style.bottom = 'auto';
                                                                }
                                                    
                                                                // Check horizontal space
                                                                const spaceRight = viewportWidth - rect.right;
                                                                const spaceLeft = rect.left;
                                                    
                                                                if (spaceRight >= menuWidth) {
                                                                    menu.style.right = (viewportWidth - rect.right) + 'px';
                                                                    menu.style.left = 'auto';
                                                                } else if (spaceLeft >= menuWidth) {
                                                                    menu.style.left = rect.left + 'px';
                                                                    menu.style.right = 'auto';
                                                                } else {
                                                                    // Default to right if not enough space anywhere
                                                                    menu.style.right = (viewportWidth - rect.right) + 'px';
                                                                    menu.style.left = 'auto';
                                                                }
                                                            });
                                                        }
                                                    }" x-init="$watch('open', () => updatePosition())"
                                                        @resize.window="updatePosition()"
                                                        @scroll.window="updatePosition()" class="relative">
                                                        <button @click="open = !open"
                                                            class="h-8 w-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-300 transition-colors flex items-center justify-center"
                                                            title="Дополнительные действия">
                                                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                                        </button>
                                                        <div x-show="open" @click.away="open = false"
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="transform opacity-0 scale-95"
                                                            x-transition:enter-end="transform opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-75"
                                                            x-transition:leave-start="transform opacity-100 scale-100"
                                                            x-transition:leave-end="transform opacity-0 scale-95"
                                                            class="w-52 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg z-50 py-1"
                                                            style="display: none; position: fixed;">
                                                            <a href="{{ route('appointments.show', $appointment) }}"
                                                                class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                                                <i class="fa-regular fa-eye w-4 text-center"></i>
                                                                <span>Просмотр</span>
                                                            </a>
                                                            <div
                                                                class="border-t border-slate-200 dark:border-slate-700 my-1">
                                                            </div>
                                                            <!-- Редактирование -->
                                                            <a href="{{ route('appointments.edit', $appointment) }}"
                                                                class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                                                <i class="fa-regular fa-pen-to-square w-4 text-center"></i>
                                                                <span>Редактировать</span>
                                                            </a>

                                                            <!-- Удаление -->
                                                            <form method="POST"
                                                                action="{{ route('appointments.destroy', $appointment) }}"
                                                                onsubmit="return confirm('Вы уверены, что хотите удалить эту запись? Это действие нельзя отменить.');"
                                                                class="w-full">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" @click="open = false"
                                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-colors">
                                                                    <i class="fa-regular fa-trash-can w-4 text-center"></i>
                                                                    <span>Удалить</span>
                                                                </button>
                                                            </form>

                                                            <!-- Разделитель -->
                                                            <div
                                                                class="border-t border-slate-200 dark:border-slate-700 my-1">
                                                            </div>

                                                            @if ($appointment->status === 'pending')
                                                                <!-- Ожидает подтверждения: можно подтвердить или отменить -->
                                                                <form method="POST"
                                                                    action="{{ route('appointments.confirm', $appointment) }}"
                                                                    class="w-full">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" @click="open = false"
                                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-500/20 transition-colors">
                                                                        <i
                                                                            class="fa-solid fa-check-circle w-4 text-center"></i>
                                                                        <span>Подтвердить</span>
                                                                    </button>
                                                                </form>
                                                                <form method="POST"
                                                                    action="{{ route('appointments.cancel', $appointment) }}"
                                                                    onsubmit="return confirm('Вы уверены, что хотите отменить запись?');"
                                                                    class="w-full">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" @click="open = false"
                                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-colors">
                                                                        <i class="fa-solid fa-xmark w-4 text-center"></i>
                                                                        <span>Отменить</span>
                                                                    </button>
                                                                </form>
                                                            @elseif($appointment->status === 'confirmed')
                                                                <!-- Подтверждена: можно выполнить или отменить -->
                                                                <form method="POST"
                                                                    action="{{ route('appointments.complete', $appointment) }}"
                                                                    class="w-full">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" @click="open = false"
                                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 transition-colors">
                                                                        <i class="fa-solid fa-check w-4 text-center"></i>
                                                                        <span>Выполнить</span>
                                                                    </button>
                                                                </form>
                                                                <form method="POST"
                                                                    action="{{ route('appointments.cancel', $appointment) }}"
                                                                    onsubmit="return confirm('Вы уверены, что хотите отменить запись?');"
                                                                    class="w-full">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" @click="open = false"
                                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-colors">
                                                                        <i class="fa-solid fa-xmark w-4 text-center"></i>
                                                                        <span>Отменить</span>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->
                        @if ($view === 'table' && $appointments->hasPages())
                            @php
                                $currentPage = $appointments->currentPage();
                                $lastPage = $appointments->lastPage();

                                // Вычисляем диапазон страниц для отображения
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);

                                // Корректируем, чтобы всегда показывать 5 страниц (если возможно)
                                if ($endPage - $startPage < 4) {
                                    if ($startPage == 1) {
                                        $endPage = min($lastPage, $startPage + 4);
                                    } else {
                                        $startPage = max(1, $endPage - 4);
                                    }
                                }
                            @endphp
                            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div class="text-xs md:text-sm text-slate-500 dark:text-slate-400">
                                        Показано <span
                                            class="font-medium text-slate-900 dark:text-white">{{ $appointments->firstItem() }}</span>
                                        -
                                        <span
                                            class="font-medium text-slate-900 dark:text-white">{{ $appointments->lastItem() }}</span>
                                        из
                                        <span
                                            class="font-medium text-slate-900 dark:text-white">{{ $appointments->total() }}</span>
                                        записей
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <!-- Кнопка "В начало" -->
                                        @if ($currentPage > 1)
                                            <a href="{{ $appointments->url(1) }}"
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300"
                                                title="В начало">
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </a>
                                        @else
                                            <button disabled
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400"
                                                title="В начало">
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </button>
                                        @endif

                                        <!-- Кнопка "Назад" -->
                                        @if ($appointments->onFirstPage())
                                            <button disabled
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                                <i class="fa-solid fa-chevron-left text-xs"></i>
                                            </button>
                                        @else
                                            <a href="{{ $appointments->previousPageUrl() }}"
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                                <i class="fa-solid fa-chevron-left text-xs"></i>
                                            </a>
                                        @endif

                                        <!-- Номера страниц -->
                                        @foreach ($appointments->getUrlRange($startPage, $endPage) as $page => $url)
                                            @if ($page == $currentPage)
                                                <button disabled
                                                    class="h-8 w-8 flex items-center justify-center bg-indigo-600 text-white rounded-lg font-medium cursor-default text-xs">
                                                    {{ $page }}
                                                </button>
                                            @else
                                                <a href="{{ $url }}"
                                                    class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 text-xs">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endforeach

                                        <!-- Кнопка "Вперед" -->
                                        @if ($appointments->hasMorePages())
                                            <a href="{{ $appointments->nextPageUrl() }}"
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                                <i class="fa-solid fa-chevron-right text-xs"></i>
                                            </a>
                                        @else
                                            <button disabled
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                                <i class="fa-solid fa-chevron-right text-xs"></i>
                                            </button>
                                        @endif

                                        <!-- Кнопка "В конец" -->
                                        @if ($currentPage < $lastPage)
                                            <a href="{{ $appointments->url($lastPage) }}"
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300"
                                                title="В конец">
                                                <i class="fa-solid fa-angles-right text-xs"></i>
                                            </a>
                                        @else
                                            <button disabled
                                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400"
                                                title="В конец">
                                                <i class="fa-solid fa-angles-right text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Пагинация для мобильных карточек -->
                    @if ($view === 'table' && $appointments->hasPages())
                        <div class="md:hidden flex items-center justify-center gap-2 pt-3">
                            @if ($appointments->onFirstPage())
                                <button disabled
                                    class="h-9 px-3 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                            @else
                                <a href="{{ $appointments->previousPageUrl() }}"
                                    class="h-9 px-3 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            <div class="text-xs text-slate-600 dark:text-slate-400 px-3">
                                Страница {{ $appointments->currentPage() }} из {{ $appointments->lastPage() }}
                            </div>

                            @if ($appointments->hasMorePages())
                                <a href="{{ $appointments->nextPageUrl() }}"
                                    class="h-9 px-3 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="h-9 px-3 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            @endif
                        </div>
                    @endif
                @else
                    <div
                        class="bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl p-8 md:p-16 text-center">
                        <div class="max-w-sm mx-auto">
                            <div
                                class="h-16 w-16 md:h-20 md:w-20 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 dark:from-indigo-900/30 dark:to-indigo-800/20 flex items-center justify-center mx-auto mb-4 md:mb-6">
                                <i
                                    class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-2xl md:text-3xl"></i>
                            </div>
                            <h3 class="text-lg md:text-xl font-semibold text-slate-900 dark:text-white mb-2">
                                @if ($view === 'calendar')
                                    Нет записей в этом месяце
                                @else
                                    Записи не найдены
                                @endif
                            </h3>
                            <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mb-6 md:mb-8 leading-relaxed">
                                @if ($view === 'calendar')
                                    Выберите другой месяц или создайте новую запись для отображения в календаре
                                @elseif($date || $status)
                                    Попробуйте изменить параметры поиска или фильтры для получения других результатов
                                @else
                                    Начните работу с системой, создав первую запись для вашего клиента
                                @endif
                            </p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 md:gap-3">
                                @if ($date || $status)
                                    <a href="{{ route('appointments.index', ['view' => $view]) }}"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                        <span>Сбросить фильтры</span>
                                    </a>
                                @endif
                                <a href="{{ route('appointments.create') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 text-xs md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-colors">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Создать запись</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
        @endif

        <!-- Модальное окно для номера телефона -->
        <div x-show="showPhoneModal" @click.away="closePhoneModal()" @keydown.escape.window="closePhoneModal()"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform opacity-0 scale-90 rotate-3"
                x-transition:enter-end="transform opacity-100 scale-100 rotate-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform opacity-100 scale-100 rotate-0"
                x-transition:leave-end="transform opacity-0 scale-90 rotate-3"
                class="bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 max-w-sm w-full overflow-hidden">
                <!-- Заголовок -->
                <div
                    class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                    <button @click="closePhoneModal()"
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Контент -->
                <div class="px-4 md:px-6 py-4 md:py-5">
                    <!-- Клиент -->
                    <div class="mb-4">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">
                            Клиент</p>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                            </div>
                            <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                        </div>
                    </div>

                    <!-- Телефон -->
                    <div class="mb-6">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">
                            Телефон</p>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-300"></i>
                            </div>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" x-text="phoneDisplay"></p>
                        </div>
                    </div>

                    <!-- Действия -->
                    <div class="space-y-3">
                        <a :href="`tel:${phone}`"
                            class="md:hidden w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fa-solid fa-phone text-sm"></i>
                            <span>Позвонить</span>
                        </a>
                        <button @click="navigator.clipboard.writeText(phone); closePhoneModal();"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 md:from-slate-100 md:to-slate-200 md:dark:from-slate-800 md:dark:to-slate-700 px-4 py-3 text-sm font-semibold text-white md:text-slate-700 md:dark:text-slate-300 hover:from-indigo-600 hover:to-indigo-700 md:hover:from-slate-200 md:hover:to-slate-300 md:dark:hover:from-slate-700 md:dark:hover:to-slate-600 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fa-regular fa-copy text-sm"></i>
                            <span>Копировать номер</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function updateSortDirection(select) {
            const selectedOption = select.options[select.selectedIndex];
            const direction = selectedOption.getAttribute('data-direction');
            // Обновляем оба скрытых поля (мобильное и десктопное)
            const mobileInput = document.getElementById('sort-direction-mobile');
            const desktopInput = document.getElementById('sort-direction-desktop');
            if (mobileInput) mobileInput.value = direction;
            if (desktopInput) desktopInput.value = direction;
        }
    </script>

@endsection
