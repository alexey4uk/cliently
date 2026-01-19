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

    <div x-data="{
        showPhoneModal: false,
        phone: '',
        phoneDisplay: '',
        client: '',
        showDeleteModal: false,
        clientToDelete: null,
        clientName: '',
        showFilters: {{ $period || request('activity') || $sort !== 'created_at' || $direction !== 'desc' || $perPage != 15 ? 'true' : 'false' }},
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

        <!-- Заголовок с кнопками -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-users text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                            Клиенты
                        </h1>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">
                            Управление клиентской базой и контактами
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a href="{{ route('clients.export', request()->query()) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200">
                        <i class="fa-solid fa-file-csv text-slate-500"></i>
                        <span>Экспорт</span>
                    </a>
                    <a href="{{ route('clients.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl text-sm font-medium hover:from-indigo-700 hover:to-indigo-800 shadow-sm hover:shadow-md transition-all duration-200">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Добавить клиента</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <div class="space-y-4">
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
                            class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 shadow-sm">
                    </div>
                    <button type="submit"
                        class="h-12 w-12 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-center transition-all duration-200 flex-shrink-0 hover:from-indigo-700 hover:to-indigo-800 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>
                    <button type="button" @click="mobileShowFilters = !mobileShowFilters"
                        class="h-12 w-12 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 flex-shrink-0 shadow-sm"
                        :class="mobileShowFilters ?
                            'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' : ''">
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
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg p-6 space-y-5 backdrop-blur-sm"
                    style="display: none;">
                    <form method="GET" action="{{ route('clients.index') }}" class="space-y-4">
                        <input type="hidden" name="view" value="table">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <div>
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">Период</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                                </div>
                                <select name="period" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">Сортировка</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-arrow-up-wide-short text-slate-400 text-sm"></i>
                                </div>
                                <select name="sort" onchange="updateSortDirection(this); this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">Активность</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chart-line text-slate-400 text-sm"></i>
                                </div>
                                <select name="activity" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">На
                                странице</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-table-list text-slate-400 text-sm"></i>
                                </div>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                            Поиск клиентов
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                            </div>
                            <input id="search-input" type="text" name="search" value="{{ $search }}"
                                placeholder="Поиск по имени, телефону или email..."
                                class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 shadow-sm">
                        </div>
                    </div>

                    <!-- Кнопка поиска -->
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-medium rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 flex-shrink-0 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>

                    <!-- Кнопка фильтров -->
                    <button @click="toggleFilters()" type="button"
                        class="inline-flex items-center justify-center gap-3 px-5 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 flex-shrink-0 ml-auto shadow-sm"
                        :class="showFilters ? 'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' :
                            ''">
                        <i class="fa-solid fa-sliders text-sm"></i>
                        <span x-text="showFilters ? 'Скрыть фильтры' : 'Показать фильтры'"></span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-200 text-sm"
                            :class="showFilters ? 'rotate-180' : ''"></i>
                    </button>
                </form>

                <!-- Панель дополнительных фильтров -->
                <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4 scale-98"
                    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 transform -translate-y-4 scale-98"
                    class="bg-slate-50/80 dark:bg-slate-800/30 backdrop-blur-sm rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm"
                    style="display: none;">
                    <!-- Дополнительные фильтры -->
                    <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap items-end gap-4">
                        <input type="hidden" name="view" value="table">
                        <input type="hidden" name="search" value="{{ $search }}">

                        <!-- Фильтр по периоду -->
                        <div class="min-w-[180px]">
                            <label for="period-filter"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                                Период
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                                </div>
                                <select id="period-filter" name="period" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">Активность</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chart-line text-slate-400 text-sm"></i>
                                </div>
                                <select id="activity-filter" name="activity" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">Сортировка</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-arrow-up-wide-short text-slate-400 text-sm"></i>
                                </div>
                                <select id="sort-filter" name="sort"
                                    onchange="updateSortDirection(this); this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">На
                                странице</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-table-list text-slate-400 text-sm"></i>
                                </div>
                                <select id="per-page-filter" name="per_page" onchange="this.form.submit()"
                                    class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
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
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead
                            class="bg-slate-50/80 dark:bg-slate-800/30 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Клиент</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Телефон</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Дата добавления</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($clients as $client)
                                <tr
                                    class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-all duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <span class="text-white font-bold text-sm">{{ $client->initials }}</span>
                                            </div>
                                            <a href="{{ route('clients.show', $client) }}"
                                                class="text-sm font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                {{ $client->full_name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                            class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors group">
                                            <i
                                                class="fa-solid fa-phone text-xs opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                            {{ $client->phone }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($client->email)
                                            <div
                                                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                                                <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                                                {{ $client->email }}
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-400 dark:text-slate-500 italic">Не
                                                указан</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-calendar-plus text-xs text-slate-400"></i>
                                            {{ $client->created_at->format('d.m.Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div x-data="{
                                            open: false,
                                            updatePosition() {
                                                if (!this.open) return;
                                                $nextTick(() => {
                                                    const button = this.$el.querySelector('button');
                                                    const menu = this.$el.querySelector('[x-show]');
                                                    if (!button || !menu) return;
                                        
                                                    const rect = button.getBoundingClientRect();
                                                    const menuHeight = 200; // Approximate menu height
                                                    const menuWidth = 224; // 56 * 4 (w-56 in rem)
                                        
                                                    // Get scroll positions
                                                    const scrollY = window.scrollY;
                                                    const scrollX = window.scrollX;
                                        
                                                    // Get viewport dimensions
                                                    const viewportHeight = window.innerHeight;
                                                    const viewportWidth = window.innerWidth;
                                        
                                                    // Set menu to fixed positioning
                                                    menu.style.position = 'fixed';
                                                    menu.style.zIndex = '1000';
                                        
                                                    // Calculate available space
                                                    const spaceAbove = rect.top - scrollY;
                                                    const spaceBelow = viewportHeight - (rect.bottom - scrollY);
                                                    const spaceLeft = rect.left - scrollX;
                                                    const spaceRight = viewportWidth - (rect.right - scrollX);
                                        
                                                    // Reset positions
                                                    menu.style.top = '';
                                                    menu.style.bottom = '';
                                                    menu.style.left = '';
                                                    menu.style.right = '';
                                        
                                                    // Position vertically - prefer below, then above
                                                    if (spaceBelow >= menuHeight) {
                                                        // Place below button
                                                        menu.style.top = (rect.bottom + 8 + scrollY) + 'px';
                                                    } else if (spaceAbove >= menuHeight) {
                                                        // Place above button
                                                        menu.style.bottom = (viewportHeight - rect.top + 8) + 'px';
                                                    } else {
                                                        // Default to below if not enough space anywhere
                                                        menu.style.top = (rect.bottom + 8 + scrollY) + 'px';
                                                    }
                                        
                                                    // Position horizontally - prefer left, then right
                                                    if (spaceLeft >= menuWidth) {
                                                        // Place to the left of button
                                                        menu.style.left = (rect.right - menuWidth + scrollX) + 'px';
                                                    } else if (spaceRight >= menuWidth) {
                                                        // Place to the right of button
                                                        menu.style.left = (rect.left + scrollX) + 'px';
                                                    } else {
                                                        // Default to left if not enough space anywhere
                                                        menu.style.left = (rect.right - menuWidth + scrollX) + 'px';
                                                    }
                                                });
                                            }
                                        }" x-init="$watch('open', () => updatePosition())"
                                            @resize.window="updatePosition()" @scroll.window="updatePosition()"
                                            class="relative inline-block text-left">
                                            <div>
                                                <button @click="open = !open" @click.outside="open = false"
                                                    type="button"
                                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                                    id="menu-button" aria-expanded="true" aria-haspopup="true">
                                                    Действия
                                                    <svg class="-mr-0.5 ml-1 h-3.5 w-3.5"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="origin-top-right absolute right-0 mt-1 w-48 rounded-md shadow-lg bg-white dark:bg-slate-900 ring-1 ring-black ring-opacity-5 dark:ring-slate-700 focus:outline-none z-50 py-1"
                                                style="display: none;" role="menu" aria-orientation="vertical"
                                                aria-labelledby="menu-button" tabindex="-1">
                                                <!-- Просмотр -->
                                                <a href="{{ route('clients.show', $client->id) }}"
                                                    class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                    role="menuitem">
                                                    <i class="fa-regular fa-eye w-4 text-center"></i>
                                                    <span>Просмотр</span>
                                                </a>

                                                <!-- Редактирование -->
                                                <a href="{{ route('clients.edit', $client->id) }}"
                                                    class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                    role="menuitem">
                                                    <i class="fa-regular fa-pen-to-square w-4 text-center"></i>
                                                    <span>Редактировать</span>
                                                </a>

                                                <!-- Удаление -->
                                                <form method="POST" action="{{ route('clients.destroy', $client->id) }}"
                                                    onsubmit="return confirm('Вы уверены, что хотите удалить клиента? Это действие нельзя отменить.');"
                                                    role="none">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" @click="open = false"
                                                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                                                        role="menuitem">
                                                        <i class="fa-regular fa-trash-can w-4 text-center"></i>
                                                        <span>Удалить</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Карточки для мобильных -->
            <div class="md:hidden grid grid-cols-1 gap-5">
                @foreach ($clients as $client)
                    @php
                        $totalAppointments = $client->appointments_count;
                        $upcomingAppointments = $client->upcoming_appointments_count;
                        $hasActivity = $totalAppointments > 0;
                    @endphp
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 overflow-hidden group">
                        <!-- Заголовок карточки -->
                        <div
                            class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-800/30 dark:to-slate-800/20">
                            <div class="flex items-start justify-between gap-4">
                                <a href="{{ route('clients.show', $client) }}"
                                    class="flex items-center gap-3 min-w-0 flex-1 group">
                                    <div
                                        class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow-md relative group-hover:shadow-lg transition-shadow">
                                        {{ $client->initials }}
                                        @if ($hasActivity)
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 flex items-center justify-center">
                                                <i class="fa-solid fa-check text-white text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3
                                            class="text-base font-bold text-slate-900 dark:text-white truncate mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $client->full_name }}
                                        </h3>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2">
                                            <i class="fa-solid fa-calendar-plus text-xs"></i>
                                            <span>Добавлен {{ $client->created_at->format('d.m.Y') }}</span>
                                            @if ($totalAppointments > 0)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-medium border border-emerald-200 dark:border-emerald-600">
                                                    <i class="fa-solid fa-calendar-check text-xs"></i>
                                                    {{ $totalAppointments }}
                                                    {{ $totalAppointments === 1 ? 'запись' : ($totalAppointments < 5 ? 'записи' : 'записей') }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Содержимое карточки -->
                        <div class="px-5 py-4 space-y-4">
                            <!-- Телефон -->
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-10 w-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shadow-sm">
                                        <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">
                                        Телефон</p>
                                    <button
                                        @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                        class="text-base font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 break-all text-left transition-colors hover:underline">
                                        {{ $client->phone }}
                                    </button>
                                </div>
                            </div>

                            <!-- Email -->
                            @if ($client->email)
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shadow-sm">
                                            <i
                                                class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">
                                            Email</p>
                                        <p class="text-base font-semibold text-slate-900 dark:text-white break-all">
                                            {{ $client->email }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Действия -->
                        <div
                            class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-800/30 dark:to-slate-800/20 flex-shrink-0">
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <button
                                        @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                        <i class="fa-solid fa-phone text-sm"></i>
                                        <span>Позвонить</span>
                                    </button>
                                    <div class="flex gap-2">
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="inline-flex items-center justify-center gap-2 p-2.5 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm hover:bg-slate-200 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200"
                                            title="Просмотр">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}"
                                            class="inline-flex items-center justify-center gap-2 p-2.5 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-300 dark:hover:border-amber-600 hover:text-amber-600 dark:hover:text-amber-400 transition-all duration-200"
                                            title="Редактировать">
                                            <i class="fa-solid fa-user-pen text-sm"></i>
                                        </a>
                                        <button
                                            @click="openDeleteModal({{ $client->id }}, '{{ addslashes($client->full_name) }}')"
                                            class="inline-flex items-center justify-center gap-2 p-2.5 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-300 dark:hover:border-rose-600 hover:text-rose-600 dark:hover:text-rose-400 transition-all duration-200"
                                            title="Удалить">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
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
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm px-6 py-5">
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
                                        class="w-11 h-11 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-bold cursor-default shadow-sm">
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 font-medium shadow-sm hover:shadow-md">
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
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 md:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div
                        class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fa-solid fa-users text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                        @if ($search || $period)
                            Клиенты не найдены
                        @else
                            База клиентов пуста
                        @endif
                    </h3>
                    <p class="text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                        @if ($search || $period)
                            Попробуйте изменить параметры поиска или очистить фильтры для получения других результатов
                        @else
                            Начните работу с системой, добавив первого клиента в вашу базу данных
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @if ($search || $period)
                            <a href="{{ route('clients.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                                <span>Очистить фильтры</span>
                            </a>
                        @endif
                        <a href="{{ route('clients.create') }}"
                            class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-xl hover:from-indigo-700 hover:to-indigo-800 shadow-sm hover:shadow-lg transition-all duration-200">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Добавить первого клиента</span>
                        </a>
                    </div>
                </div>
            </div>
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
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden">
                <div
                    class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-800/50 dark:to-slate-800/30">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Контактная информация</h3>
                    <button @click="closePhoneModal()"
                        class="h-10 w-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">
                            Клиент</p>
                        <div class="flex items-center gap-4">
                            <div
                                class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <i class="fa-solid fa-user text-white text-lg"></i>
                            </div>
                            <p class="text-lg font-bold text-slate-900 dark:text-white" x-text="client"></p>
                        </div>
                    </div>
                    <div class="mb-8">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">
                            Телефон</p>
                        <div class="flex items-center gap-4">
                            <div
                                class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <i class="fa-solid fa-phone text-white text-lg"></i>
                            </div>
                            <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight"
                                x-text="phoneDisplay"></p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a :href="`tel:${phone}`"
                            class="md:hidden w-full inline-flex items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-5 py-4 text-base font-bold text-white hover:from-emerald-700 hover:to-emerald-800 shadow-sm hover:shadow-lg transition-all duration-200">
                            <i class="fa-solid fa-phone text-lg"></i>
                            <span>Позвонить</span>
                        </a>
                        <button @click="navigator.clipboard.writeText(phone); closePhoneModal();"
                            class="w-full inline-flex items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 md:from-slate-100 md:to-slate-200 md:dark:from-slate-800 md:dark:to-slate-700 px-5 py-4 text-base font-bold text-white md:text-slate-700 md:dark:text-slate-300 hover:from-indigo-700 hover:to-indigo-800 md:hover:from-slate-200 md:hover:to-slate-300 md:dark:hover:from-slate-700 md:dark:hover:to-slate-600 shadow-sm hover:shadow-lg transition-all duration-200">
                            <i class="fa-regular fa-copy text-lg"></i>
                            <span>Копировать номер</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <div x-show="showDeleteModal" @click.away="closeDeleteModal()" @keydown.escape.window="closeDeleteModal()"
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
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden">
                <div
                    class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-rose-50 to-pink-50/50 dark:from-rose-900/20 dark:to-pink-900/10">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                    </div>
                    <button @click="closeDeleteModal()"
                        class="h-10 w-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div class="flex items-start gap-4 mb-6">
                        <div
                            class="h-12 w-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i class="fa-solid fa-user-xmark text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-base text-slate-700 dark:text-slate-300 leading-relaxed">
                                Вы уверены, что хотите удалить клиента <span
                                    class="font-bold text-slate-900 dark:text-white" x-text="clientName"></span>?
                            </p>
                            <p class="text-sm text-rose-600 dark:text-rose-400 mt-2 font-medium">
                                Это действие нельзя отменить.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button @click="closeDeleteModal()"
                            class="flex-1 px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                            Отмена
                        </button>
                        <button @click="confirmDelete()"
                            class="flex-1 px-5 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-semibold transition-all duration-200 shadow-sm hover:shadow-lg">
                            Удалить клиента
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
