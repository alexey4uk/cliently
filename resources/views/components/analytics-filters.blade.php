@props([
    'routeName',
    'filters' => [],
    'filterPresets' => [],
    'business' => null,
])

@php
    $services = $business?->services ?? collect();
    $masters = $business?->masters ?? collect();
    $locations = $business?->locations ?? collect();
    $hasPresets = isset($filterPresets) && count($filterPresets) > 0;
    $inputClass = 'w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors';
@endphp

<div class="rounded-xl border border-gray-200/80 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-800/50 shadow-sm" x-data="{ open: true }">
    {{-- Заголовок (кликабельный на мобильном для сворачивания) --}}
    <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-3 sm:px-5 sm:py-3.5 text-left rounded-t-xl hover:bg-gray-100/80 dark:hover:bg-slate-700/30 transition-colors sm:cursor-default sm:pointer-events-none">
        <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                <i class="fa-solid fa-sliders text-sm"></i>
            </div>
            <span class="text-sm font-semibold text-gray-800 dark:text-white">Параметры отчёта</span>
        </div>
        <span class="sm:hidden flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 dark:text-gray-400" :class="open ? 'rotate-180' : ''">
            <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
        </span>
    </button>

    <div x-show="open" class="px-4 pb-4 sm:px-5 sm:pb-5">
        <form method="GET" action="{{ route($routeName) }}" class="space-y-4">
            {{-- Строка 1: Период + даты --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    @if($hasPresets)
                        @foreach($filterPresets as $preset)
                            <a href="{{ route($routeName, ['preset' => $preset['key'], 'service_id' => $filters['service_id'] ?? '', 'master_id' => $filters['master_id'] ?? '', 'location_id' => $filters['location_id'] ?? '']) }}"
                               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all {{ ($filters['date_from'] ?? '') === $preset['date_from'] && ($filters['date_to'] ?? '') === $preset['date_to'] ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/10' }}">
                                {{ $preset['label'] }}
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="flex flex-wrap items-end gap-2 sm:gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <label for="analytics-date_from" class="hidden sm:inline text-sm text-gray-500 dark:text-gray-400 shrink-0">с</label>
                        <input type="date" id="analytics-date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                               class="{{ $inputClass }} sm:w-[140px]" placeholder="Дата с">
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <label for="analytics-date_to" class="hidden sm:inline text-sm text-gray-500 dark:text-gray-400 shrink-0">по</label>
                        <input type="date" id="analytics-date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                               class="{{ $inputClass }} sm:w-[140px]" placeholder="Дата по">
                    </div>
                </div>
            </div>

            {{-- Строка 2: Услуга, Мастер, Локация + кнопки --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-5 sm:gap-4">
                <div class="sm:col-span-1">
                    <label for="analytics-service_id" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Услуга</label>
                    <select id="analytics-service_id" name="service_id" class="{{ $inputClass }}">
                        <option value="">Все</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ ($filters['service_id'] ?? '') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="analytics-master_id" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Мастер</label>
                    <select id="analytics-master_id" name="master_id" class="{{ $inputClass }}">
                        <option value="">Все</option>
                        @foreach($masters as $master)
                            <option value="{{ $master->id }}" {{ ($filters['master_id'] ?? '') == $master->id ? 'selected' : '' }}>{{ $master->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="analytics-location_id" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Локация</label>
                    <select id="analytics-location_id" name="location_id" class="{{ $inputClass }}">
                        <option value="">Все</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ ($filters['location_id'] ?? '') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap items-end gap-2 sm:col-span-2 lg:col-span-2 sm:justify-end">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 shrink-0">
                        <i class="fa-solid fa-check text-xs"></i>
                        Применить
                    </button>
                    <a href="{{ route($routeName) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shrink-0">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        Сбросить
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
