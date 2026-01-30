@extends('layouts.user')

@section('title', 'Настройки бизнеса - Cliently')
@section('page-title', 'Настройки бизнеса')
@section('page-description', 'Управление данными вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки']]" />
@endpush

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Настройки бизнеса</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управляйте основными параметрами вашей компании</p>
            </div>
            <a href="{{ route('settings.business.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-pencil text-sm"></i>
                <span>Редактировать бизнес</span>
            </a>
        </div>
    </div>

    <!-- Основные разделы -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основные разделы</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Карточка: Локации -->
            <a href="{{ route('settings.locations') }}"
                class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="h-12 w-12 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mb-4 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-500/30 transition-colors">
                    <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400 text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    Локации
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    <span class="font-semibold text-slate-900 dark:text-white">{{ $business->locations->count() }}</span>
                    <span class="ml-1">{{ $business->locations->count() === 1 ? 'локация' : ($business->locations->count() < 5 ? 'локации' : 'локаций') }}</span>
                </p>
                <div class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                    <span>Управление</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Карточка: Услуги -->
            <a href="{{ route('services.index') }}"
                class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center mb-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                    <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400 text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                    Услуги
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    <span class="font-semibold text-slate-900 dark:text-white">{{ $business->services->count() }}</span>
                    <span class="ml-1">{{ $business->services->count() === 1 ? 'услуга' : ($business->services->count() < 5 ? 'услуги' : 'услуг') }}</span>
                </p>
                <div class="flex items-center gap-2 text-sm text-purple-600 dark:text-purple-400 font-medium">
                    <span>Управление</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Карточка: Мастера -->
            <a href="{{ route('settings.masters') }}"
                class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="h-12 w-12 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-4 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/30 transition-colors">
                    <i class="fa-solid fa-user-tie text-emerald-600 dark:text-emerald-400 text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    Мастера
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    <span class="font-semibold text-slate-900 dark:text-white">{{ $business->masters->count() }}</span>
                    <span class="ml-1">{{ $business->masters->count() === 1 ? 'мастер' : ($business->masters->count() < 5 ? 'мастера' : 'мастеров') }}</span>
                </p>
                <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                    <span>Управление</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Карточка: Онлайн-запись -->
            @php
                $isBookingEnabled = $business->online_booking_enabled ?? true;
            @endphp
            <a href="{{ route('settings.online-booking') }}"
                class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center mb-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-500/30 transition-colors">
                    <i class="fa-solid fa-calendar-check text-blue-600 dark:text-blue-400 text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    Онлайн-запись
                </h3>
                @if ($business->slug)
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        @if ($isBookingEnabled)
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                                <i class="fa-solid fa-check-circle text-xs"></i>
                                Включена
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                                <i class="fa-solid fa-pause-circle text-xs"></i>
                                Выключена
                            </span>
                        @endif
                    </p>
                @else
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                            <i class="fa-solid fa-exclamation-circle text-xs"></i>
                            Не настроено
                        </span>
                    </p>
                @endif
                <div class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 font-medium">
                    <span>Управление</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Карточка: Telegram Бот -->
            @php
                $telegramBotActive = $business->telegram_chat_id;
                
                // Проверяем доступ к Telegram боту согласно тарифу
                $hasTelegramAccess = false;
                $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
                if ($ownerRole) {
                    $ownerPivot = \Illuminate\Support\Facades\DB::table('business_user')
                        ->where('business_id', $business->id)
                        ->where('role_id', $ownerRole->id)
                        ->first();
                    if ($ownerPivot) {
                        $owner = \App\Models\User::find($ownerPivot->user_id);
                        if ($owner) {
                            $subscriptionService = app(\App\Services\SubscriptionService::class);
                            $telegramEnabled = $subscriptionService->getLimit($owner, 'telegram_bot_enabled');
                            $hasTelegramAccess = $telegramEnabled === true;
                        }
                    }
                }
            @endphp
            @if($hasTelegramAccess)
            <a href="{{ route('settings.telegram') }}"
                class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="h-12 w-12 rounded-lg bg-cyan-100 dark:bg-cyan-500/20 flex items-center justify-center mb-4 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-500/30 transition-colors">
                    <i class="fa-brands fa-telegram text-cyan-600 dark:text-cyan-400 text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                    Telegram Бот
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    @if ($telegramBotActive)
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                            <i class="fa-solid fa-check-circle text-xs"></i>
                            Подключен
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                            <i class="fa-solid fa-exclamation-circle text-xs"></i>
                            Требуется настройка
                        </span>
                    @endif
                </p>
                <div class="flex items-center gap-2 text-sm text-cyan-600 dark:text-cyan-400 font-medium">
                    <span>Настройка</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
            @endif

            <!-- Карточка: Тарифы и подписка -->
            @php
                $user = Auth::user();
                $currentSubscription = $user->activeSubscription();
                $currentPlan = $currentSubscription ? $currentSubscription->plan : null;
                
                // Получаем бизнес и роль для проверки прав доступа
                $currentBusiness = null;
                $currentBusinessRoleId = null;
                $permissionService = null;
                if ($user) {
                    $user->load('businesses');
                    $currentBusiness = $user->businesses->first();
                    if ($currentBusiness) {
                        $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
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
                
                $hasSubscriptionAccess = $hasBusinessPermission('client.subscription.view');
            @endphp
            @if($hasSubscriptionAccess)
                <a href="{{ route('subscription.index') }}"
                    class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                    <div class="h-12 w-12 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mb-4 group-hover:bg-amber-200 dark:group-hover:bg-amber-500/30 transition-colors">
                        <i class="fa-solid fa-crown text-amber-600 dark:text-amber-400 text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Тарифы и подписка
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        @if($currentPlan)
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-500/20 dark:text-slate-300 rounded-full">
                                {{ $currentPlan->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                                <i class="fa-solid fa-exclamation-circle text-xs"></i>
                                Не выбран
                            </span>
                        @endif
                    </p>
                    <div class="flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 font-medium">
                        <span>Управление</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- Информационные подсказки -->
    @if ($business->locations->count() === 0 || $business->services->count() === 0 || $business->masters->count() === 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-amber-200 dark:border-amber-800 shadow-sm p-6">
            <div class="flex items-start gap-4">
                <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                        Начните настройку бизнеса
                    </h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Для полноценной работы системы рекомендуется настроить следующие разделы:
                    </p>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        @if ($business->locations->count() === 0)
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>Добавьте хотя бы одну локацию</span>
                            </li>
                        @endif
                        @if ($business->services->count() === 0)
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>Создайте услуги для записи</span>
                            </li>
                        @endif
                        @if ($business->masters->count() === 0)
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>Добавьте мастеров</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
