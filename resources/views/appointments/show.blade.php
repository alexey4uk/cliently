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
    <!-- Заголовок с действиями -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                Запись #{{ $appointment->id }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $appointment->date->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Быстрые действия -->
            @if($appointment->status === 'pending')
                <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-check-circle"></i>
                        Подтвердить
                    </button>
                </form>
            @endif

            @if(in_array($appointment->status, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-check-double"></i>
                        Выполнить
                    </button>
                </form>
            @endif

            @if(!in_array($appointment->status, ['completed', 'cancelled']))
                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" 
                      onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');"
                      class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-times-circle"></i>
                        Отменить
                    </button>
                </form>
            @endif

            <a href="{{ route('appointments.edit', $appointment) }}"
               class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-edit"></i>
                Редактировать
            </a>
            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" 
                  onsubmit="return confirm('Вы уверены, что хотите удалить эту запись?');"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    Удалить
                </button>
            </form>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400"></i>
                    Основная информация
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Клиент
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        <a href="{{ route('clients.show', $appointment->client) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ $appointment->client->full_name }}
                        </a>
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $appointment->client->phone }}
                    </p>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Услуга
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $appointment->service->name }}
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ number_format($appointment->final_price, 0, ',', ' ') }} Br • {{ $appointment->final_duration }} мин
                    </p>
                </div>

                @if($appointment->master)
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Мастер
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}
                    </p>
                </div>
                @endif

                @if($appointment->location)
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Локация
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $appointment->location->name }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Дата и время -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                    Дата и время
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Дата
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $appointment->date->format('d.m.Y') }}
                    </p>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Время
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                    </p>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Статус
                    </label>
                    <p class="mt-1">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                                'confirmed' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                                'completed' => 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300',
                                'cancelled' => 'bg-rose-100 dark:bg-rose-900/20 text-rose-800 dark:text-rose-300',
                            ];
                            $statusLabels = [
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждена',
                                'completed' => 'Завершена',
                                'cancelled' => 'Отменена',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium {{ $statusColors[$appointment->status] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300' }}">
                            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($appointment->notes)
    <!-- Заметки -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-indigo-600 dark:text-indigo-400"></i>
                    Заметки
                </h3>
            </div>

            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap">
                    {{ $appointment->notes }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Дополнительная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
                    Дополнительная информация
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Дата создания
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $appointment->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Последнее обновление
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $appointment->updated_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

