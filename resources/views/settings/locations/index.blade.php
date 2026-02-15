@extends('layouts.user')

@section('title', 'Локации - Cliently')
@section('page-title', 'Локации')
@section('page-description', 'Управление локациями вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Локации', 'url' => null]]" />
@endpush

@section('content')

@php
    $search = request('search', '');
    $hasActiveFilters = (bool) $search;
@endphp

<div x-data="{
    showDeleteModal: false,
    locationToDelete: null,
    locationName: '',
    openDeleteModal(locationId, locationName) {
        this.locationToDelete = locationId;
        this.locationName = locationName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.locationToDelete = null;
        this.locationName = '';
    },
    confirmDelete() {
        if (this.locationToDelete) {
            const form = document.getElementById('delete-form-' + this.locationToDelete);
            if (form) {
                form.submit();
            }
        }
    }
}" class="max-w-[1400px] mx-auto">
    <div class="space-y-4 md:space-y-6">

        @if(!$business)
        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 p-4">
            <p class="text-amber-800 dark:text-amber-200 font-medium">Создайте бизнес или примите приглашение, чтобы добавлять локации.</p>
        <div class="mt-2 flex flex-wrap gap-2">
            <a href="{{ route('settings.businesses.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg">Управление бизнесами</a>
        </div>
        </div>
        @else
        <!-- Поиск и фильтры -->
        <div class="space-y-4">
            <!-- Активные фильтры -->
            @if ($hasActiveFilters)
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('settings.locations') }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Поиск: "{{ $search }}"</span>
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                        <a href="{{ route('settings.locations') }}"
                            class="ml-auto text-xs text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                            Сбросить
                        </a>
                    </div>
                </div>
            @endif

            <!-- Мобильная версия: заголовок + поиск + действия -->
            <div class="md:hidden space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <h1 class="text-lg font-semibold text-slate-900 dark:text-white truncate">Локации</h1>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($canCreateLocations && $canCreateLocation)
                        <a href="{{ route('settings.locations.create') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-plus text-sm"></i>
                            <span>Добавить</span>
                        </a>
                        @elseif($canCreateLocations && !$canCreateLocation)
                        <button type="button" disabled
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed"
                            title="Достигнут лимит локаций для вашего тарифа.">
                            <i class="fa-solid fa-plus text-sm"></i>
                            <span>Добавить</span>
                        </button>
                        @endif
                    </div>
                </div>
                <form method="GET" action="{{ route('settings.locations') }}" class="flex gap-2">
                    <label class="sr-only" for="locations-search-mobile">Поиск локаций</label>
                    <div class="flex-1 min-w-0 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none" aria-hidden="true"></i>
                        <input id="locations-search-mobile" type="text" name="search" value="{{ $search }}"
                            placeholder="Название, адрес, описание..."
                            class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <button type="submit" class="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 active:scale-[0.98] transition-all" aria-label="Искать">
                        <i class="fa-solid fa-magnifying-glass text-base"></i>
                    </button>
                </form>
            </div>

            <!-- Десктопная версия: поиск + действия в одной строке -->
            <div class="hidden md:flex flex-col gap-4">
                <div class="flex flex-wrap items-end gap-4">
                    <form method="GET" action="{{ route('settings.locations') }}" class="flex items-end gap-4 flex-1 min-w-0">
                        <div class="flex-1 max-w-md min-w-0">
                            <label for="locations-search" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Поиск локаций</label>
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                                <input id="locations-search" type="text" name="search" value="{{ $search }}"
                                    placeholder="Название, адрес, описание..."
                                    class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-slate-900 dark:text-white placeholder-slate-400">
                            </div>
                        </div>
                        <button type="submit" class="px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shrink-0">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </button>
                    </form>
                    <div class="flex items-center gap-2 shrink-0 pb-0.5">
                        @if($canCreateLocations && $canCreateLocation)
                        <a href="{{ route('settings.locations.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-plus text-sm"></i>
                            <span>Добавить локацию</span>
                        </a>
                        @elseif($canCreateLocations && !$canCreateLocation)
                        <button type="button" disabled
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed"
                            title="Достигнут лимит локаций для вашего тарифа.">
                            <i class="fa-solid fa-plus text-sm"></i>
                            <span>Добавить локацию</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            @if($canCreateLocations && !$canCreateLocation)
            <div x-data="{ showLimitNotice: true }" x-show="showLimitNotice"
                 class="flex items-center gap-3 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 px-4 py-3 text-sm text-amber-800 dark:text-amber-200">
                <i class="fa-solid fa-info-circle shrink-0 text-amber-600 dark:text-amber-400"></i>
                <span>Достигнут лимит локаций для вашего тарифа. Добавление новых локаций недоступно.</span>
                <a href="{{ route('subscription.index') }}" class="shrink-0 font-medium underline hover:no-underline">Обновить тариф</a>
                <button type="button" @click="showLimitNotice = false" class="ml-auto p-1 text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200" aria-label="Закрыть">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            @endif
        </div>

    <!-- Список локаций -->
    @if ($locations->count() > 0)
        <!-- Таблица для больших экранов -->
        <div class="hidden md:block">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Адрес</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Рабочие часы</th>
                            @if($hasAnyLocationAction)
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach ($locations as $location)
                            @php
                                $workingHours = json_decode($location->working_hours, true);
                                $is24Hours = $workingHours['24_hours'] ?? false;
                                $timeFrom = $workingHours['from'] ?? '—';
                                $timeTo = $workingHours['to'] ?? '—';
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                            <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $location->name }}
                                            </div>
                                            @if ($location->description)
                                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                                    {{ $location->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $location->full_address }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $location->phone ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($workingHours)
                                        @if ($is24Hours)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-600">
                                                <i class="fa-solid fa-clock text-xs"></i>
                                                Круглосуточно
                                            </span>
                                        @else
                                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                                {{ $timeFrom }} – {{ $timeTo }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-sm text-slate-400 dark:text-slate-500">—</span>
                                    @endif
                                </td>
                                @if($hasAnyLocationAction)
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($canUpdateLocations)
                                                <a href="{{ route('settings.locations.edit', $location) }}" 
                                                    class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                    title="Редактировать">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($canDeleteLocations)
                                                <form method="POST" action="{{ route('settings.locations.destroy', $location) }}"
                                                    id="delete-form-{{ $location->id }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type="button"
                                                    @click="openDeleteModal({{ $location->id }}, '{{ addslashes($location->name) }}')"
                                                    class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                    title="Удалить">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Карточки для мобильных -->
        <div class="md:hidden grid grid-cols-1 gap-4">
            @foreach ($locations as $location)
                @php
                    $workingHours = json_decode($location->working_hours, true);
                    $is24Hours = $workingHours['24_hours'] ?? false;
                    $timeFrom = $workingHours['from'] ?? '—';
                    $timeTo = $workingHours['to'] ?? '—';
                @endphp

                <x-mobile-card>
                    <!-- Заголовок карточки -->
                    <x-mobile-card-header class="p-6 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex-1">
                                {{ $location->name }}
                            </h3>
                        </div>
                        @if ($workingHours)
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                <i class="fa-solid fa-clock text-indigo-500"></i>
                                @if ($is24Hours)
                                    <span class="font-medium text-indigo-600 dark:text-indigo-400">Круглосуточно</span>
                                @else
                                    <span>{{ $timeFrom }} – {{ $timeTo }}</span>
                                @endif
                            </div>
                        @endif
                    </x-mobile-card-header>

                    <x-mobile-card-body class="space-y-4">
                        <!-- Адрес -->
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-map-marker-alt text-slate-600 dark:text-slate-400 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Адрес</p>
                                <p class="text-sm text-slate-900 dark:text-white">
                                    {{ $location->full_address }}
                                </p>
                            </div>
                        </div>

                        <!-- Телефон -->
                        @if ($location->phone)
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-phone text-slate-600 dark:text-slate-400 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Телефон</p>
                                    <p class="text-sm text-slate-900 dark:text-white">
                                        {{ $location->phone }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Описание -->
                        @if ($location->description)
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-info-circle text-slate-600 dark:text-slate-400 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Описание</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                                        {{ $location->description }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </x-mobile-card-body>

                    <x-mobile-card-footer class="bg-slate-50 dark:bg-slate-800/30">
                        <div class="flex items-center justify-end gap-3">
                            @if($canUpdateLocations)
                                <a href="{{ route('settings.locations.edit', $location) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <i class="fa-solid fa-pencil text-xs"></i>
                                    <span>Редактировать</span>
                                </a>
                            @endif

                            @if($canDeleteLocations)
                                <form method="POST" action="{{ route('settings.locations.destroy', $location) }}"
                                    id="delete-form-{{ $location->id }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                    @click="openDeleteModal({{ $location->id }}, '{{ addslashes($location->name) }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-white dark:bg-slate-800 border border-rose-300 dark:border-rose-700/50 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Удалить</span>
                                </button>
                            @endif
                        </div>
                    </x-mobile-card-footer>
                </x-mobile-card>
            @endforeach
        </div>
    @else
        @if(request('search'))
        <!-- Ничего не найдено по поиску -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-8 text-center">
            <p class="text-slate-600 dark:text-slate-400 mb-4">По вашему запросу ничего не найдено.</p>
            <a href="{{ route('settings.locations') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                Сбросить фильтры
            </a>
        </div>
        @else
        <!-- Пустое состояние -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-16 w-16 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    Локации не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Начните работу с системой, добавив первую локацию с адресом и рабочими часами.
                </p>
                @if($canCreateLocations && $canCreateLocation)
                <a href="{{ route('settings.locations.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить локацию</span>
                </a>
                @elseif($canCreateLocations && !$canCreateLocation)
                <button type="button" disabled
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed"
                    title="Достигнут лимит локаций для вашего тарифа.">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить локацию</span>
                </button>
                @endif
            </div>
        </div>
        @endif
    @endif
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" 
         @click.away="closeDeleteModal()" 
         @keydown.escape.window="closeDeleteModal()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         style="display: none;">
        <div @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                <button @click="closeDeleteModal()"
                    class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-6">
                    Вы уверены, что хотите удалить локацию <span class="font-semibold" x-text="locationName"></span>?
                    Это действие нельзя отменить.
                </p>
                <div class="flex gap-3">
                    <button @click="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
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
    @endif
</div>

@endsection
