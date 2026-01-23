@extends('layouts.user')

@section('title', 'Записи - Cliently')
@section('page-title', 'Записи')
@section('page-description', 'Управление записями клиентов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Записи', 'url' => null]]" />
@endpush

@section('content')

@php
    // Получаем бизнес и роль для проверки прав доступа
    $user = Auth::user();
    $currentBusiness = null;
    $currentBusinessRole = null;
    $permissionService = null;
    if ($user) {
        $user->load('businesses');
        $currentBusiness = $user->businesses->first();
        if ($currentBusiness) {
            $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
            $currentBusinessRole = $pivot?->pivot->role ?? null;
            if ($currentBusinessRole) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
            }
        }
    }

    // Функция для проверки бизнес-прав
    $hasBusinessPermission = function($permission) use ($currentBusiness, $currentBusinessRole, $permissionService) {
        if (!$currentBusiness || !$currentBusinessRole || !$permissionService) {
            return false;
        }
        return $permissionService->hasPermission($currentBusiness->id, $currentBusinessRole, $permission);
    };
@endphp

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
    $hasActiveFilters = $date || $status || request('service_id') || request('master_id') || $search;
@endphp

<div x-data="{
    showPhoneModal: false,
    phone: '',
    phoneDisplay: '',
    client: '',
    showFilters: {{ $hasActiveFilters ? 'true' : 'false' }},
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
}" class="space-y-4 md:space-y-6">

    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">
                    Записи
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Управление записями клиентов
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($hasBusinessPermission('appointments.export'))
                    <a href="{{ route('appointments.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-file-csv text-sm"></i>
                        <span>Экспорт</span>
                    </a>
                @endif
                @if($hasBusinessPermission('appointments.create'))
                    <a href="{{ route('appointments.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-plus text-sm"></i>
                        <span>Создать запись</span>
                    </a>
                @endif
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
                        <a href="{{ route('appointments.index', array_merge(request()->except('search'))) }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Поиск: "{{ $search }}"</span>
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                    @if ($date)
                        <a href="{{ route('appointments.index', array_merge(request()->except('date'))) }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Дата: {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</span>
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                    @if ($status)
                        <a href="{{ route('appointments.index', array_merge(request()->except('status'))) }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Статус: {{ $status === 'confirmed' ? 'Подтвержденные' : ($status === 'pending' ? 'Ожидающие' : ($status === 'completed' ? 'Завершенные' : 'Отмененные')) }}</span>
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                    @if (request('service_id'))
                        <a href="{{ route('appointments.index', array_merge(request()->except('service_id'))) }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Услуга</span>
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                    @if (request('master_id'))
                        <a href="{{ route('appointments.index', array_merge(request()->except('master_id'))) }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <span>Мастер</span>
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                    <a href="{{ route('appointments.index') }}"
                        class="ml-auto text-xs text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                        Сбросить
                    </a>
                </div>
            </div>
        @endif

        <!-- Мобильная версия: поиск и кнопка фильтров -->
        <div class="md:hidden space-y-4">
            <!-- Всегда видимый поиск -->
            <form method="GET" action="{{ route('appointments.index') }}" class="flex gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Поиск записей..."
                        class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                </div>
                <button type="submit"
                    class="h-12 px-4 rounded-lg bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>
                <button type="button" @click="toggleFilters()"
                    class="h-12 w-12 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                    :class="showFilters ? 'border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400' : ''">
                    <i class="fa-solid fa-sliders text-sm"></i>
                </button>
            </form>

            <!-- Выпадающая панель дополнительных фильтров -->
            <div x-show="showFilters" @click.away="showFilters = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 transform -translate-y-4 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4 space-y-4"
                style="display: none;">
                <form method="GET" action="{{ route('appointments.index') }}" class="space-y-4">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Дата</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                            </div>
                            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                                class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Статус</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-circle-check text-slate-400 text-sm"></i>
                            </div>
                            <select name="status" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="">Все статусы</option>
                                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Подтвержденные</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидающие</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Завершенные</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Услуга</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-scissors text-slate-400 text-sm"></i>
                            </div>
                            <select name="service_id" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="">Все услуги</option>
                                @foreach(\App\Models\Service::where('business_id', $business->id)->orderBy('name')->get() as $service)
                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Мастер</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user-tie text-slate-400 text-sm"></i>
                            </div>
                            <select name="master_id" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="">Все мастера</option>
                                @foreach(\App\Models\Master::where('business_id', $business->id)->orderBy('first_name')->get() as $master)
                                    <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                        {{ $master->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Десктопная версия фильтров -->
        <div class="hidden md:flex flex-col gap-4">
            <!-- Всегда видимый поиск -->
            <form method="GET" action="{{ route('appointments.index') }}" class="flex items-end gap-4">
                <!-- Поиск -->
                <div class="flex-1 max-w-lg">
                    <label for="search-input"
                        class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                        Поиск записей
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input id="search-input" type="text" name="search" value="{{ $search }}"
                            placeholder="Поиск по клиенту, услуге или мастеру..."
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
                <form method="GET" action="{{ route('appointments.index') }}" class="flex flex-wrap items-end gap-4">
                    <input type="hidden" name="search" value="{{ $search }}">

                    <!-- Фильтр по дате -->
                    <div class="min-w-[180px]">
                        <label for="date-filter"
                            class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                            Дата
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-calendar-days text-slate-400 text-sm"></i>
                            </div>
                            <input id="date-filter" type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                                class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Фильтр по статусу -->
                    <div class="min-w-[180px]">
                        <label for="status-filter"
                            class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Статус</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-circle-check text-slate-400 text-sm"></i>
                            </div>
                            <select id="status-filter" name="status" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="">Все статусы</option>
                                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Подтвержденные</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидающие</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Завершенные</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Фильтр по услуге -->
                    <div class="min-w-[180px]">
                        <label for="service-filter"
                            class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Услуга</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-scissors text-slate-400 text-sm"></i>
                            </div>
                            <select id="service-filter" name="service_id" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="">Все услуги</option>
                                @foreach(\App\Models\Service::where('business_id', $business->id)->orderBy('name')->get() as $service)
                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Фильтр по мастеру -->
                    <div class="min-w-[180px]">
                        <label for="master-filter"
                            class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Мастер</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user-tie text-slate-400 text-sm"></i>
                            </div>
                            <select id="master-filter" name="master_id" onchange="this.form.submit()"
                                class="w-full pl-11 pr-10 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="">Все мастера</option>
                                @foreach(\App\Models\Master::where('business_id', $business->id)->orderBy('first_name')->get() as $master)
                                    <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                        {{ $master->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка сброса фильтров -->
                    @if ($date || $status || request('service_id') || request('master_id'))
                        <div class="ml-auto">
                            <a href="{{ route('appointments.index', ['search' => $search]) }}"
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

    <!-- Список записей -->
    @if ($appointments->count() > 0)
        <!-- Таблица для больших экранов -->
        <div class="hidden md:block">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <a href="{{ route('appointments.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => ($sort === 'date' && $direction === 'asc') ? 'desc' : 'asc'])) }}"
                                    class="flex items-center gap-1 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                                    Дата и время
                                    @if($sort === 'date')
                                        <i class="fa-solid fa-chevron-{{ $direction === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Клиент</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Услуга</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Мастер</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Цена</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach ($appointments as $appointment)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-calendar text-slate-600 dark:text-slate-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $appointment->date->format('d.m.Y') }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $appointment->client->full_name }}
                                            </div>
                                            @if($appointment->client->phone)
                                                <button
                                                    @click="openPhoneModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ addslashes($appointment->client->full_name) }}')"
                                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                                    {{ $appointment->client->phone }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-900 dark:text-white font-medium">
                                        {{ $appointment->service->name }}
                                    </div>
                                    @if ($appointment->final_duration)
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $appointment->final_duration }} мин
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                    {{ $appointment->master->name ?? 'Не назначен' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $appointment->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-600' : '' }}
                                        {{ $appointment->status === 'cancelled' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-600' : '' }}
                                        {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200 dark:border-blue-600' : '' }}
                                        {{ $appointment->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-600' : '' }}">
                                        @if($appointment->status === 'completed')
                                            <i class="fa-solid fa-check-circle text-xs"></i>
                                            Завершена
                                        @elseif($appointment->status === 'cancelled')
                                            <i class="fa-solid fa-xmark-circle text-xs"></i>
                                            Отменена
                                        @elseif($appointment->status === 'confirmed')
                                            <i class="fa-solid fa-circle-check text-xs"></i>
                                            Подтверждена
                                        @else
                                            <i class="fa-solid fa-clock text-xs"></i>
                                            Ожидает
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($appointment->final_price)
                                        <span class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ number_format($appointment->final_price, 0, ',', ' ') }} ₽
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic">Не указана</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('appointments.show', $appointment) }}"
                                            class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                            title="Просмотр">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        @if($hasBusinessPermission('appointments.update'))
                                            <a href="{{ route('appointments.edit', $appointment) }}"
                                                class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                title="Редактировать">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            @if($appointment->status === 'confirmed')
                                                <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-1.5 text-emerald-400 dark:text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors"
                                                        title="Завершить"
                                                        onclick="return confirm('Вы уверены, что хотите завершить эту запись?')">
                                                        <i class="fa-solid fa-check text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-1.5 text-rose-400 dark:text-rose-500 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                        title="Отменить"
                                                        onclick="return confirm('Вы уверены, что хотите отменить эту запись?')">
                                                        <i class="fa-solid fa-xmark text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
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
            @foreach ($appointments as $appointment)
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                    <!-- Заголовок карточки -->
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-calendar text-slate-600 dark:text-slate-400"></i>
                                </div>
                                <div>
                                    <div class="text-base font-medium text-slate-900 dark:text-white">
                                        {{ $appointment->date->format('d.m.Y') }}
                                    </div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400">
                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $appointment->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-600' : '' }}
                                {{ $appointment->status === 'cancelled' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-600' : '' }}
                                {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200 dark:border-blue-600' : '' }}
                                {{ $appointment->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-600' : '' }}">
                                @if($appointment->status === 'completed')
                                    <i class="fa-solid fa-check-circle text-xs"></i>
                                    Завершена
                                @elseif($appointment->status === 'cancelled')
                                    <i class="fa-solid fa-xmark-circle text-xs"></i>
                                    Отменена
                                @elseif($appointment->status === 'confirmed')
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                    Подтверждена
                                @else
                                    <i class="fa-solid fa-clock text-xs"></i>
                                    Ожидает
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Содержимое карточки -->
                    <div class="px-4 py-3 space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Клиент</p>
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $appointment->client->full_name }}
                                    </p>
                                    @if($appointment->client->phone)
                                        <button
                                            @click="openPhoneModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ addslashes($appointment->client->full_name) }}')"
                                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                            {{ $appointment->client->phone }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Услуга</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $appointment->service->name }}
                                @if ($appointment->final_duration)
                                    <span class="text-slate-500 dark:text-slate-400">• {{ $appointment->final_duration }} мин</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Мастер</p>
                            <p class="text-sm text-slate-900 dark:text-white">
                                {{ $appointment->master->name ?? 'Не назначен' }}
                            </p>
                        </div>
                        @if ($appointment->final_price)
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Цена</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ number_format($appointment->final_price, 0, ',', ' ') }} ₽
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Действия -->
                    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('appointments.show', $appointment) }}"
                                class="flex-1 px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors text-center">
                                Просмотр
                            </a>

                            @if($hasBusinessPermission('appointments.update'))
                                <a href="{{ route('appointments.edit', $appointment) }}"
                                    class="flex-1 px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors text-center">
                                    Изменить
                                </a>
                                @if($appointment->status === 'confirmed')
                                    <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="flex-shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-2 text-sm font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/20 hover:bg-emerald-200 dark:hover:bg-emerald-500/30 rounded-lg transition-colors"
                                            onclick="return confirm('Вы уверены, что хотите завершить эту запись?')">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                    <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="flex-shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-2 text-sm font-medium text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-500/20 hover:bg-rose-200 dark:hover:bg-rose-500/30 rounded-lg transition-colors"
                                            onclick="return confirm('Вы уверены, что хотите отменить эту запись?')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        @if ($appointments->hasPages())
            @php
                $currentPage = $appointments->currentPage();
                $lastPage = $appointments->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
                if ($endPage - $startPage < 4) {
                    if ($startPage == 1) {
                        $endPage = min($lastPage, $startPage + 4);
                    } else {
                        $startPage = max(1, $endPage - 4);
                    }
                }
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 px-6 py-4">
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
                        <!-- Кнопка "В начало" -->
                        @if ($currentPage > 1)
                            <a href="{{ $appointments->url(1) }}"
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
                        @if ($appointments->onFirstPage())
                            <button disabled
                                class="w-11 h-11 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl opacity-50 cursor-not-allowed text-slate-400 shadow-sm">
                                <i class="fa-solid fa-chevron-left text-sm"></i>
                            </button>
                        @else
                            <a href="{{ $appointments->previousPageUrl() }}"
                                class="w-11 h-11 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-200 text-slate-700 dark:text-slate-300 shadow-sm hover:shadow-md">
                                <i class="fa-solid fa-chevron-left text-sm"></i>
                            </a>
                        @endif

                        <!-- Номера страниц -->
                        @foreach ($appointments->getUrlRange($startPage, $endPage) as $page => $url)
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
                        @if ($appointments->hasMorePages())
                            <a href="{{ $appointments->nextPageUrl() }}"
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
                            <a href="{{ $appointments->url($lastPage) }}"
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
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-16 w-16 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    @if ($hasActiveFilters)
                        Записи не найдены
                    @else
                        Записей пока нет
                    @endif
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    @if ($hasActiveFilters)
                        Попробуйте изменить параметры поиска или очистить фильтры
                    @else
                        Начните работу, создав первую запись для вашего клиента
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if ($hasActiveFilters)
                        <a href="{{ route('appointments.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-rotate-left text-sm"></i>
                            <span>Очистить фильтры</span>
                        </a>
                    @endif
                    <a href="{{ route('appointments.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-plus text-sm"></i>
                        <span>Создать запись</span>
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

</div>

@endsection
