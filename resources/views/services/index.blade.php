@extends('layouts.user')

@section('title', 'Услуги - Cliently')
@section('page-title', 'Услуги')
@section('page-description', 'Управление услугами вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Услуги', 'url' => null]
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
    
    // Проверяем, есть ли хотя бы одно действие для услуг
    $hasAnyServiceAction = $hasBusinessPermission('client.services.update') || 
                          $hasBusinessPermission('client.services.delete');
@endphp

<div class="max-w-[1400px] mx-auto">
    <div x-data="{ 
    showDeleteModal: false,
    serviceToDelete: null,
    serviceName: '',
    openDeleteModal(serviceId, serviceName) {
        this.serviceToDelete = serviceId;
        this.serviceName = serviceName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.serviceToDelete = null;
        this.serviceName = '';
    },
    confirmDelete() {
        if (this.serviceToDelete) {
            const form = document.getElementById('delete-form-' + this.serviceToDelete);
            if (form) {
                form.submit();
            }
        }
    }
}">
    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Услуги</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управление услугами и прайс-листом вашего бизнеса</p>
            </div>
            @php
                $canCreateService = false;
                if ($hasBusinessPermission('client.services.create')) {
                    $subscriptionService = app(\App\Services\SubscriptionService::class);
                    $canCreateService = $subscriptionService->canCreateService(Auth::user());
                }
            @endphp
            @if($hasBusinessPermission('client.services.create') && $canCreateService)
                <a href="{{ route('services.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить услугу</span>
                </a>
            @elseif($hasBusinessPermission('client.services.create') && !$canCreateService)
                <button disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed"
                    title="Достигнут лимит услуг для вашего тарифа. Обновите тариф для добавления большего количества услуг.">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить услугу</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Список услуг -->
    @if($services->count() > 0)
        <!-- Таблица для больших экранов -->
        <div class="hidden md:block">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Услуга</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Цена</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Длительность</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                            @if($hasAnyServiceAction)
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($services as $service)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                                            <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $service->name }}
                                            </div>
                                            @if($service->description)
                                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                                    {{ $service->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                        {{ number_format($service->price, 0, ',', ' ') }} BYN
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-sm text-slate-600 dark:text-slate-400">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                        <span>{{ $service->duration }} мин</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($service->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-600">
                                            <i class="fa-solid fa-check-circle text-xs"></i>
                                            Активна
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 rounded-full border border-slate-200 dark:border-slate-700">
                                            <i class="fa-solid fa-pause-circle text-xs"></i>
                                            Неактивна
                                        </span>
                                    @endif
                                </td>
                                @if($hasAnyServiceAction)
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($hasBusinessPermission('client.services.update'))
                                                <a href="{{ route('services.edit', $service) }}" 
                                                    class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                    title="Редактировать">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($hasBusinessPermission('client.services.delete'))
                                                <form method="POST" action="{{ route('services.destroy', $service) }}" 
                                                      id="delete-form-{{ $service->id }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type="button"
                                                        @click="openDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
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
            @foreach($services as $service)
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <!-- Заголовок карточки -->
                    <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                    {{ $service->name }}
                                </h3>
                            </div>
                            @if($service->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-600">
                                    <i class="fa-solid fa-check-circle text-xs"></i>
                                    Активна
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 rounded-full border border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-pause-circle text-xs"></i>
                                    Неактивна
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1.5">
                                <span class="font-semibold text-slate-900 dark:text-white">
                                    {{ number_format($service->price, 0, ',', ' ') }} BYN
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                <i class="fa-solid fa-clock text-xs"></i>
                                <span>{{ $service->duration }} мин</span>
                            </div>
                        </div>
                    </div>

                    <!-- Содержимое карточки -->
                    @if($service->description)
                        <div class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-info-circle text-slate-600 dark:text-slate-400 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Описание</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                                        {{ $service->description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Действия -->
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                        <div class="flex items-center justify-end gap-3">
                            @if($hasBusinessPermission('client.services.update'))
                                <a href="{{ route('services.edit', $service) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <i class="fa-solid fa-pencil text-xs"></i>
                                    <span>Редактировать</span>
                                </a>
                            @endif

                            @if($hasBusinessPermission('client.services.delete'))
                                <form method="POST" action="{{ route('services.destroy', $service) }}"
                                      id="delete-form-{{ $service->id }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                        @click="openDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-white dark:bg-slate-800 border border-rose-300 dark:border-rose-700/50 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Удалить</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Пустое состояние -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="max-w-sm mx-auto">
                <div class="h-16 w-16 rounded-xl bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Услуги не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Начните работу с системой, добавив первую услугу в ваш прайс-лист
                </p>
                <a href="{{ route('services.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить услугу</span>
                </a>
            </div>
        </div>
    @endif

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
                    Вы уверены, что хотите удалить услугу <span class="font-semibold" x-text="serviceName"></span>? Это действие нельзя отменить.
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
    </div>
</div>

@endsection
