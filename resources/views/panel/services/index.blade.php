@extends('layouts.panel')

@section('title', 'Услуги')

@section('content')
    <div class="max-w-[1400px] mx-auto">
        <div x-data="{
        showFilters: {{ $search || $businessFilter || $sort !== 'created_at' || $direction !== 'desc' || $perPage != 20 ? 'true' : 'false' }}
    }" class="space-y-6">

        <!-- Заголовок с статистикой -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-briefcase text-white text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Услуги</h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Управление услугами системы</p>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4">
                    <div class="text-left sm:text-right">
                        @if(method_exists($services, 'total'))
                            <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ $services->total() }}</p>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Всего услуг</p>
                        @else
                            <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ $services->count() }}</p>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">На странице</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <div class="space-y-4">
            <!-- Поиск и кнопка фильтров -->
            <form method="GET" action="{{ route('panel.services') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 sm:gap-4">
                <!-- Поиск -->
                <div class="flex-1 min-w-0">
                    <label for="search-input" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">
                        Поиск услуг
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input id="search-input" type="text" name="search" value="{{ $search }}"
                            placeholder="Поиск по названию или описанию..."
                            class="pl-9 sm:pl-11 pr-3 sm:pr-4 py-2.5 sm:py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 shadow-sm">
                    </div>
                </div>

                <!-- Кнопка поиска -->
                <button type="submit" class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-xs sm:text-sm font-medium rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 flex-shrink-0 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-magnifying-glass text-xs sm:text-sm"></i>
                    <span class="ml-2 sm:hidden">Найти</span>
                </button>

                <!-- Кнопка фильтров -->
                <button @click="showFilters = !showFilters" type="button"
                    class="inline-flex items-center justify-center gap-2 sm:gap-3 px-4 sm:px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 flex-shrink-0 sm:ml-auto shadow-sm"
                    :class="showFilters ? 'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' : ''">
                    <i class="fa-solid fa-sliders text-xs sm:text-sm"></i>
                    <span class="hidden sm:inline" x-text="showFilters ? 'Скрыть фильтры' : 'Показать фильтры'"></span>
                    <span class="sm:hidden" x-text="showFilters ? 'Скрыть' : 'Фильтры'"></span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-200 text-xs sm:text-sm" :class="showFilters ? 'rotate-180' : ''"></i>
                </button>
            </form>

            <!-- Панель фильтров -->
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4 scale-98"
                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 transform -translate-y-4 scale-98"
                class="bg-slate-50/80 dark:bg-slate-800/30 backdrop-blur-sm rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-6 shadow-sm"
                style="display: none;">
                <form method="GET" action="{{ route('panel.services') }}" class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-end gap-3 sm:gap-4">
                    <input type="hidden" name="search" value="{{ $search }}">

                    <!-- Фильтр по бизнесу -->
                    <div class="flex-1 sm:min-w-[180px]">
                        <label for="business-filter" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 sm:mb-3 uppercase tracking-wide">
                            Бизнес
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-building text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                            <select id="business-filter" name="business_id" onchange="this.form.submit()"
                                class="w-full pl-9 sm:pl-11 pr-8 sm:pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-xs sm:text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="" {{ $businessFilter === '' ? 'selected' : '' }}>Все бизнесы</option>
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}" {{ $businessFilter == $business->id ? 'selected' : '' }}>
                                        {{ $business->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Сортировка -->
                    <div class="flex-1 sm:min-w-[200px]">
                        <label for="sort-filter" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 sm:mb-3 uppercase tracking-wide">Сортировка</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-arrow-up-wide-short text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                            <select id="sort-filter" name="sort" onchange="updateSortDirection(this); this.form.submit()"
                                class="w-full pl-9 sm:pl-11 pr-8 sm:pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-xs sm:text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="created_at" data-direction="desc" {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате добавления (новые)</option>
                                <option value="created_at" data-direction="asc" {{ $sort === 'created_at' && $direction === 'asc' ? 'selected' : '' }}>По дате добавления (старые)</option>
                                <option value="name" data-direction="asc" {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По названию (А-Я)</option>
                                <option value="name" data-direction="desc" {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По названию (Я-А)</option>
                                <option value="price" data-direction="desc" {{ $sort === 'price' && $direction === 'desc' ? 'selected' : '' }}>По цене (дорогие)</option>
                                <option value="price" data-direction="asc" {{ $sort === 'price' && $direction === 'asc' ? 'selected' : '' }}>По цене (дешевые)</option>
                                <option value="duration" data-direction="desc" {{ $sort === 'duration' && $direction === 'desc' ? 'selected' : '' }}>По длительности (дольше)</option>
                                <option value="duration" data-direction="asc" {{ $sort === 'duration' && $direction === 'asc' ? 'selected' : '' }}>По длительности (короче)</option>
                            </select>
                            <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction">
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- На странице -->
                    <div class="flex-1 sm:min-w-[130px]">
                        <label for="per-page-filter" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 sm:mb-3 uppercase tracking-wide">На странице</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-table-list text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                            <select id="per-page-filter" name="per_page" onchange="this.form.submit()"
                                class="w-full pl-9 sm:pl-11 pr-8 sm:pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-xs sm:text-sm text-slate-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs sm:text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка сброса фильтров -->
                    @if ($search || $businessFilter || $sort !== 'created_at' || $direction !== 'desc' || $perPage != 20)
                        <div class="w-full sm:w-auto sm:ml-auto">
                            <a href="{{ route('panel.services') }}"
                                class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 sm:px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                                <i class="fa-solid fa-rotate-left text-xs sm:text-sm"></i>
                                <span>Сбросить фильтры</span>
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Список услуг -->
        @if ($services->count() > 0)
            <!-- Таблица для десктопа -->
            <div class="hidden md:block">
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/30 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Услуга</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Цена</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Длительность</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Бизнес</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Записи</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($services as $service)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <i class="fa-solid fa-briefcase text-white text-sm"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $service->name }}</p>
                                                @if($service->description)
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate max-w-xs">{{ $service->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-600/30">
                                            {{ number_format($service->price, 0, '.', ' ') }} BYN
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-clock text-slate-400 text-xs"></i>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $service->duration }} мин</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $service->business->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium
                                            @if($service->appointments_count > 0) bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300
                                            @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 @endif rounded-full border
                                            @if($service->appointments_count > 0) border-indigo-200 dark:border-indigo-600/30
                                            @else border-slate-200 dark:border-slate-700 @endif">
                                            <i class="fa-solid fa-calendar-check text-xs mr-1"></i>
                                            {{ $service->appointments_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                            @if($service->is_active) bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-600/30
                                            @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 @endif">
                                            @if($service->is_active)
                                                <i class="fa-solid fa-circle-check text-xs mr-1"></i>
                                                Активна
                                            @else
                                                <i class="fa-solid fa-circle-xmark text-xs mr-1"></i>
                                                Неактивна
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('panel.services.view')
                                                <a href="{{ route('panel.services.show', $service) }}"
                                                   class="inline-flex items-center justify-center p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-md transition-colors"
                                                   title="Просмотр деталей">
                                                    <i class="fa-solid fa-eye text-sm"></i>
                                                </a>
                                            @endcan
                                            @can('panel.services.update')
                                                <a href="{{ route('panel.services.edit', $service) }}"
                                                   class="inline-flex items-center justify-center p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                                                   title="Редактировать">
                                                    <i class="fa-solid fa-pencil text-sm"></i>
                                                </a>
                                            @endcan
                                            @can('panel.services.delete')
                                                <form method="POST" action="{{ route('panel.services.destroy', $service) }}" 
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту услугу? Это действие нельзя отменить.');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center p-1.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors"
                                                            title="Удалить">
                                                        <i class="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </form>
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
            <div class="md:hidden grid grid-cols-1 gap-4 sm:gap-5">
                @foreach ($services as $service)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 overflow-hidden group">
                        <!-- Заголовок карточки -->
                        <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-800/30 dark:to-slate-800/20">
                            <div class="flex items-start justify-between gap-3 sm:gap-4">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md">
                                        <i class="fa-solid fa-briefcase text-white text-sm sm:text-base"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate mb-1">
                                            {{ $service->name }}
                                        </h3>
                                        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-600/30">
                                                <i class="fa-solid fa-ruble-sign text-xs"></i>
                                                {{ number_format($service->price, 0, '.', ' ') }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 text-xs font-semibold bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 rounded-lg border border-indigo-200 dark:border-indigo-600/30">
                                                <i class="fa-solid fa-clock text-xs"></i>
                                                {{ $service->duration }} мин
                                            </span>
                                            @if($service->is_active)
                                                <span class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-600/30">
                                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                                    Активна
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 text-xs font-semibold bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg border border-slate-200 dark:border-slate-700">
                                                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                                                    Неактивна
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-1.5 sm:gap-2 flex-shrink-0">
                                    @can('panel.services.view')
                                        <a href="{{ route('panel.services.show', $service) }}"
                                           class="inline-flex items-center justify-center p-1.5 sm:p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-md transition-colors"
                                           title="Просмотр деталей">
                                            <i class="fa-solid fa-eye text-xs sm:text-sm"></i>
                                        </a>
                                    @endcan
                                    @can('panel.services.update')
                                        <a href="{{ route('panel.services.edit', $service) }}"
                                           class="inline-flex items-center justify-center p-1.5 sm:p-2 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                                           title="Редактировать">
                                            <i class="fa-solid fa-pencil text-xs sm:text-sm"></i>
                                        </a>
                                    @endcan
                                    @can('panel.services.delete')
                                        <form method="POST" action="{{ route('panel.services.destroy', $service) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить эту услугу? Это действие нельзя отменить.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center p-1.5 sm:p-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors"
                                                    title="Удалить">
                                                <i class="fa-solid fa-trash text-xs sm:text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <!-- Содержимое карточки -->
                        <div class="px-4 sm:px-5 py-3 sm:py-4 space-y-3 sm:space-y-4">
                            <!-- Описание -->
                            @if($service->description)
                                <div class="flex items-start gap-2 sm:gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-slate-100 dark:bg-slate-500/20 flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-file-lines text-slate-600 dark:text-slate-400 text-xs sm:text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1.5 sm:mb-2">Описание</p>
                                        <p class="text-xs sm:text-sm text-slate-900 dark:text-white">{{ $service->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Бизнес -->
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-slate-100 dark:bg-slate-500/20 flex items-center justify-center shadow-sm">
                                        <i class="fa-solid fa-building text-slate-600 dark:text-slate-400 text-xs sm:text-sm"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1.5 sm:mb-2">Бизнес</p>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $service->business->name ?? 'Не указан' }}</p>
                                </div>
                            </div>

                            <!-- Записи -->
                            @if($service->appointments_count > 0)
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs sm:text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1.5 sm:mb-2">Записи</p>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $service->appointments_count }} {{ $service->appointments_count === 1 ? 'запись' : ($service->appointments_count < 5 ? 'записи' : 'записей') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if ($services->hasPages())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm px-4 sm:px-6 py-4 sm:py-5">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-4 sm:gap-5">
                        <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 text-center sm:text-left">
                            <span class="font-medium">Показано</span>
                            @if(method_exists($services, 'total'))
                                <span class="font-bold text-slate-900 dark:text-white">{{ $services->firstItem() }}</span>
                                <span class="font-medium">—</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $services->lastItem() }}</span>
                                <span class="font-medium">из</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $services->total() }}</span>
                                <span class="font-medium">услуг</span>
                            @else
                                <span class="font-medium">Страница {{ $services->currentPage() }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 flex-wrap justify-center">
                            <!-- Кнопки навигации -->
                            @if ($services->onFirstPage())
                                <button disabled class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
                                </button>
                            @else
                                <a href="{{ $services->previousPageUrl() }}" class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                    <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
                                </a>
                            @endif

                            <!-- Номера страниц (только для полной пагинации) -->
                            @if(method_exists($services, 'lastPage'))
                                @foreach ($services->getUrlRange(1, min($services->lastPage(), 5)) as $page => $url)
                                    @if ($page == $services->currentPage())
                                        <button disabled class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-bold cursor-default shadow-sm text-xs sm:text-sm">
                                            {{ $page }}
                                        </button>
                                    @else
                                        <a href="{{ $url }}" class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 font-medium shadow-sm hover:shadow-md text-xs sm:text-sm">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif

                            <!-- Кнопка "Вперед" -->
                            @if ($services->hasMorePages())
                                <a href="{{ $services->nextPageUrl() }}" class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                    <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
                                </a>
                            @else
                                <button disabled class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                    <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Пустое состояние -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 sm:p-12 md:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg">
                        <i class="fa-solid fa-briefcase text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3">
                        @if ($search || $businessFilter)
                            Услуги не найдены
                        @else
                            Услуг пока нет
                        @endif
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-6 sm:mb-8 leading-relaxed px-2">
                        @if ($search || $businessFilter)
                            Попробуйте изменить параметры поиска или очистить фильтры для получения других результатов
                        @else
                            Услуги будут отображаться здесь после их создания в системе
                        @endif
                    </p>
                    @if ($search || $businessFilter)
                        <a href="{{ route('panel.services') }}" class="inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                            <i class="fa-solid fa-rotate-left text-xs sm:text-sm"></i>
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
        </div>
    </div>
@endsection
