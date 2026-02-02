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

@php
    // Получаем бизнес и роль для проверки прав доступа
    $user = Auth::user();
    $currentBusiness = null;
    $currentBusinessRole = null;
    $currentBusinessRoleId = null;
    $permissionService = null;
    if ($user) {
        $user->load('businesses');
        $currentBusiness = $user->businesses->first();
        if ($currentBusiness) {
            $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
            $currentBusinessRole = $pivot?->pivot->role_id ? \App\Models\BusinessRole::find($pivot->pivot->role_id)?->slug : null;
            $currentBusinessRoleId = $pivot?->pivot->role_id;
            if ($currentBusinessRoleId) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
            }
        }
    }

    // Функция для проверки бизнес-прав
    $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
        if (!$currentBusinessRoleId || !$permissionService) {
            return false;
        }
        return $permissionService->hasPermission($currentBusinessRoleId, $permission);
    };
@endphp

<div class="max-w-4xl mx-auto">
    <div x-data="{ 
        showPhoneModal: false, 
        phone: '', 
        phoneDisplay: '', 
        client: '',
        openPhoneModal(phone, phoneDisplay, client) {
            this.phone = phone;
            this.phoneDisplay = phoneDisplay;
            this.client = client;
            this.showPhoneModal = true;
        },
        closePhoneModal() {
            this.showPhoneModal = false;
        }
    }">
        
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
                    @if($appointment->master)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-user-tie text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Мастер</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->master->name }}</p>
                        </div>
                    </div>
                    @endif

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
                        <button @click="openPhoneModal('{{ $appointment->client->phone }}', '{{ $appointment->client->phone }}', '{{ addslashes($appointment->client->full_name) }}')"
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
                    @if($hasBusinessPermission('client.appointments.update'))
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
                        @if($hasBusinessPermission('client.appointments.update'))
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

                        @if($hasBusinessPermission('client.appointments.update'))
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

    <!-- Модальное окно для номера телефона -->
    <div x-show="showPhoneModal" 
         @click.away="closePhoneModal()"
         @keydown.escape.window="closePhoneModal()"
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
