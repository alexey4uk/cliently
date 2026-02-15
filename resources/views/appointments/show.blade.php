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

<div class="max-w-4xl mx-auto">
        <!-- Appointment Header -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Запись #{{ $appointment->id }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-sm font-medium rounded-full
                            {{ $appointment->status === 'completed' ? 'text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-600' : '' }}
                            {{ $appointment->status === 'cancelled' ? 'text-rose-700 bg-rose-100 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-600' : '' }}
                            {{ $appointment->status === 'confirmed' ? 'text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200 dark:border-blue-600' : '' }}
                            {{ $appointment->status === 'pending' ? 'text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-600' : '' }}">
                            @if($appointment->status === 'completed')
                                <i class="fa-solid fa-check-circle text-xs"></i>
                                Завершено
                            @elseif($appointment->status === 'cancelled')
                                <i class="fa-solid fa-xmark-circle text-xs"></i>
                                Отменено
                            @elseif($appointment->status === 'confirmed')
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                Подтверждено
                            @else
                                <i class="fa-solid fa-clock text-xs"></i>
                                Ожидает подтверждения
                            @endif
                        </span>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">
                        Создана {{ $appointment->created_at->format('d.m.Y') }} в {{ $appointment->created_at->format('H:i') }}
                    </p>
                </div>
                <div class="sm:text-right">
                    <p class="text-3xl font-bold text-slate-900 dark:text-white">
                        {{ number_format($appointment->final_price, 0, ',', ' ') }} BYN
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Стоимость услуги</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Appointment Details -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Детали записи</h2>
                
                <div class="space-y-4">
                    <!-- Дата и время -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-calendar text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Дата и время</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $appointment->date->locale('ru')->isoFormat('D MMMM YYYY') }}, 
                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($appointment->time)->addMinutes($appointment->final_duration)->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- Услуга -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-500/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Услуга</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->service->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Длительность: {{ $appointment->final_duration }} мин</p>
                        </div>
                    </div>

                    <!-- Мастер -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                            {{ $appointment->master ? 'bg-emerald-100 dark:bg-emerald-500/20' : 'bg-amber-100 dark:bg-amber-500/20' }}">
                            <i class="fa-solid fa-user-tie {{ $appointment->master ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}"></i>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Мастер</p>
                            @if($appointment->master)
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->master->name }}</p>
                            @else
                                <p class="text-sm font-medium text-amber-700 dark:text-amber-300">Не назначен</p>
                                @if(isset($canUpdateAppointments) && $canUpdateAppointments && $appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                    @if(isset($mastersForAssign) && $mastersForAssign->isNotEmpty())
                                        <form id="assign-master" method="POST" action="{{ route('appointments.assign-master', ['appointment' => $appointment, 'from' => 'show']) }}" class="mt-2 flex flex-wrap items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="master_id" required class="rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-1.5 px-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">— Выберите мастера —</option>
                                                @foreach($mastersForAssign as $m)
                                                    <option value="{{ $m->id }}" {{ old('master_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                                <i class="fa-solid fa-check"></i>
                                                Назначить
                                            </button>
                                        </form>
                                        @error('master_id')
                                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Нет мастеров со свободным слотом на это время.</p>
                                        <a href="{{ route('appointments.edit', $appointment) }}?assign=1" class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                            <i class="fa-solid fa-user-plus"></i>
                                            Назначить мастера в форме редактирования
                                        </a>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Локация -->
                    @if($appointment->location)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 dark:bg-amber-500/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-location-dot text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Локация</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->location->name }}</p>
                            @if($appointment->location->full_address)
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $appointment->location->full_address }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                @if($appointment->notes)
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Заметки</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap">{{ $appointment->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Client Info -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Клиент</h2>
                
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($appointment->client->full_name) }}&background=6366f1&color=fff&size=64" 
                        class="w-16 h-16 rounded-full" 
                        alt="{{ $appointment->client->full_name }}">
                    <div class="ml-4">
                        <a href="{{ route('clients.show', $appointment->client) }}" 
                            class="text-lg font-medium text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ $appointment->client->full_name }}
                        </a>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Клиент с {{ $appointment->client->created_at->format('d.m.Y') }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    @if($appointment->client->phone)
                    <div class="flex items-center text-sm">
                        <i class="fa-solid fa-phone w-5 text-slate-400 mr-3"></i>
                        <button type="button" data-phone-modal-trigger
                            data-phone="{{ $appointment->client->phone }}"
                            data-phone-display="{{ $appointment->client->phone }}"
                            data-client-name="{{ $appointment->client->full_name }}"
                            class="text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ $appointment->client->phone }}
                        </button>
                    </div>
                    @endif
                    @if($appointment->client->email)
                    <div class="flex items-center text-sm">
                        <i class="fa-solid fa-envelope w-5 text-slate-400 mr-3"></i>
                        <span class="text-slate-900 dark:text-white">{{ $appointment->client->email }}</span>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('clients.show', $appointment->client) }}" 
                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                        Открыть профиль клиента →
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    @if($canUpdateAppointments)
                        <a href="{{ route('appointments.edit', $appointment) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-pen text-sm"></i>
                            <span>Редактировать</span>
                        </a>

                        @if($appointment->status === 'pending')
                            <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/20 border border-blue-200 dark:border-blue-600 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-500/30 transition-colors">
                                    <i class="fa-solid fa-check-circle text-sm"></i>
                                    <span>Подтвердить</span>
                                </button>
                            </form>
                            @if($appointment->client->telegram_user_id ?? null)
                                <form method="POST" action="{{ route('appointments.send-telegram-confirmation', $appointment) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                            title="Отправить клиенту в Telegram сообщение с кнопками «Подтвердить» / «Отменить»">
                                        <i class="fa-brands fa-telegram text-sm"></i>
                                        <span>Подтверждение в Telegram</span>
                                    </button>
                                </form>
                            @endif
                        @endif

                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline"
                              onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-600 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/30 transition-colors">
                                <i class="fa-solid fa-xmark-circle text-sm"></i>
                                <span>Отменить</span>
                            </button>
                        </form>
                    @elseif($appointment->status === 'confirmed')
                        @if($canUpdateAppointments)
                            <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-600 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/30 transition-colors">
                                    <i class="fa-solid fa-check-double text-sm"></i>
                                    <span>Завершить</span>
                                </button>
                            </form>
                        @endif

                        @if($canUpdateAppointments)
                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline"
                                  onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-600 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/30 transition-colors">
                                    <i class="fa-solid fa-xmark-circle text-sm"></i>
                                    <span>Отменить</span>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">История изменений</h2>
            
            <div class="space-y-4">
                @php
                    $statusHistory = [];
                    $currentStatus = $appointment->status;
                    $createdAt = $appointment->created_at;
                    $updatedAt = $appointment->updated_at;
                    
                    // Если статус изменился после создания
                    if ($updatedAt->gt($createdAt)) {
                        $statusHistory[] = [
                            'action' => 'Запись ' . ($currentStatus === 'confirmed' ? 'подтверждена' : ($currentStatus === 'completed' ? 'завершена' : ($currentStatus === 'cancelled' ? 'отменена' : 'обновлена'))),
                            'date' => $updatedAt,
                            'type' => $currentStatus,
                            'source' => 'Система'
                        ];
                    }
                    
                    // Добавляем создание записи
                    $statusHistory[] = [
                        'action' => 'Запись создана',
                        'date' => $createdAt,
                        'type' => 'created',
                        'source' => 'Администратор'
                    ];
                    
                    // Сортируем по дате (новые сверху)
                    usort($statusHistory, function($a, $b) {
                        return $b['date']->timestamp - $a['date']->timestamp;
                    });
                @endphp

                @foreach($statusHistory as $entry)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                            {{ $entry['type'] === 'confirmed' ? 'bg-blue-100 dark:bg-blue-500/20' : '' }}
                            {{ $entry['type'] === 'completed' ? 'bg-emerald-100 dark:bg-emerald-500/20' : '' }}
                            {{ $entry['type'] === 'cancelled' ? 'bg-rose-100 dark:bg-rose-500/20' : '' }}
                            {{ $entry['type'] === 'created' ? 'bg-indigo-100 dark:bg-indigo-500/20' : 'bg-slate-100 dark:bg-slate-800' }}">
                            @if($entry['type'] === 'confirmed')
                                <i class="fa-solid fa-check-circle text-blue-600 dark:text-blue-400 text-xs"></i>
                            @elseif($entry['type'] === 'completed')
                                <i class="fa-solid fa-check-double text-emerald-600 dark:text-emerald-400 text-xs"></i>
                            @elseif($entry['type'] === 'cancelled')
                                <i class="fa-solid fa-xmark-circle text-rose-600 dark:text-rose-400 text-xs"></i>
                            @else
                                <i class="fa-solid fa-plus text-indigo-600 dark:text-indigo-400 text-xs"></i>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-slate-900 dark:text-white">{{ $entry['action'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $entry['date']->format('d.m.Y') }} в {{ $entry['date']->format('H:i') }} · {{ $entry['source'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
