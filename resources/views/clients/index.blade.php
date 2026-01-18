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
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg p-4 flex items-center gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-4 flex items-center gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400"></i>
            </div>
            <p class="text-sm font-medium text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200 transition-colors">
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white mb-1">
                    Клиенты
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Управление клиентской базой
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('clients.export', request()->query()) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span>Экспорт CSV</span>
                </a>
                <a href="{{ route('clients.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить клиента</span>
                </a>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <div class="space-y-4">
            <!-- Мобильная версия: поиск и кнопка фильтров -->
            <div class="md:hidden space-y-3">
                <!-- Всегда видимый поиск -->
                <form method="GET" action="{{ route('clients.index') }}" class="flex gap-2">
                    <input type="hidden" name="view" value="table">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400 text-xs sm:text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Поиск по имени, телефону или email..."
                            class="pl-9 sm:pl-10 pr-4 py-2 sm:py-2.5 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-200 text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400">
                    </div>
                    <button type="submit"
                        class="h-10 w-10 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center transition-colors flex-shrink-0">
                        <i class="fa-solid fa-search text-xs sm:text-sm"></i>
                    </button>
                    <button type="button" @click="mobileShowFilters = !mobileShowFilters"
                        class="h-10 w-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex-shrink-0">
                        <i class="fa-solid fa-filter text-xs sm:text-sm"></i>
                    </button>
                </form>

                <!-- Выпадающая панель дополнительных фильтров -->
                <div x-show="mobileShowFilters" @click.away="mobileShowFilters = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 space-y-3"
                    style="display: none;">
                    <form method="GET" action="{{ route('clients.index') }}" class="space-y-3">
                        <input type="hidden" name="view" value="table">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Период</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                </div>
                                <select name="period" onchange="this.form.submit()"
                                    class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $period === '' ? 'selected' : '' }}>Все время</option>
                                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
                                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Последняя неделя
                                    </option>
                                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Последний месяц
                                    </option>
                                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Последний год
                                    </option>
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
                                    <option value="name" data-direction="asc"
                                        {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По имени (А-Я)
                                    </option>
                                    <option value="name" data-direction="desc"
                                        {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По имени (Я-А)
                                    </option>
                                    <option value="created_at" data-direction="desc"
                                        {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате
                                        добавления</option>
                                </select>
                                <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction">
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Активность</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-user-check text-slate-400 text-xs"></i>
                                </div>
                                <select name="activity" onchange="this.form.submit()"
                                    class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $activity === '' ? 'selected' : '' }}>Все клиенты</option>
                                    <option value="active" {{ $activity === 'active' ? 'selected' : '' }}>С записями
                                    </option>
                                    <option value="inactive" {{ $activity === 'inactive' ? 'selected' : '' }}>Без записей
                                    </option>
                                </select>
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
                    @if ($period || request('activity'))
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('clients.index') }}"
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
                <form method="GET" action="{{ route('clients.index') }}" class="flex items-end gap-3">
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
                                placeholder="Поиск по имени, телефону или email..."
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
                    <form method="GET" action="{{ route('clients.index') }}"
                        class="flex flex-wrap items-end gap-3">
                        <input type="hidden" name="view" value="table">
                        <input type="hidden" name="search" value="{{ $search }}">

                        <!-- Фильтр по периоду -->
                        <div class="min-w-[140px]">
                            <label for="period-filter"
                                class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Период
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                </div>
                                <select id="period-filter" name="period" onchange="this.form.submit()"
                                    class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $period === '' ? 'selected' : '' }}>Все время</option>
                                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
                                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Последняя неделя
                                    </option>
                                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Последний месяц
                                    </option>
                                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Последний год
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Фильтр по активности -->
                        <div class="min-w-[140px]">
                            <label for="activity-filter"
                                class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Активность</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-user-check text-slate-400 text-xs"></i>
                                </div>
                                <select id="activity-filter" name="activity" onchange="this.form.submit()"
                                    class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="" {{ $activity === '' ? 'selected' : '' }}>Все клиенты</option>
                                    <option value="active" {{ $activity === 'active' ? 'selected' : '' }}>С записями
                                    </option>
                                    <option value="inactive" {{ $activity === 'inactive' ? 'selected' : '' }}>Без записей
                                    </option>
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
                                <select id="sort-filter" name="sort" onchange="updateSortDirection(this); this.form.submit()"
                                    class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="created_at" data-direction="desc"
                                        {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате (новые)</option>
                                    <option value="created_at" data-direction="asc"
                                        {{ $sort === 'created_at' && $direction === 'asc' ? 'selected' : '' }}>По дате (старые)</option>
                                    <option value="name" data-direction="asc"
                                        {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По имени (А-Я)</option>
                                    <option value="name" data-direction="desc"
                                        {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По имени (Я-А)</option>
                                </select>
                                <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction-desktop">
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- На странице -->
                        <div class="min-w-[100px]">
                            <label for="per-page-filter"
                                class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">На странице</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-list text-slate-400 text-xs"></i>
                                </div>
                                <select id="per-page-filter" name="per_page" onchange="this.form.submit()"
                                    class="w-full pl-9 pr-8 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white appearance-none cursor-pointer">
                                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопка сброса фильтров -->
                        @if ($period || $activity || $sort !== 'created_at' || $direction !== 'desc' || $perPage != 15)
                            <div class="ml-auto">
                                <a href="{{ route('clients.index', ['search' => $search]) }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
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
                    class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Клиент</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Телефон</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Дата добавления</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($clients as $client)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $totalAppointments = $client->appointments_count;
                                            $hasActivity = $totalAppointments > 0;
                                        @endphp
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="flex items-center gap-3 group">
                                            <div
                                                class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow-sm relative">
                                                {{ $client->initials }}
                                                @if ($hasActivity)
                                                    <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-blue-500 border-2 border-white dark:border-slate-900"
                                                        title="{{ $totalAppointments }} {{ $totalAppointments === 1 ? 'запись' : ($totalAppointments < 5 ? 'записи' : 'записей') }}">
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                        {{ $client->full_name }}
                                                    </div>
                                                    @if ($hasActivity)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 text-xs font-medium">
                                                            <i class="fa-solid fa-calendar-check text-xs"></i>
                                                            {{ $totalAppointments }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    ID: {{ $client->id }}
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                            class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                                            {{ $client->phone }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($client->email)
                                            <div class="text-sm text-slate-700 dark:text-slate-300">
                                                {{ $client->email }}
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                        {{ $client->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-700/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors">
                                                <i class="fa-solid fa-phone text-xs"></i>
                                                <span class="hidden lg:inline">Контакт</span>
                                            </button>
                                            <a href="{{ route('clients.show', $client) }}"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-500/20 border border-slate-200 dark:border-slate-700/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-500/30 transition-colors">
                                                <i class="fa-regular fa-eye text-xs"></i>
                                                <span class="hidden lg:inline">Просмотр</span>
                                            </a>
                                            <a href="{{ route('clients.edit', $client) }}"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-700/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-500/30 transition-colors">
                                                <i class="fa-solid fa-pencil text-xs"></i>
                                                <span class="hidden lg:inline">Редакт.</span>
                                            </a>
                                            <button
                                                @click="openDeleteModal({{ $client->id }}, '{{ addslashes($client->full_name) }}')"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/30 transition-colors">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                                <span class="hidden lg:inline">Удалить</span>
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
                        $totalAppointments = $client->appointments_count;
                        $upcomingAppointments = $client->upcoming_appointments_count;
                        $hasActivity = $totalAppointments > 0;
                    @endphp
                    <div
                        class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                        <!-- Заголовок карточки -->
                        <div
                            class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <div class="flex items-start justify-between gap-3">
                                <a href="{{ route('clients.show', $client) }}"
                                    class="flex items-center gap-3 min-w-0 flex-1 group">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow-sm relative">
                                        {{ $client->initials }}
                                        @if ($hasActivity)
                                            <div
                                                class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-blue-500 border-2 border-white dark:border-slate-900">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3
                                            class="text-sm font-semibold text-slate-900 dark:text-white truncate mb-0.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $client->full_name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                            <span>Клиент с {{ $client->created_at->format('d.m.Y') }}</span>
                                            @if ($totalAppointments > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 text-xs font-medium">
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
                        <div class="p-4 space-y-3">
                            <!-- Телефон -->
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-phone text-slate-400 text-xs flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Телефон</p>
                                    <button
                                        @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium break-all text-left transition-colors">
                                        {{ $client->phone }}
                                    </button>
                                </div>
                            </div>

                            <!-- Email -->
                            @if ($client->email)
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-envelope text-slate-400 text-xs flex-shrink-0"></i>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Email</p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300 break-all truncate">
                                            {{ $client->email }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Действия -->
                        <div
                            class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex-shrink-0">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-700/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                    <span>Контакт</span>
                                </button>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('clients.show', $client) }}"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-500/20 border border-slate-200 dark:border-slate-700/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-500/30 transition-colors">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                        <span>Просмотр</span>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-700/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-500/30 transition-colors">
                                        <i class="fa-solid fa-pencil text-xs"></i>
                                        <span>Редактировать</span>
                                    </a>
                                    <button
                                        @click="openDeleteModal({{ $client->id }}, '{{ addslashes($client->full_name) }}')"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/30 transition-colors">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                        <span>Удалить</span>
                                    </button>
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
                    class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm px-4 py-3">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                            Показано <span
                                class="font-medium text-slate-900 dark:text-white">{{ $clients->firstItem() }}</span> -
                            <span class="font-medium text-slate-900 dark:text-white">{{ $clients->lastItem() }}</span> из
                            <span class="font-medium text-slate-900 dark:text-white">{{ $clients->total() }}</span>
                            клиентов
                        </div>

                        <div class="flex items-center space-x-1">
                            <!-- Кнопка "В начало" -->
                            @if ($currentPage > 1)
                                <a href="{{ $clients->url(1) }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300"
                                    title="В начало">
                                    <i class="fa-solid fa-angles-left text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400"
                                    title="В начало">
                                    <i class="fa-solid fa-angles-left text-xs"></i>
                                </button>
                            @endif

                            <!-- Кнопка "Назад" -->
                            @if ($clients->onFirstPage())
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                            @else
                                <a href="{{ $clients->previousPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            <!-- Номера страниц -->
                            @foreach ($clients->getUrlRange($startPage, $endPage) as $page => $url)
                                @if ($page == $currentPage)
                                    <button disabled
                                        class="w-8 h-8 flex items-center justify-center bg-indigo-600 text-white rounded-lg font-medium cursor-default text-xs sm:text-sm">
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 text-xs sm:text-sm">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <!-- Кнопка "Вперед" -->
                            @if ($clients->hasMorePages())
                                <a href="{{ $clients->nextPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            @endif

                            <!-- Кнопка "В конец" -->
                            @if ($currentPage < $lastPage)
                                <a href="{{ $clients->url($lastPage) }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300"
                                    title="В конец">
                                    <i class="fa-solid fa-angles-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400"
                                    title="В конец">
                                    <i class="fa-solid fa-angles-right text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Пустое состояние -->
            <div
                class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-8 md:p-12 text-center">
                <div class="max-w-sm mx-auto">
                    <div
                        class="h-16 w-16 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                        @if ($search || $period)
                            Клиенты не найдены
                        @else
                            База клиентов пуста
                        @endif
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        @if ($search || $period)
                            Попробуйте изменить параметры поиска или очистить фильтры для получения других результатов
                        @else
                            Начните работу с системой, добавив первого клиента в вашу базу данных
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        @if ($search || $period)
                            <a href="{{ route('clients.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <i class="fa-solid fa-xmark text-xs"></i>
                                <span>Очистить фильтры</span>
                            </a>
                        @endif
                        <a href="{{ route('clients.create') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Добавить клиента</span>
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
                <div
                    class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                    <button @click="closePhoneModal()"
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-4 md:px-6 py-4 md:py-5">
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
                    <div class="space-y-2">
                        <a :href="`tel:${phone}`"
                            class="md:hidden w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors">
                            <i class="fa-solid fa-phone text-sm"></i>
                            <span>Позвонить</span>
                        </a>
                        <button @click="navigator.clipboard.writeText(phone); closePhoneModal();"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 md:bg-slate-100 md:dark:bg-slate-800 px-4 py-3 text-sm font-medium text-white md:text-slate-700 md:dark:text-slate-300 hover:bg-indigo-700 md:hover:bg-slate-200 md:dark:hover:bg-slate-700 active:bg-indigo-800 transition-colors">
                            <i class="fa-regular fa-copy text-sm"></i>
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
                <div
                    class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Подтверждение удаления
                    </h3>
                    <button @click="closeDeleteModal()"
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-4 md:px-6 py-4 md:py-5">
                    <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 mb-6">
                        Вы уверены, что хотите удалить клиента <span class="font-semibold" x-text="clientName"></span>?
                        Это действие нельзя отменить.
                    </p>
                    <div class="flex gap-3">
                        <button @click="closeDeleteModal()"
                            class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Отмена
                        </button>
                        <button @click="confirmDelete()"
                            class="flex-1 px-4 py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium transition-colors">
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
