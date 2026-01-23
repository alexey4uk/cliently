@extends('layouts.user')

@section('title', 'Профиль клиента - Cliently')
@section('page-title', 'Профиль клиента')
@section('page-description', 'Информация о клиенте')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => $client->full_name, 'url' => null],
    ]" />
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

<div x-data="{
    showPhoneModal: false,
    phone: '',
    phoneDisplay: '',
    client: '',
    showDeleteModal: false,
    openPhoneModal(phone, phoneDisplay, client) {
        this.phone = phone;
        this.phoneDisplay = phoneDisplay;
        this.client = client;
        this.showPhoneModal = true;
    },
    closePhoneModal() {
        this.showPhoneModal = false;
    },
    openDeleteModal() {
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
    },
    confirmDelete() {
        const form = document.getElementById('delete-form');
        if (form) {
            form.submit();
        }
    }
}">

    <main class="p-4 sm:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Client Info -->
            <div class="space-y-6">
                <!-- Profile Card -->
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4 text-2xl font-semibold text-slate-600 dark:text-slate-300">
                            {{ $client->initials }}
                        </div>
                        <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $client->full_name }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">ID: {{ $client->id }}</p>
                        @if ($totalAppointments > 0)
                            <span class="inline-block mt-2 px-3 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/20 rounded-full border border-emerald-200 dark:border-emerald-600">
                                Активный клиент
                            </span>
                        @else
                            <span class="inline-block mt-2 px-3 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700">
                                Новый клиент
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center text-sm">
                            <i class="fa-solid fa-phone w-5 text-slate-400 dark:text-slate-500 mr-3"></i>
                            <button @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                class="text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                {{ $client->phone }}
                            </button>
                        </div>
                        @if ($client->email)
                            <div class="flex items-center text-sm">
                                <i class="fa-solid fa-envelope w-5 text-slate-400 dark:text-slate-500 mr-3"></i>
                                <span class="text-slate-900 dark:text-white">{{ $client->email }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm">
                            <i class="fa-solid fa-calendar-plus w-5 text-slate-400 dark:text-slate-500 mr-3"></i>
                            <span class="text-slate-900 dark:text-white">Клиент с {{ $client->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 flex gap-2">
                        @if($hasBusinessPermission('appointments.create'))
                            <a href="{{ route('appointments.create', ['client_id' => $client->id]) }}"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                <i class="fa-solid fa-calendar-plus text-sm"></i>
                                <span>Записать</span>
                            </a>
                        @endif

                        @if($hasBusinessPermission('clients.update'))
                            <a href="{{ route('clients.edit', $client) }}"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <i class="fa-solid fa-pencil text-sm"></i>
                            </a>
                        @endif

                        @if($hasBusinessPermission('clients.delete'))
                            <button @click="openDeleteModal()"
                                class="px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/30 rounded-lg transition-colors">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Статистика</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalAppointments }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Записей</p>
                        </div>
                        <div class="text-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($totalSpent, 0, ',', ' ') }} ₽</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Потрачено</p>
                        </div>
                        <div class="text-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($avgCheck, 0, ',', ' ') }} ₽</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Средний чек</p>
                        </div>
                        <div class="text-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <p class="text-2xl font-bold text-slate-600 dark:text-slate-400">{{ $cancelledAppointments }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Отмен</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column - History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Upcoming Appointments -->
                @if ($upcomingAppointmentsList->count() > 0)
                    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                        <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Предстоящие записи</h2>
                        </div>
                        <div class="p-5 space-y-4">
                            @foreach ($upcomingAppointmentsList as $appointment)
                                <div class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg border border-indigo-100 dark:border-indigo-500/20">
                                    <div class="flex-shrink-0 w-16 text-center">
                                        <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</p>
                                        <p class="text-xs text-indigo-500 dark:text-indigo-400">{{ \Carbon\Carbon::parse($appointment->date)->format('d M') }}</p>
                                    </div>
                                    <div class="flex-shrink-0 w-px h-12 bg-indigo-200 dark:bg-indigo-500/30 mx-4"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->service->name }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            @if ($appointment->master)
                                                Мастер: {{ $appointment->master->name }}
                                            @else
                                                Мастер не назначен
                                            @endif
                                        </p>
                                    </div>
                                    <span class="px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/20 rounded-full border border-emerald-200 dark:border-emerald-600">
                                        {{ $appointment->status === 'confirmed' ? 'Подтверждено' : 'Ожидает' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Appointment History -->
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">История записей</h2>
                        <a href="{{ route('appointments.index', ['client_id' => $client->id]) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                            Все записи
                        </a>
                    </div>
                    <div class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse ($appointmentHistory as $appointment)
                            <div class="p-5 flex items-center">
                                <div class="flex-shrink-0 w-16 text-center">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->date)->format('d M') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($appointment->date)->format('Y') }}</p>
                                </div>
                                <div class="flex-1 ml-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->service->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        @if ($appointment->master)
                                            {{ $appointment->master->name }}
                                        @else
                                            Мастер не назначен
                                        @endif
                                        @if ($appointment->final_price)
                                            · {{ number_format($appointment->final_price, 0, ',', ' ') }} ₽
                                        @endif
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/20 rounded-full border border-emerald-200 dark:border-emerald-600">
                                    Завершено
                                </span>
                            </div>
                        @empty
                            <div class="p-5 text-center">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Нет завершенных записей</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

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

    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" @click.away="closeDeleteModal()" @keydown.escape.window="closeDeleteModal()"
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
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                <button @click="closeDeleteModal()"
                    class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 py-4">
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-4">
                    Вы уверены, что хотите удалить клиента <span
                        class="font-semibold text-slate-900 dark:text-white">{{ $client->full_name }}</span>?
                </p>
                <p class="text-xs text-rose-600 dark:text-rose-400 mb-4">
                    Это действие нельзя отменить.
                </p>
                <div class="flex gap-2">
                    <button @click="closeDeleteModal()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Отмена
                    </button>
                    <form method="POST" action="{{ route('clients.destroy', $client) }}" id="delete-form" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" @click="closeDeleteModal()"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                            Удалить
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
