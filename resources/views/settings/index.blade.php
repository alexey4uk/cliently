@extends('layouts.user')

@section('title', 'Настройки бизнеса - Cliently')
@section('page-title', 'Настройки бизнеса')
@section('page-description', 'Управление данными вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки']]" />
@endpush

@section('content')
    <div class="space-y-6 w-full overflow-x-hidden">
        <!-- Заголовок страницы -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Настройки бизнеса</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управляйте основными параметрами вашей компании</p>
            </div>
            
            <a href="{{ route('settings.business.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg border border-indigo-200 dark:border-indigo-800 transition-all active:scale-95">
                <i class="fa-solid fa-pencil text-xs"></i>
                <span>Редактировать бизнес</span>
            </a>
        </div>

        <!-- Карточка информации о бизнесе -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-start gap-4">
                <!-- Иконка -->
                <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-building text-white"></i>
                </div>
                
                <!-- Информация -->
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white truncate">
                        {{ $business->name }}
                    </h2>
                    
                    @if ($business->phone || $business->email)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-2">
                            @if ($business->phone)
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                    <i class="fa-solid fa-phone text-slate-400 dark:text-slate-500"></i>
                                    <span>{{ $business->phone }}</span>
                                </div>
                            @endif
                            
                            @if ($business->email)
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                    <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500"></i>
                                    <span class="truncate max-w-[200px]">{{ $business->email }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    {{-- <!-- Slug для доступа -->
                    @if ($business->slug)
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                                <i class="fa-solid fa-link"></i>
                                <span>Идентификатор для клиентов:</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 px-2 py-1 rounded">
                                    {{ $business->slug }}
                                </span>
                                <a href="{{ route('public.appointments.show', ['slug' => $business->slug]) }}" 
                                   target="_blank"
                                   class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 hover:underline">
                                    Посмотреть страницу →
                                </a>
                            </div>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>

        <!-- Основные разделы -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Основные разделы
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Карточка: Локации -->
                <a href="{{ route('settings.locations') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Локации
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            <span
                                class="font-semibold text-slate-900 dark:text-white">{{ $business->locations->count() }}</span>
                            <span
                                class="ml-1">{{ $business->locations->count() === 1 ? 'локация' : ($business->locations->count() < 5 ? 'локации' : 'локаций') }}</span>
                        </p>
                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Карточка: Услуги -->
                <a href="{{ route('services.index') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Услуги
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            <span
                                class="font-semibold text-slate-900 dark:text-white">{{ $business->services->count() }}</span>
                            <span
                                class="ml-1">{{ $business->services->count() === 1 ? 'услуга' : ($business->services->count() < 5 ? 'услуги' : 'услуг') }}</span>
                        </p>
                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Карточка: Мастера -->
                <a href="{{ route('settings.masters') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Мастера
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            <span
                                class="font-semibold text-slate-900 dark:text-white">{{ $business->masters->count() }}</span>
                            <span
                                class="ml-1">{{ $business->masters->count() === 1 ? 'мастер' : ($business->masters->count() < 5 ? 'мастера' : 'мастеров') }}</span>
                        </p>
                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Карточка: Онлайн-запись -->
                @php
                    $isBookingEnabled = $business->online_booking_enabled ?? true;
                @endphp
                <a href="{{ route('settings.online-booking') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Онлайн-запись
                        </h3>

                        @if ($business->slug)
                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                                <span class="font-semibold text-slate-900 dark:text-white">
                                    @if ($isBookingEnabled)
                                        <span class="text-emerald-600 dark:text-emerald-400">Включена</span>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400">Выключена</span>
                                    @endif
                                </span>
                            </p>
                        @else
                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                                <span class="font-semibold text-amber-600 dark:text-amber-400">Не настроено</span>
                            </p>
                        @endif

                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="mr-1">Управление</span>
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Карточка: Telegram Бот -->
                @php
                    $telegramBotActive = $business->telegram_chat_id;
                @endphp
                <a href="{{ route('settings.telegram') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-brands fa-telegram text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Telegram Бот
                        </h3>

                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            @if ($telegramBotActive)
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Подключен</span>
                            @else
                                <span class="font-semibold text-amber-600 dark:text-amber-400">Требуется настройка</span>
                            @endif
                        </p>

                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="mr-1">Настройка</span>
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Информационные подсказки -->
        @if ($business->locations->count() === 0 || $business->services->count() === 0 || $business->masters->count() === 0)
            <div
                class="bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-lg p-4 md:p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    {{-- Иконка предупреждения --}}
                    <div
                        class="h-10 w-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-300"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1.5">
                            Начните настройку бизнеса
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            Для полноценной работы системы рекомендуется настроить следующие разделы:
                        </p>

                        {{-- Список недостающих элементов --}}
                        <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                            @if ($business->locations->count() === 0)
                                <li class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                    <span>Добавьте хотя бы одну локацию</span>
                                </li>
                            @endif
                            @if ($business->services->count() === 0)
                                <li class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                    <span>Создайте услуги для записи</span>
                                </li>
                            @endif
                            @if ($business->masters->count() === 0)
                                <li class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
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
