@extends('layouts.user')

@section('title', 'Запись - Cliently')
@section('page-title', 'Запись')
@section('page-description', 'Информация о записи')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Записи', 'url' => route('appointments.index')],
        ['title' => 'Запись #' . $appointment->id, 'url' => null]
    ]" />
@endpush

@section('content')

<div x-data="{ 
    showPhoneModal: false, 
    phone: '', 
    phoneDisplay: '', 
    client: '',
    actionsMenuOpen: false,
    openPhoneModal(phone, phoneDisplay, client) {
        this.phone = phone;
        this.phoneDisplay = phoneDisplay;
        this.client = client;
        this.showPhoneModal = true;
    },
    closePhoneModal() {
        this.showPhoneModal = false;
    }
}" class="space-y-4 md:space-y-6">
    
    <!-- Заголовок страницы -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="space-y-2">
            <div class="flex items-center gap-2 md:gap-3 flex-wrap">
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white">
                        Запись #{{ $appointment->id }}
                    </h1>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                            'confirmed' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                            'completed' => 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                            'cancelled' => 'bg-rose-100 dark:bg-rose-900/20 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Ожидает',
                            'confirmed' => 'Подтверждена',
                            'completed' => 'Завершена',
                            'cancelled' => 'Отменена',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $statusColors[$appointment->status] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300' }}">
                        {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-calendar text-xs"></i>
                    <span>{{ $appointment->date->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-2">
                <!-- Кнопка редактирования (отдельно) -->
                <a href="{{ route('appointments.edit', $appointment) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-pencil text-xs"></i>
                    <span>Редактировать</span>
                </a>

                <!-- Выпадающее меню для остальных действий -->
                <div class="relative w-full sm:w-auto" @click.away="actionsMenuOpen = false">
                    <!-- Кнопка открытия меню -->
                    <button @click="actionsMenuOpen = !actionsMenuOpen"
                            type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <span>Действия</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': actionsMenuOpen }"></i>
                    </button>

                    <!-- Выпадающее меню -->
                    <div x-show="actionsMenuOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 sm:right-0 mt-2 w-full sm:w-56 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl z-50 overflow-hidden"
                         style="display: none;">
                        <div class="py-1">
                            <!-- Действия по статусам -->
                            @if($appointment->status === 'pending')
                                <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" 
                                      @submit="actionsMenuOpen = false">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                        <i class="fa-solid fa-check-circle text-xs w-5"></i>
                                        <span>Подтвердить</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" 
                                      onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');"
                                      @submit="actionsMenuOpen = false">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                        <i class="fa-solid fa-times-circle text-xs w-5"></i>
                                        <span>Отменить</span>
                                    </button>
                                </form>
                            @elseif($appointment->status === 'confirmed')
                                <form method="POST" action="{{ route('appointments.complete', $appointment) }}" 
                                      @submit="actionsMenuOpen = false">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                        <i class="fa-solid fa-check-double text-xs w-5"></i>
                                        <span>Выполнить</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" 
                                      onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');"
                                      @submit="actionsMenuOpen = false">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                        <i class="fa-solid fa-times-circle text-xs w-5"></i>
                                        <span>Отменить</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Основная информация
            </h2>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Клиент
                    </label>
                    <a href="{{ route('clients.show', $appointment->client) }}" class="block group">
                        <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $appointment->client->full_name }}
                        </p>
                        </a>
                    <button @click="openPhoneModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ addslashes($appointment->client->full_name) }}')"
                            class="text-xs md:text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-1.5 mt-1 transition-colors">
                        <i class="fa-solid fa-phone text-xs"></i>
                        <span>{{ $appointment->client->phone }}</span>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Услуга
                    </label>
                    <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->service->name }}
                    </p>
                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-1">
                        <span class="font-medium">{{ number_format($appointment->final_price, 0, ',', ' ') }} Br</span>
                        <span>•</span>
                        <span>{{ $appointment->final_duration }} мин</span>
                    </p>
                </div>

                @if($appointment->master)
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Мастер
                    </label>
                    <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}
                    </p>
                </div>
                @endif

                @if($appointment->location)
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Локация
                    </label>
                    <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->location->name }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Дата и время -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Дата и время
            </h2>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Дата
                    </label>
                    <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->date->format('d.m.Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Время
                    </label>
                    <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Статус
                    </label>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $statusColors[$appointment->status] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300' }}">
                            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($appointment->notes)
    <!-- Заметки -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-note-sticky text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Заметки
            </h2>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                {{ $appointment->notes }}
            </p>
        </div>
    </div>
    @endif

    <!-- Системная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <i class="fa-solid fa-info-circle text-slate-600 dark:text-slate-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Системная информация
            </h2>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Дата создания
                    </label>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->created_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $appointment->created_at->format('H:i') }}
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Последнее обновление
                    </label>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->updated_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $appointment->updated_at->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для номера телефона -->
    <div x-show="showPhoneModal" 
         @click.away="closePhoneModal()"
         @keydown.escape.window="closePhoneModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display: none;">
        <div @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                <button @click="closePhoneModal()" 
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <div class="mb-4">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Клиент</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Телефон</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
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
</div>

@endsection
