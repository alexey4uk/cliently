@extends('layouts.user')

@section('title', 'Клиенты - Cliently')
@section('page-title', 'Клиенты')
@section('page-description', 'Ваша клиентская база')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Клиенты', 'url' => null]]" />
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
            class="bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg p-5 flex items-center gap-4 shadow-sm">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
            <button @click="show = false"
                class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors">
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
            class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-5 flex items-center gap-4 shadow-sm">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
            </div>
            <button @click="show = false"
                class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @php
        $totalClients = $business->clients()->count();
        $activeClients = $business->clients()->whereHas('appointments')->count();
        $newClientsThisMonth = $business->clients()->where('created_at', '>=', now()->startOfMonth())->count();
        $hasActiveFilters = $period || request('activity') || $sort !== 'created_at' || $direction !== 'desc' || $perPage != 15 || $search;
    @endphp

    <div x-data="{
        showPhoneModal: false,
        phone: '',
        phoneDisplay: '',
        client: '',
        showDeleteModal: false,
        clientToDelete: null,
        clientName: '',
        showFilters: {{ $hasActiveFilters ? 'true' : 'false' }},
        mobileShowFilters: false,
        openPhoneModal(phone, phoneDisplay, client) {
            this.phone = phone;
            this.phoneDisplay = phoneDisplay;
            this.client = client;
            this.showPhoneModal = true;
        },
        closePhoneModal() {
            this.showPhoneModal = false;
        },
        openDeleteModal(clientId, clientName) {
            this.clientToDelete = clientId;
            this.clientName = clientName;
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
            this.clientToDelete = null;
            this.clientName = '';
        },
        confirmDelete() {
            if (this.clientToDelete) {
                document.getElementById('delete-form').action = '{{ route('clients.index') }}/' + this.clientToDelete;
                document.getElementById('delete-form').submit();
            }
        },
        toggleFilters() {
            this.showFilters = !this.showFilters;
        }
    }" class="space-y-4 md:space-y-6">

        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">
                        Клиенты
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Всего: {{ $totalClients }} • Активных: {{ $activeClients }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('clients.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-file-csv text-sm"></i>
                        <span>Экспорт</span>
                    </a>
                    <a href="{{ route('clients.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                        <span>Добавить</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <div class="space-y-4">
            <!-- Активные фильтры -->
            @if ($hasActiveFilters)
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($search)
                            <a href="{{ route('clients.index', array_merge(request()->except('search'), ['view' => 'table'])) }}"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                <span>Поиск: "{{ $search }}"</span>
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </a>
                        @endif
                        @if ($period)
                            <a href="{{ route('clients.index', array_merge(request()->except('period'), ['view' => 'table'])) }}"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                <span>{{ $period === 'today' ? 'Сегодня' : ($period === 'week' ? 'Неделя' : ($period === 'month' ? 'Месяц' : 'Год')) }}</span>
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </a>
                        @endif
                        @if (request('activity'))
                            <a href="{{ route('clients.index', array_merge(request()->except('activity'), ['view' => 'table'])) }}"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                <span>{{ request('activity') === 'active' ? 'С записями' : 'Без записей' }}</span>
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </a>
                        @endif
                        <a href="{{ route('clients.index', ['view' => 'table']) }}"
                            class="ml-auto text-xs text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                            Сбросить
                        </a>
                    </div>
                </div>
            @endif

            <!-- Мобильная версия: поиск и кнопка фильтров -->
            <div class="md:hidden space-y-4">
                <!-- Всегда видимый поиск -->
                <form method="GET" action="{{ route('clients.index') }}" class="flex gap-3">
                    <input type="hidden" name="view" value="table">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Поиск клиентов..."
                            class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                    </div>
                    <button type="submit"
                        class="h-12 px-4 rounded-lg bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>
                    <button type="button" @click="mobileShowFilters = !mobileShowFilters"
                        class="h-12 w-12 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                        :class="mobileShowFilters ? 'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' : ''">
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </button>
                </form>

                <!-- Выпадающая панель дополнительных фильтров -->
                <div x-show="mobileShowFilters" @click.away="mobileShowFilters = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 transform -translate-y-4 scale-95"
                    class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4 space-y-4"
                    style="display: none;">
                    <form method="GET" action="{{ route('clients.index') }}" class="space-y-4">
                        <input type="hidden" name="view" value="table">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Период</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                                </div>
                                <select name="period" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $period === '' ? 'selected' : '' }}>Все время</option>
                                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
                                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Последняя неделя
                                    </option>
                                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Последний месяц
                                    </option>
                                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Последний год
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Сортировка</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-arrow-up-wide-short text-slate-400 text-sm"></i>
                                </div>
                                <select name="sort" onchange="updateSortDirection(this); this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="created_at" data-direction="desc"
                                        {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате
                                        добавления (новые)</option>
                                    <option value="created_at" data-direction="asc"
                                        {{ $sort === 'created_at' && $direction === 'asc' ? 'selected' : '' }}>По дате
                                        добавления (старые)</option>
                                    <option value="name" data-direction="asc"
                                        {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По имени (А-Я)
                                    </option>
                                    <option value="name" data-direction="desc"
                                        {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По имени (Я-А)
                                    </option>
                                </select>
                                <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Активность</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chart-line text-slate-400 text-sm"></i>
                                </div>
                                <select name="activity" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $activity === '' ? 'selected' : '' }}>Все клиенты</option>
                                    <option value="active" {{ $activity === 'active' ? 'selected' : '' }}>С записями
                                    </option>
                                    <option value="inactive" {{ $activity === 'inactive' ? 'selected' : '' }}>Без записей
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">На странице</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-table-list text-slate-400 text-sm"></i>
                                </div>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Кнопка сброса фильтров -->
                    @if ($period || request('activity'))
                        <div class="pt-5 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('clients.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                                <span>Сбросить фильтры</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Десктопная версия фильтров -->
            <div class="hidden md:flex flex-col gap-4">
                <!-- Всегда видимый поиск -->
                <form method="GET" action="{{ route('clients.index') }}" class="flex items-end gap-4">
                    <input type="hidden" name="view" value="table">
                    <!-- Поиск -->
                    <div class="flex-1 max-w-lg">
                        <label for="search-input"
                            class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                            Поиск клиентов
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                            </div>
                            <input id="search-input" type="text" name="search" value="{{ $search }}"
                                placeholder="Поиск по имени, телефону или email..."
                                class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                        </div>
                    </div>

                    <!-- Кнопка поиска -->
                    <button type="submit"
                        class="px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>

                    <!-- Кнопка фильтров -->
                    <button @click="toggleFilters()" type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors ml-auto"
                        :class="showFilters ? 'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' : ''">
                        <i class="fa-solid fa-sliders text-sm"></i>
                        <span x-text="showFilters ? 'Скрыть' : 'Фильтры'"></span>
                    </button>
                </form>

                <!-- Панель дополнительных фильтров -->
                <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4 scale-98"
                    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 transform -translate-y-4 scale-98"
                    class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 p-4"
                    style="display: none;">
                    <!-- Дополнительные фильтры -->
                    <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap items-end gap-4">
                        <input type="hidden" name="view" value="table">
                        <input type="hidden" name="search" value="{{ $search }}">

                        <!-- Фильтр по периоду -->
                        <div class="min-w-[180px]">
                            <label for="period-filter"
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                                Период
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                                </div>
                                <select id="period-filter" name="period" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $period === '' ? 'selected' : '' }}>Все время</option>
                                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
                                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Последняя неделя
                                    </option>
                                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Последний месяц
                                    </option>
                                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Последний год
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Фильтр по активности -->
                        <div class="min-w-[180px]">
                            <label for="activity-filter"
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Активность</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chart-line text-slate-400 text-sm"></i>
                                </div>
                                <select id="activity-filter" name="activity" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $activity === '' ? 'selected' : '' }}>Все клиенты</option>
                                    <option value="active" {{ $activity === 'active' ? 'selected' : '' }}>С записями
                                    </option>
                                    <option value="inactive" {{ $activity === 'inactive' ? 'selected' : '' }}>Без записей
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Сортировка -->
                        <div class="min-w-[200px]">
                            <label for="sort-filter"
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Сортировка</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-arrow-up-wide-short text-slate-400 text-sm"></i>
                                </div>
                                <select id="sort-filter" name="sort"
                                    onchange="updateSortDirection(this); this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="created_at" data-direction="desc"
                                        {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате
                                        добавления (новые)</option>
                                    <option value="created_at" data-direction="asc"
                                        {{ $sort === 'created_at' && $direction === 'asc' ? 'selected' : '' }}>По дате
                                        добавления (старые)</option>
                                    <option value="name" data-direction="asc"
                                        {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По имени (А-Я)
                                    </option>
                                    <option value="name" data-direction="desc"
                                        {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По имени (Я-А)
                                    </option>
                                </select>
                                <input type="hidden" name="direction" value="{{ $direction }}"
                                    id="sort-direction-desktop">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- На странице -->
                        <div class="min-w-[130px]">
                            <label for="per-page-filter"
                                class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">На странице</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-table-list text-slate-400 text-sm"></i>
                                </div>
                                <select id="per-page-filter" name="per_page" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопка сброса фильтров -->
                        @if ($period || $activity || $sort !== 'created_at' || $direction !== 'desc' || $perPage != 15)
                            <div class="ml-auto">
                                <a href="{{ route('clients.index', ['search' => $search]) }}"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                                    <i class="fa-solid fa-rotate-left text-sm"></i>
                                    <span>Сбросить фильтры</span>
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Список клиентов -->
        @if ($clients->count() > 0)
            <!-- Таблица для больших экранов -->
            <div class="hidden md:block">
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Клиент</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Телефон</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Записей</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Добавлен</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach ($clients as $client)
                                @php
                                    $totalAppointments = $client->appointments_count ?? 0;
                                    $hasActivity = $totalAppointments > 0;
                                    $isNew = $client->created_at->isToday() || $client->created_at->isYesterday();
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($client->full_name) }}&background=6366f1&color=fff&size=40" 
                                                class="w-10 h-10 rounded-full" 
                                                alt="{{ $client->full_name }}">
                                            <div class="ml-3">
                                                <a href="{{ route('clients.show', $client) }}" 
                                                    class="text-sm font-medium text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                    {{ $client->full_name }}
                                                </a>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $client->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button
                                            @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                            class="text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            {{ $client->phone }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        {{ $client->email ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white font-medium">
                                        {{ $totalAppointments }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($hasActivity)
                                            <span class="px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/20 rounded-full border border-emerald-200 dark:border-emerald-600">
                                                Активный
                                            </span>
                                        @elseif ($isNew)
                                            <span class="px-2.5 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-500/20 rounded-full border border-blue-200 dark:border-blue-600">
                                                Новый
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700">
                                                Неактивный
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                        {{ $client->created_at->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('clients.show', $client->id) }}" 
                                                class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                title="Просмотр">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('clients.edit', $client->id) }}" 
                                                class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                title="Редактировать">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button
                                                @click="openDeleteModal({{ $client->id }}, '{{ addslashes($client->full_name) }}')"
                                                class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                title="Удалить">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Карточки для мобильных -->
            <div class="md:hidden grid grid-cols-1 gap-4">
                @foreach ($clients as $client)
                    @php
                        $totalAppointments = $client->appointments_count ?? 0;
                        $upcomingAppointments = $client->upcoming_appointments_count ?? 0;
                        $hasActivity = $totalAppointments > 0;
                    @endphp
                    <div
                        class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                        <!-- Заголовок карточки -->
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('clients.show', $client) }}"
                                    class="flex items-center gap-3 min-w-0 flex-1">
                                    <div
                                        class="h-9 w-9 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-medium text-xs">
                                        {{ $client->initials }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-base font-medium text-slate-900 dark:text-white truncate">
                                            {{ $client->full_name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ $client->created_at->format('d.m.Y') }}
                                            @if ($totalAppointments > 0)
                                                • {{ $totalAppointments }}
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Содержимое карточки -->
                        <div class="px-4 py-3 space-y-3">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Телефон</p>
                                <button
                                    @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                    class="text-sm text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $client->phone }}
                                </button>
                            </div>
                            @if ($client->email)
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Email</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 break-all">
                                        {{ $client->email }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Действия -->
                        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <button
                                    @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                                    Позвонить
                                </button>
                                <a href="{{ route('clients.show', $client) }}"
                                    class="px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors">
                                    Просмотр
                                </a>
                                <a href="{{ route('clients.edit', $client) }}"
                                    class="px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors">
                                    Изменить
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Единая форма удаления -->
            <form method="POST" id="delete-form" class="hidden">
                @csrf
                @method('DELETE')
            </form>

            <!-- Пагинация -->
            @if ($clients->hasPages())
                @php
                    $currentPage = $clients->currentPage();
                    $lastPage = $clients->lastPage();

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
                <div
                    class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 px-6 py-4">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-5">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            <span class="font-medium">Показано</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $clients->firstItem() }}</span>
                            <span class="font-medium">—</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $clients->lastItem() }}</span>
                            <span class="font-medium">из</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $clients->total() }}</span>
                            <span class="font-medium">клиентов</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Кнопка "В начало" -->
                            @if ($currentPage > 1)
                                <a href="{{ $clients->url(1) }}"
                                    class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md"
                                    title="В начало">
                                    <i class="fa-solid fa-angles-left text-sm"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm"
                                    title="В начало">
                                    <i class="fa-solid fa-angles-left text-sm"></i>
                                </button>
                            @endif

                            <!-- Кнопка "Назад" -->
                            @if ($clients->onFirstPage())
                                <button disabled
                                    class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-chevron-left text-sm"></i>
                                </button>
                            @else
                                <a href="{{ $clients->previousPageUrl() }}"
                                    class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                    <i class="fa-solid fa-chevron-left text-sm"></i>
                                </a>
                            @endif

                            <!-- Номера страниц -->
                            @foreach ($clients->getUrlRange($startPage, $endPage) as $page => $url)
                                @if ($page == $currentPage)
                                    <button disabled
                                        class="w-10 h-10 flex items-center justify-center bg-indigo-600 text-white rounded-lg font-semibold cursor-default">
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 font-medium">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <!-- Кнопка "Вперед" -->
                            @if ($clients->hasMorePages())
                                <a href="{{ $clients->nextPageUrl() }}"
                                    class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                </button>
                            @endif

                            <!-- Кнопка "В конец" -->
                            @if ($currentPage < $lastPage)
                                <a href="{{ $clients->url($lastPage) }}"
                                    class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md"
                                    title="В конец">
                                    <i class="fa-solid fa-angles-right text-sm"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm"
                                    title="В конец">
                                    <i class="fa-solid fa-angles-right text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Пустое состояние -->
            <div
                class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div
                        class="h-16 w-16 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                        @if ($search || $period)
                            Клиенты не найдены
                        @else
                            База клиентов пуста
                        @endif
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                        @if ($search || $period)
                            Попробуйте изменить параметры поиска или очистить фильтры
                        @else
                            Начните работу, добавив первого клиента
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        @if ($search || $period)
                            <a href="{{ route('clients.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                                <span>Очистить фильтры</span>
                            </a>
                        @endif
                        <a href="{{ route('clients.create') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-user-plus text-sm"></i>
                            <span>Добавить клиента</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Модальное окно для номера телефона -->
        <div x-show="showPhoneModal" @click.away="closePhoneModal()" @keydown.escape.window="closePhoneModal()"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Контактная информация</h3>
                    <button @click="closePhoneModal()"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-4 py-4">
                    <div class="mb-4">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Клиент</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Телефон</p>
                        <p class="text-xl font-semibold text-slate-900 dark:text-white" x-text="phoneDisplay"></p>
                    </div>
                    <div class="space-y-2">
                        <a :href="`tel:${phone}`"
                            class="md:hidden w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-phone text-sm"></i>
                            <span>Позвонить</span>
                        </a>
                        <button @click="navigator.clipboard.writeText(phone); closePhoneModal();"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            <i class="fa-regular fa-copy text-sm"></i>
                            <span>Копировать номер</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <div x-show="showDeleteModal" @click.away="closeDeleteModal()" @keydown.escape.window="closeDeleteModal()"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                    <button @click="closeDeleteModal()"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-4 py-4">
                    <p class="text-sm text-slate-700 dark:text-slate-300 mb-4">
                        Вы уверены, что хотите удалить клиента <span
                            class="font-semibold text-slate-900 dark:text-white" x-text="clientName"></span>?
                    </p>
                    <p class="text-xs text-rose-600 dark:text-rose-400 mb-4">
                        Это действие нельзя отменить.
                    </p>
                    <div class="flex gap-2">
                        <button @click="closeDeleteModal()"
                            class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Отмена
                        </button>
                        <button @click="confirmDelete()"
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                            Удалить
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
            const mobileInput = document.getElementById('sort-direction');
            const desktopInput = document.getElementById('sort-direction-desktop');
            if (mobileInput) mobileInput.value = direction;
            if (desktopInput) desktopInput.value = direction;
        }
    </script>

@endsection
