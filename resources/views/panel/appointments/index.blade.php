@extends('layouts.panel')

@section('title', 'Записи')

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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
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
        showFilters: {{ $search || $statusFilter || $businessFilter || $dateFilter || $sort !== 'date' || $direction !== 'desc' || $perPage != 20 ? 'true' : 'false' }}
    }" class="space-y-6">

        <!-- Заголовок с статистикой -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-calendar-check text-white text-lg"></i>
                    </div>
            <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Записи</h1>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Управление всеми записями системы</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $appointments->total() }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Всего записей</p>
                </div>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <div class="space-y-4">
            <!-- Поиск и кнопка фильтров -->
            <form method="GET" action="{{ route('panel.appointments') }}" class="flex items-end gap-4">
                <!-- Поиск -->
                <div class="flex-1 max-w-lg">
                    <label for="search-input" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                        Поиск записей
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input id="search-input" type="text" name="search" value="{{ $search }}"
                            placeholder="Поиск по клиенту, услуге или мастеру..."
                            class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 shadow-sm">
                    </div>
                </div>

                <!-- Кнопка поиска -->
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-medium rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 flex-shrink-0 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                <!-- Кнопка фильтров -->
                <button @click="showFilters = !showFilters" type="button"
                    class="inline-flex items-center justify-center gap-3 px-5 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 flex-shrink-0 ml-auto shadow-sm"
                    :class="showFilters ? 'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' : ''">
                    <i class="fa-solid fa-sliders text-sm"></i>
                    <span x-text="showFilters ? 'Скрыть фильтры' : 'Показать фильтры'"></span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-200 text-sm" :class="showFilters ? 'rotate-180' : ''"></i>
                </button>
            </form>

            <!-- Панель фильтров -->
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4 scale-98"
                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 transform -translate-y-4 scale-98"
                class="bg-slate-50/80 dark:bg-slate-800/30 backdrop-blur-sm rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm"
                style="display: none;">
                <form method="GET" action="{{ route('panel.appointments') }}" class="flex flex-wrap items-end gap-4">
                    <input type="hidden" name="search" value="{{ $search }}">

                    <!-- Фильтр по статусу -->
                    <div class="min-w-[160px]">
                        <label for="status-filter" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                            Статус
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-circle-info text-slate-400 text-sm"></i>
                            </div>
                            <select id="status-filter" name="status" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>Все статусы</option>
                                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Ожидает</option>
                                <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Подтверждена</option>
                                <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Завершена</option>
                                <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Отменена</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Фильтр по бизнесу -->
                    <div class="min-w-[160px]">
                        <label for="business-filter" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                            Бизнес
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-building text-slate-400 text-sm"></i>
                            </div>
                            <select id="business-filter" name="business_id" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="" {{ $businessFilter === '' ? 'selected' : '' }}>Все бизнесы</option>
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}" {{ $businessFilter == $business->id ? 'selected' : '' }}>
                                        {{ $business->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Фильтр по дате -->
                    <div class="min-w-[160px]">
                        <label for="date-filter" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                            Дата
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                            </div>
                            <input id="date-filter" type="date" name="date" value="{{ $dateFilter }}"
                                onchange="this.form.submit()"
                                class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white shadow-sm">
                        </div>
                    </div>

                    <!-- Сортировка -->
                    <div class="min-w-[180px]">
                        <label for="sort-filter" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">Сортировка</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-arrow-up-wide-short text-slate-400 text-sm"></i>
                            </div>
                            <select id="sort-filter" name="sort" onchange="updateSortDirection(this); this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="date" data-direction="desc" {{ $sort === 'date' && $direction === 'desc' ? 'selected' : '' }}>По дате (новые)</option>
                                <option value="date" data-direction="asc" {{ $sort === 'date' && $direction === 'asc' ? 'selected' : '' }}>По дате (старые)</option>
                                <option value="client" data-direction="asc" {{ $sort === 'client' && $direction === 'asc' ? 'selected' : '' }}>По клиенту (А-Я)</option>
                                <option value="client" data-direction="desc" {{ $sort === 'client' && $direction === 'desc' ? 'selected' : '' }}>По клиенту (Я-А)</option>
                                <option value="service" data-direction="asc" {{ $sort === 'service' && $direction === 'asc' ? 'selected' : '' }}>По услуге (А-Я)</option>
                                <option value="service" data-direction="desc" {{ $sort === 'service' && $direction === 'desc' ? 'selected' : '' }}>По услуге (Я-А)</option>
                                <option value="status" data-direction="asc" {{ $sort === 'status' && $direction === 'asc' ? 'selected' : '' }}>По статусу</option>
                            </select>
                            <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- На странице -->
                    <div class="min-w-[130px]">
                        <label for="per-page-filter" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">На странице</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-table-list text-slate-400 text-sm"></i>
                            </div>
                            <select id="per-page-filter" name="per_page" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка сброса фильтров -->
                    @if ($search || $statusFilter || $businessFilter || $dateFilter || $sort !== 'date' || $direction !== 'desc' || $perPage != 20)
                        <div class="ml-auto">
                            <a href="{{ route('panel.appointments') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                                <span>Сбросить фильтры</span>
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Список записей -->
        @if ($appointments->count() > 0)
            <!-- Таблица для десктопа -->
            <div class="hidden md:block">
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <table class="w-full">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/30 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Клиент</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Услуга</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Мастер</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Бизнес</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Дата и время</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($appointments as $appointment)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-bold text-xs">{{ substr($appointment->client->first_name ?? 'Н', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->client->full_name }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $appointment->client->phone }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->service->name }}</p>
                                        @if($appointment->duration)
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $appointment->duration }} мин</p>
                                        @endif
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appointment->master)
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->master->name }}</p>
                                        @else
                                            <span class="text-sm text-slate-400 dark:text-slate-500 italic">Не назначен</span>
                                        @endif
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->business->name }}</p>
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->date->format('d.m.Y') }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $appointment->time }}</p>
                                            </div>
                                        </div>
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                        @if($appointment->status === 'confirmed') bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400
                                        @elseif($appointment->status === 'pending') bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400
                                            @elseif($appointment->status === 'completed') bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400
                                        @elseif($appointment->status === 'cancelled') bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400
                                            @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 @endif">
                                            @switch($appointment->status)
                                                @case('pending')
                                                    Ожидает
                                                    @break
                                                @case('confirmed')
                                                    Подтверждена
                                                    @break
                                                @case('completed')
                                                    Завершена
                                                    @break
                                                @case('cancelled')
                                                    Отменена
                                                    @break
                                                @default
                                        {{ $appointment->status }}
                                            @endswitch
                                    </span>
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('panel.appointments.update')
                                                <a href="{{ route('panel.appointments.edit', $appointment) }}"
                                                   class="inline-flex items-center justify-center p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                                                   title="Редактировать">
                                                    <i class="fa-solid fa-pencil text-sm"></i>
                                                </a>
                                            @endcan
                                            @can('panel.appointments.delete')
                                                @if(!($appointment->dateTime->isPast() && $appointment->status === 'confirmed'))
                                                    <form method="POST" action="{{ route('panel.appointments.destroy', $appointment) }}"
                                                          onsubmit="return confirm('Вы уверены, что хотите удалить эту запись?');"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center p-1.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors"
                                                                title="Удалить">
                                                            <i class="fa-solid fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
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
                @foreach ($appointments as $appointment)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 overflow-hidden group">
                        <!-- Заголовок карточки -->
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-800/30 dark:to-slate-800/20">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow-md">
                                        {{ substr($appointment->client->first_name ?? 'Н', 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white truncate mb-1">
                                            {{ $appointment->client->full_name }}
                                        </h3>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2">
                                            <i class="fa-solid fa-calendar text-xs"></i>
                                            <span>{{ $appointment->date->format('d.m.Y') }} в {{ $appointment->time }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-medium border border-emerald-200 dark:border-emerald-600">
                                        <i class="fa-solid fa-check-circle text-xs"></i>
                                        @switch($appointment->status)
                                            @case('pending')
                                                Ожидает
                                                @break
                                            @case('confirmed')
                                                Подтверждена
                                                @break
                                            @case('completed')
                                                Завершена
                                                @break
                                            @case('cancelled')
                                                Отменена
                                                @break
                                            @default
                                                {{ $appointment->status }}
                                        @endswitch
                                    </span>
                                    <div class="flex gap-2">
                                        @can('panel.appointments.update')
                                            <a href="{{ route('panel.appointments.edit', $appointment) }}"
                                               class="inline-flex items-center justify-center p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                                               title="Редактировать">
                                                <i class="fa-solid fa-pencil text-xs"></i>
                                            </a>
                                        @endcan
                                        @can('panel.appointments.delete')
                                            @if(!($appointment->dateTime->isPast() && $appointment->status === 'confirmed'))
                                                <form method="POST" action="{{ route('panel.appointments.destroy', $appointment) }}"
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту запись?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center p-1.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors"
                                                            title="Удалить">
                                                        <i class="fa-solid fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Содержимое карточки -->
                        <div class="px-5 py-4 space-y-4">
                            <!-- Услуга -->
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shadow-sm">
                                        <i class="fa-solid fa-scissors text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Услуга</p>
                                    <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->service->name }}</p>
                                    @if($appointment->duration)
                                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $appointment->duration }} мин</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Мастер -->
                            @if($appointment->master)
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-user-tie text-blue-600 dark:text-blue-400 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Мастер</p>
                                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->master->name }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Бизнес -->
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-500/20 flex items-center justify-center shadow-sm">
                                        <i class="fa-solid fa-building text-slate-600 dark:text-slate-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Бизнес</p>
                                    <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->business->name }}</p>
                                </div>
                            </div>

                            <!-- Примечания -->
                            @if($appointment->notes)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-sticky-note text-amber-600 dark:text-amber-400 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Примечания</p>
                                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if ($appointments->hasPages())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm px-6 py-5">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-5">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            <span class="font-medium">Показано</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $appointments->firstItem() }}</span>
                            <span class="font-medium">—</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $appointments->lastItem() }}</span>
                            <span class="font-medium">из</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $appointments->total() }}</span>
                            <span class="font-medium">записей</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Кнопки навигации -->
                            @if ($appointments->onFirstPage())
                                <button disabled class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-chevron-left text-sm"></i>
                                </button>
                            @else
                                <a href="{{ $appointments->previousPageUrl() }}" class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                    <i class="fa-solid fa-chevron-left text-sm"></i>
                                </a>
                            @endif

                            <!-- Номера страниц -->
                            @foreach ($appointments->getUrlRange(1, $appointments->lastPage()) as $page => $url)
                                @if ($page == $appointments->currentPage())
                                    <button disabled class="w-11 h-11 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-bold cursor-default shadow-sm">
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $url }}" class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 font-medium shadow-sm hover:shadow-md">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <!-- Кнопка "Вперед" -->
                            @if ($appointments->hasMorePages())
                                <a href="{{ $appointments->nextPageUrl() }}" class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                </a>
                            @else
                                <button disabled class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Пустое состояние -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 md:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fa-solid fa-calendar-check text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                        @if ($search || $statusFilter || $businessFilter || $dateFilter)
                            Записи не найдены
                        @else
                            Записей пока нет
                        @endif
                    </h3>
                    <p class="text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                        @if ($search || $statusFilter || $businessFilter || $dateFilter)
                            Попробуйте изменить параметры поиска или очистить фильтры для получения других результатов
                        @else
                            Записи клиентов будут отображаться здесь после их создания в системе
                        @endif
                    </p>
                    @if ($search || $statusFilter || $businessFilter || $dateFilter)
                        <a href="{{ route('panel.appointments') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                            <i class="fa-solid fa-rotate-left text-sm"></i>
                            <span>Сбросить фильтры</span>
                        </a>
            @endif
        </div>
            </div>
        @endif
    </div>

    <script>
        function updateSortDirection(select) {
            const selectedOption = select.options[select.selectedIndex];
            const direction = selectedOption.getAttribute('data-direction');
            document.getElementById('sort-direction').value = direction;
        }
    </script>
@endsection
