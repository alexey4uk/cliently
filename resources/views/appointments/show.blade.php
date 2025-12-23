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
<div class="space-y-6">
    <!-- Заголовок страницы -->
    <div class="flex flex-col gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
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
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Быстрые действия по статусам -->
                @if($appointment->status === 'pending')
                    <!-- Ожидает подтверждения: можно подтвердить или отменить -->
                    <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center gap-1.5">
                            <i class="fa-solid fa-check-circle text-xs"></i>
                            <span>Подтвердить</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" 
                          onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');"
                          class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center gap-1.5">
                            <i class="fa-solid fa-times-circle text-xs"></i>
                            <span>Отменить</span>
                        </button>
                    </form>
                @elseif($appointment->status === 'confirmed')
                    <!-- Подтверждена: можно выполнить или отменить -->
                    <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center gap-1.5">
                            <i class="fa-solid fa-check-double text-xs"></i>
                            <span>Выполнить</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" 
                          onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');"
                          class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center gap-1.5">
                            <i class="fa-solid fa-times-circle text-xs"></i>
                            <span>Отменить</span>
                        </button>
                    </form>
                @elseif($appointment->status === 'completed')
                    <!-- Выполнена: ничего нельзя делать со статусом -->
                @elseif($appointment->status === 'cancelled')
                    <!-- Отменена: ничего нельзя делать со статусом -->
                @endif

                <!-- Редактирование доступно всегда -->
                <a href="{{ route('appointments.edit', $appointment) }}"
                   class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-150 flex items-center gap-1.5">
                    <i class="fa-solid fa-edit text-xs"></i>
                    <span>Редактировать</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Основная информация</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Клиент
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        <a href="{{ route('clients.show', $appointment->client) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ $appointment->client->full_name }}
                        </a>
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-xs"></i>
                        <span>{{ $appointment->client->phone }}</span>
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Услуга
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->service->name }}
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span class="font-medium">{{ number_format($appointment->final_price, 0, ',', ' ') }} Br</span>
                        <span>•</span>
                        <span>{{ $appointment->final_duration }} мин</span>
                    </p>
                </div>

                @if($appointment->master)
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Мастер
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}
                    </p>
                </div>
                @endif

                @if($appointment->location)
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Локация
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->location->name }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Дата и время -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Дата и время</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Дата
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->date->format('d.m.Y') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Время
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Статус
                    </label>
                    <div class="mt-1">
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
                </div>
            </div>
        </div>
    </div>

    @if($appointment->notes)
    <!-- Заметки -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-note-sticky text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Заметки</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                {{ $appointment->notes }}
            </p>
        </div>
    </div>
    @endif

    <!-- Дополнительная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Системная информация</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Дата создания
                    </label>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->created_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $appointment->created_at->format('H:i') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Последнее обновление
                    </label>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->updated_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $appointment->updated_at->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

