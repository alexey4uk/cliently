@extends('layouts.panel')

@section('title', 'Свойства тарифов')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash сообщения -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg p-3 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm mb-6">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm sm:text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
                <button @click="show = false"
                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors">
                    <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
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
                class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-3 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm mb-6">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm sm:text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
                </div>
                <button @click="show = false"
                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                    <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
                </button>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Заголовок -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                            <i class="fa-solid fa-chart-line text-white text-base sm:text-lg"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Свойства тарифов</h1>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Управление свойствами для тарифов</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4">
                        <div class="text-left sm:text-right hidden sm:block">
                            <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ $metrics->total() }}</p>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Всего свойств</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('panel.plans.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 shadow-sm hover:shadow-md transition-all duration-200 whitespace-nowrap">
                                <i class="fa-solid fa-arrow-left text-xs sm:text-sm"></i>
                                <span class="hidden sm:inline">К тарифам</span>
                                <span class="sm:hidden">Назад</span>
                            </a>
                            @can('panel.plans.create')
                                <a href="{{ route('panel.plans.properties.create') }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl text-xs sm:text-sm font-medium hover:from-indigo-700 hover:to-indigo-800 shadow-sm hover:shadow-md transition-all duration-200 whitespace-nowrap">
                                    <i class="fa-solid fa-plus text-xs sm:text-sm"></i>
                                    <span class="hidden sm:inline">Создать свойство</span>
                                    <span class="sm:hidden">Создать</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Поиск и фильтры -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <form method="GET" action="{{ route('panel.plans.properties.index') }}" x-data="{ open: false }">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <!-- Поиск -->
                        <div class="flex-1 w-full sm:w-auto">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="search"
                                    name="search" 
                                    value="{{ $search }}" 
                                    placeholder="Поиск по ключу, названию, описанию..."
                                    class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                >
                            </div>
                        </div>
                        
                        <!-- Фильтры -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Дропдаун фильтров -->
                            <div class="relative">
                                <button 
                                    type="button"
                                    @click="open = !open"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-all hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-sm whitespace-nowrap"
                                >
                                    <i class="fa-solid fa-sliders-h text-xs"></i>
                                    <span>Фильтры</span>
                                    @if(request('type') || request('status'))
                                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-full">
                                            {{ (request('type') ? 1 : 0) + (request('status') ? 1 : 0) }}
                                        </span>
                                    @endif
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                </button>
                                
                                <!-- Выпадающее меню -->
                                <div 
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95 translate-y-1"
                                    x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 transform scale-95 translate-y-1"
                                    class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl z-50 overflow-hidden"
                                    style="display: none;"
                                >
                                    <div class="p-4 space-y-4">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Фильтры</h3>
                                            @if(request('type') || request('status'))
                                                <a 
                                                    href="{{ route('panel.plans.properties.index', array_merge(request()->except(['type', 'status']))) }}"
                                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium"
                                                >
                                                    Сбросить
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <!-- Тип -->
                                        <div>
                                            <label for="type_filter" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">
                                                Тип
                                            </label>
                                            <select 
                                                id="type_filter"
                                                name="type" 
                                                class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                            >
                                                <option value="">Все типы</option>
                                                <option value="integer" {{ request('type') === 'integer' ? 'selected' : '' }}>Число</option>
                                                <option value="boolean" {{ request('type') === 'boolean' ? 'selected' : '' }}>Да/Нет</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Статус -->
                                        <div>
                                            <label for="status_filter" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">
                                                Статус
                                            </label>
                                            <select 
                                                id="status_filter"
                                                name="status" 
                                                class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                            >
                                                <option value="">Все</option>
                                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Неактивные</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Кнопка применения -->
                                        <button 
                                            type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all text-sm shadow-sm hover:shadow-md"
                                        >
                                            <i class="fa-solid fa-check text-xs"></i>
                                            <span>Применить фильтры</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Кнопка поиска -->
                            <button 
                                type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all text-sm shadow-sm hover:shadow-md whitespace-nowrap"
                            >
                                <i class="fa-solid fa-search text-xs"></i>
                                <span class="hidden sm:inline">Найти</span>
                            </button>
                            
                            <!-- Сброс всех фильтров -->
                            @if($search || request('type') || request('status'))
                                <a 
                                    href="{{ route('panel.plans.properties.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-all text-sm whitespace-nowrap"
                                >
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                    <span class="hidden sm:inline">Сбросить</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Активные фильтры (теги) -->
                    @if($search || request('type') || request('status'))
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Активные фильтры:</span>
                                @if($search)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 rounded-md text-xs font-medium border border-indigo-200 dark:border-indigo-500/20">
                                        Поиск: "{{ $search }}"
                                        <a href="{{ route('panel.plans.properties.index', array_merge(request()->except(['search']))) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('type'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 rounded-md text-xs font-medium border border-indigo-200 dark:border-indigo-500/20">
                                        Тип: {{ request('type') === 'integer' ? 'Число' : 'Да/Нет' }}
                                        <a href="{{ route('panel.plans.properties.index', array_merge(request()->except(['type']))) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('status'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 rounded-md text-xs font-medium border border-indigo-200 dark:border-indigo-500/20">
                                        Статус: {{ request('status') === 'active' ? 'Активные' : 'Неактивные' }}
                                        <a href="{{ route('panel.plans.properties.index', array_merge(request()->except(['status']))) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <!-- Сохраняем параметры сортировки и пагинации -->
                    @if($sort)
                        <input type="hidden" name="sort" value="{{ $sort }}">
                    @endif
                    @if($direction)
                        <input type="hidden" name="direction" value="{{ $direction }}">
                    @endif
                    @if($perPage)
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                    @endif
                </form>
            </div>

            <!-- Таблица свойств -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <a href="{{ route('panel.plans.properties.index', array_merge(request()->all(), ['sort' => 'key', 'direction' => $sort === 'key' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1">
                                        Ключ
                                        @if($sort === 'key')
                                            <i class="fa-solid fa-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <a href="{{ route('panel.plans.properties.index', array_merge(request()->all(), ['sort' => 'label', 'direction' => $sort === 'label' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1">
                                        Название
                                        @if($sort === 'label')
                                            <i class="fa-solid fa-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тип</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Иконка</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <a href="{{ route('panel.plans.properties.index', array_merge(request()->all(), ['sort' => 'sort_order', 'direction' => $sort === 'sort_order' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1">
                                        Порядок
                                        @if($sort === 'sort_order')
                                            <i class="fa-solid fa-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-tbody" class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($metrics as $metric)
                                <tr data-id="{{ $metric->id }}" data-sort-order="{{ $metric->sort_order }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-700 dark:text-slate-300">{{ $metric->key }}</code>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $metric->label }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $metric->type === 'integer' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-500/20 dark:text-purple-300' }}">
                                            <i class="fa-solid fa-{{ $metric->type === 'integer' ? 'hashtag' : 'toggle-on' }} text-xs"></i>
                                            {{ $metric->type === 'integer' ? 'Число' : 'Да/Нет' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($metric->icon)
                                            <i class="{{ $metric->icon }} text-slate-600 dark:text-slate-400 text-base"></i>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-600 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $metric->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                            <i class="fa-solid fa-{{ $metric->is_active ? 'check-circle' : 'pause-circle' }} text-xs"></i>
                                            {{ $metric->is_active ? 'Активна' : 'Неактивна' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-md text-xs font-semibold bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">
                                                {{ $metric->sort_order }}
                                            </span>
                                            @can('panel.plans.update')
                                                <div class="flex flex-col gap-0.5" draggable="false">
                                                    <form action="{{ route('panel.plans.properties.decrement-sort', $metric) }}" method="POST" class="inline" draggable="false">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center w-6 h-6 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all cursor-pointer" title="Переместить вверх" draggable="false">
                                                            <i class="fa-solid fa-chevron-up text-[10px]"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('panel.plans.properties.increment-sort', $metric) }}" method="POST" class="inline" draggable="false">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center w-6 h-6 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all cursor-pointer" title="Переместить вниз" draggable="false">
                                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('panel.plans.update')
                                                <a href="{{ route('panel.plans.properties.edit', $metric) }}"
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-900 dark:hover:text-indigo-300 transition-all">
                                                    <i class="fa-solid fa-edit text-sm"></i>
                                                </a>
                                            @endcan
                                            @can('panel.plans.delete')
                                                <form action="{{ route('panel.plans.properties.destroy', $metric) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить свойство «{{ addslashes($metric->label) }}»?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 hover:text-rose-900 dark:hover:text-rose-300 transition-all">
                                                        <i class="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('panel.plans.update')
                                                <button type="button" class="sortable-handle inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-all cursor-move touch-none" title="Перетащить для изменения порядка">
                                                    <i class="fa-solid fa-grip-vertical text-sm pointer-events-none"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-inbox text-4xl mb-4"></i>
                                            <p class="text-sm font-medium">Свойства не найдены</p>
                                            <p class="text-xs mt-1">Создайте первое свойство для тарифов</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($metrics->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                        {{ $metrics->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .sortable-chosen {
                background-color: rgb(238 242 255) !important;
            }
            .dark .sortable-chosen {
                background-color: rgba(99, 102, 241, 0.1) !important;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Проверяем, что Sortable загружен
                if (typeof Sortable === 'undefined') {
                    console.error('Sortable.js не загружен');
                    return;
                }

                const tbody = document.getElementById('sortable-tbody');
                if (!tbody) {
                    console.warn('Элемент sortable-tbody не найден');
                    return;
                }

                const sortable = new Sortable(tbody, {
                    handle: '.sortable-handle',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    chosenClass: 'sortable-chosen',
                    forceFallback: true,
                    fallbackOnBody: true,
                    onEnd: function(evt) {
                        const items = Array.from(tbody.querySelectorAll('tr[data-id]'));
                        const newOrder = items.map((item, index) => ({
                            id: item.dataset.id,
                            sort_order: index + 1
                        }));

                        // Обновляем визуально номера позиций
                        items.forEach((item, index) => {
                            const positionSpan = item.querySelector('td span.inline-flex.items-center.justify-center.w-7');
                            if (positionSpan) {
                                positionSpan.textContent = index + 1;
                            }
                            item.dataset.sortOrder = index + 1;
                        });

                        // Отправляем запрос на сервер
                        fetch('{{ route("panel.plans.properties.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order: newOrder })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('HTTP error! status: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                console.log('Порядок обновлен успешно');
                            } else {
                                throw new Error('Сервер вернул ошибку');
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка при обновлении порядка:', error);
                            alert('Ошибка при сохранении порядка. Страница будет перезагружена.');
                            window.location.reload();
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
