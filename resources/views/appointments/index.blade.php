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

    <!-- Заголовок страницы -->
    <div class="flex flex-col gap-6">
        <!-- Заголовок -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white">
                    Записи
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Управление записями клиентов
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Фильтры -->
                <button @click="toggleFilters()"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Фильтры</span>
                </button>
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

        <!-- Фильтры -->
        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-96"
             x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <form method="GET" action="{{ route('appointments.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Фильтр по дате -->
                        <div class="space-y-2">
                            <label for="date-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Дата
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-calendar text-slate-400 text-xs"></i>
                                </div>
                                <input id="date-filter" type="date" name="date" value="{{ $date }}"
                                    class="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                    onchange="this.form.submit()">
                            </div>
                        </div>

                        <!-- Фильтр по статусу -->
                        <div class="space-y-2">
                            <label for="status-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Статус
                            </label>
                            <select name="status" id="status-filter"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="">Все статусы</option>
                                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Подтвержденные</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидающие</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Завершенные</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                            </select>
                        </div>

                        <!-- Фильтр по услуге -->
                        <div class="space-y-2">
                            <label for="service-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Услуга
                            </label>
                            <select name="service_id" id="service-filter"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="">Все услуги</option>
                                @foreach(\App\Models\Service::where('business_id', $business->id)->orderBy('name')->get() as $service)
                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по мастеру -->
                        <div class="space-y-2">
                            <label for="master-filter" class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                                Мастер
                            </label>
                            <select name="master_id" id="master-filter"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-xs text-slate-900 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="">Все мастера</option>
                                @foreach(\App\Models\Master::where('business_id', $business->id)->orderBy('first_name')->get() as $master)
                                    <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                        {{ $master->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Кнопки управления фильтрами -->
                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center gap-2">
                            @if ($date || $status || request('service_id') || request('master_id'))
                                <a href="{{ route('appointments.index') }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                    <span>Сбросить фильтры</span>
                                </a>
                            @endif
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            <span>Применить</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Табличное представление -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
        <!-- Мобильная версия: поиск и кнопка фильтров -->
        <div class="md:hidden p-4 space-y-3 border-b border-slate-200 dark:border-slate-800">
            <!-- Всегда видимый поиск -->
            <form method="GET" action="{{ route('appointments.index') }}" class="flex gap-2">
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
            </form>
        </div>

        <!-- Десктопная версия: заголовки таблицы -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            <a href="{{ route('appointments.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => ($sort === 'date' && $direction === 'asc') ? 'desc' : 'asc'])) }}"
                                class="flex items-center gap-1 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                                Дата и время
                                @if($sort === 'date')
                                    <i class="fa-solid fa-chevron-{{ $direction === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Клиент
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Услуга
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Мастер
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Цена
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($appointments as $appointment)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                            <i class="fa-solid fa-calendar text-slate-600 dark:text-slate-400 text-xs"></i>
                                        </div>
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
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ $appointment->client->full_name }}
                                        </div>
                                        @if($appointment->client->phone)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $appointment->client->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-sm text-slate-900 dark:text-white font-medium">
                                    {{ $appointment->service->name }}
                                </div>
                                @if ($appointment->final_duration)
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $appointment->final_duration }} мин
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-sm text-slate-900 dark:text-white">
                                    {{ $appointment->master->name ?? 'Не назначен' }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $appointment->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300' : '' }}
                                    {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $appointment->status === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}">
                                    {{ $appointment->status === 'completed' ? 'Завершена' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'Отменена' : '' }}
                                    {{ $appointment->status === 'confirmed' ? 'Подтверждена' : '' }}
                                    {{ $appointment->status === 'pending' ? 'Ожидает' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($appointment->final_price)
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ number_format($appointment->final_price, 0, ',', ' ') }} ₽
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">Не указана</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('appointments.show', $appointment) }}"
                                        class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                        title="Просмотр">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('appointments.edit', $appointment) }}"
                                        class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                        title="Редактировать">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>

                                    @if($appointment->status === 'confirmed')
                                        <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition-colors"
                                                title="Завершить"
                                                onclick="return confirm('Вы уверены, что хотите завершить эту запись?')">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="h-8 w-8 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800/50 transition-colors"
                                                title="Отменить"
                                                onclick="return confirm('Вы уверены, что хотите отменить эту запись?')">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="max-w-md mx-auto">
                                    <div class="h-16 w-16 md:h-20 md:w-20 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/30 flex items-center justify-center mx-auto mb-4 md:mb-6">
                                        <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-2xl md:text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-semibold text-slate-900 dark:text-white mb-2">
                                        Записи не найдены
                                    </h3>
                                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mb-6 md:mb-8 leading-relaxed">
                                        @if($date || $status)
                                            Попробуйте изменить параметры поиска или фильтры для получения других результатов
                                        @else
                                            Начните работу с системой, создав первую запись для вашего клиента
                                        @endif
                                    </p>
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2 md:gap-3">
                                        @if ($date || $status)
                                            <a href="{{ route('appointments.index') }}"
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
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
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Мобильная версия: карточки -->
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800">
            @forelse ($appointments as $appointment)
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-calendar text-slate-600 dark:text-slate-400"></i>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-slate-900 dark:text-white">
                                    {{ $appointment->date->format('d.m.Y') }}
                                </div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $appointment->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                            {{ $appointment->status === 'cancelled' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300' : '' }}
                            {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                            {{ $appointment->status === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}">
                            {{ $appointment->status === 'completed' ? 'Завершена' : '' }}
                            {{ $appointment->status === 'cancelled' ? 'Отменена' : '' }}
                            {{ $appointment->status === 'confirmed' ? 'Подтверждена' : '' }}
                            {{ $appointment->status === 'pending' ? 'Ожидает' : '' }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $appointment->client->full_name }}
                                </div>
                                @if($appointment->client->phone)
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $appointment->client->phone }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-sm text-slate-900 dark:text-white font-medium">
                            {{ $appointment->service->name }}
                            @if ($appointment->final_duration)
                                <span class="text-slate-500 dark:text-slate-400">• {{ $appointment->final_duration }} мин</span>
                            @endif
                        </div>

                        <div class="text-sm text-slate-900 dark:text-white">
                            {{ $appointment->master->name ?? 'Мастер не назначен' }}
                        </div>

                        @if ($appointment->final_price)
                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ number_format($appointment->final_price, 0, ',', ' ') }} ₽
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('appointments.show', $appointment) }}"
                            class="flex-1 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors text-sm">
                            <i class="fa-solid fa-eye text-xs mr-2"></i>
                            Просмотр
                        </a>
                        <a href="{{ route('appointments.edit', $appointment) }}"
                            class="flex-1 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors text-sm">
                            <i class="fa-solid fa-pen text-xs mr-2"></i>
                            Изменить
                        </a>

                        @if($appointment->status === 'confirmed')
                            <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition-colors text-sm"
                                    onclick="return confirm('Вы уверены, что хотите завершить эту запись?')">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </button>
                            </form>
                        @endif

                        @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full h-9 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-700 dark:text-rose-300 hover:bg-rose-200 dark:hover:bg-rose-800/50 transition-colors text-sm"
                                    onclick="return confirm('Вы уверены, что хотите отменить эту запись?')">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="h-16 w-16 md:h-20 md:w-20 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/30 flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-2xl md:text-3xl"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-slate-900 dark:text-white mb-2">
                            Записи не найдены
                        </h3>
                        <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mb-6 md:mb-8 leading-relaxed">
                            @if($date || $status)
                                Попробуйте изменить параметры поиска или фильтры для получения других результатов
                            @else
                                Начните работу с системой, создав первую запись для вашего клиента
                            @endif
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-2 md:gap-3">
                            @if ($date || $status)
                                <a href="{{ route('appointments.index') }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
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
            @endforelse
        </div>

        <!-- Пагинация -->
        @if($appointments->hasPages())
            <div class="px-4 md:px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                <div class="flex items-center justify-between">
                    <!-- Мобильная пагинация -->
                    <div class="md:hidden flex items-center justify-center w-full gap-2">
                        @if ($appointments->onFirstPage())
                            <button disabled
                                class="h-9 px-3 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </button>
                        @else
                            <a href="{{ $appointments->previousPageUrl() }}"
                                class="h-9 px-3 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        <div class="text-xs text-slate-600 dark:text-slate-400 px-3">
                            {{ $appointments->currentPage() }} из {{ $appointments->lastPage() }}
                        </div>

                        @if ($appointments->hasMorePages())
                            <a href="{{ $appointments->nextPageUrl() }}"
                                class="h-9 px-3 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <button disabled
                                class="h-9 px-3 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Десктопная пагинация -->
                    <div class="hidden md:flex items-center justify-between w-full">
                        <div class="text-sm text-slate-700 dark:text-slate-300">
                            Показано {{ $appointments->firstItem() }} - {{ $appointments->lastItem() }} из {{ $appointments->total() }} записей
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($appointments->onFirstPage())
                                <button disabled
                                    class="h-8 w-8 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-angles-left text-xs"></i>
                                </button>
                            @else
                                <a href="{{ $appointments->url(1) }}"
                                    class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300"
                                    title="В начало">
                                    <i class="fa-solid fa-angles-left text-xs"></i>
                                </a>
                            @endif

                            @if ($appointments->onFirstPage())
                                <button disabled
                                    class="h-8 w-8 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                            @else
                                <a href="{{ $appointments->previousPageUrl() }}"
                                    class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            @php
                                $startPage = max(1, $appointments->currentPage() - 2);
                                $endPage = min($appointments->lastPage(), $appointments->currentPage() + 2);
                            @endphp

                            @foreach(range($startPage, $endPage) as $page)
                                @if($page === $appointments->currentPage())
                                    <button disabled
                                        class="h-8 w-8 flex items-center justify-center bg-indigo-600 border border-indigo-600 rounded-lg text-white font-medium text-xs">
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $appointments->url($page) }}"
                                        class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 text-xs">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if ($appointments->hasMorePages())
                                <a href="{{ $appointments->nextPageUrl() }}"
                                    class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="h-8 w-8 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            @endif

                            @if ($appointments->hasMorePages())
                                <a href="{{ $appointments->url($appointments->lastPage()) }}"
                                    class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300"
                                    title="В конец">
                                    <i class="fa-solid fa-angles-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="h-8 w-8 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-angles-right text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

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
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-100 dark:border-slate-800">
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
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Клиент</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                    </div>
                </div>

                <!-- Телефон -->
                <div class="mb-6">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Телефон</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
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

@endsection