@extends('layouts.panel')

@section('title', 'Просмотр бизнеса')

@section('content')
    <div class="space-y-6">
        <!-- Flash сообщения -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg p-3 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm sm:text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
                <button @click="show = false"
                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors">
                    <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-3 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm sm:text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
                </div>
                <button @click="show = false"
                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                    <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
                </button>
            </div>
        @endif

        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-building text-white text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">{{ $business->name }}</h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Детальная информация о бизнесе</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('panel.businesses') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                        <i class="fa-solid fa-arrow-left text-xs sm:text-sm"></i>
                        <span>Назад к списку</span>
                    </a>
                    @can('panel.businesses.update')
                        <a href="{{ route('panel.businesses.edit', $business) }}"
                           class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-amber-50 hover:bg-amber-100 dark:bg-amber-500/20 dark:hover:bg-amber-500/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700/50 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                            <i class="fa-solid fa-pencil text-xs sm:text-sm"></i>
                            <span>Редактировать</span>
                        </a>
                    @endcan
                    @can('panel.businesses.delete')
                        <form method="POST" action="{{ route('panel.businesses.destroy', $business) }}" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот бизнес? Это действие нельзя отменить.');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/20 dark:hover:bg-rose-500/30 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-700/50 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                                <i class="fa-solid fa-trash text-xs sm:text-sm"></i>
                                <span>Удалить</span>
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-4 sm:mb-6">Основная информация</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Название -->
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Название</p>
                    <p class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white">{{ $business->name }}</p>
                </div>

                <!-- Телефон -->
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Телефон</p>
                    @if($business->phone)
                        <p class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white">{{ $business->phone }}</p>
                    @else
                        <p class="text-sm sm:text-base text-slate-400 dark:text-slate-500 italic">Не указан</p>
                    @endif
                </div>

                <!-- Описание -->
                @if($business->description)
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Описание</p>
                        <p class="text-sm sm:text-base text-slate-900 dark:text-white">{{ $business->description }}</p>
                    </div>
                @endif

                <!-- Дата создания -->
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Дата создания</p>
                    <p class="text-sm sm:text-base text-slate-900 dark:text-white">{{ $business->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Владелец -->
        @php
            $owner = $business->users->first();
        @endphp
        @if($owner)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-4 sm:mb-6">Владелец</h2>
                
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-12 w-12 sm:h-16 sm:w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-base sm:text-lg shadow-sm">
                        {{ strtoupper(mb_substr($owner->name ?? ($owner->pivot->first_name ?? ''), 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">
                            {{ $owner->name ?? ($owner->pivot->first_name . ' ' . ($owner->pivot->last_name ?? '')) }}
                        </p>
                        @if($owner->email)
                            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-1">{{ $owner->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Статистика -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-4 sm:mb-6">Статистика</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400 text-sm"></i>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Клиенты</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $business->clients_count }}</p>
                </div>
                
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-briefcase text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Услуги</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $business->services_count }}</p>
                </div>
                
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-user-tie text-amber-600 dark:text-amber-400 text-sm"></i>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Мастера</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $business->masters_count }}</p>
                </div>
                
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-map-marker-alt text-blue-600 dark:text-blue-400 text-sm"></i>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Локации</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $business->locations_count }}</p>
                </div>
                
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-calendar-check text-purple-600 dark:text-purple-400 text-sm"></i>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Записи</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $business->appointments_count }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
