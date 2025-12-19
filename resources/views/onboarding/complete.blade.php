@extends('layouts.user')

@section('title', 'Настройка завершена - Cliently')
@section('page-title', 'Настройка завершена')
@section('page-description', 'Ваш бизнес готов к работе')

@section('content')
    <!-- Центральный блок -->
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-200px)] text-center px-4">
        <div class="mb-6 md:mb-8">
            <div class="mx-auto w-16 md:w-20 h-16 md:h-20 flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20 mb-4 md:mb-6">
                <i class="fa-solid fa-check text-2xl md:text-3xl text-emerald-600 dark:text-emerald-300"></i>
            </div>

            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white mb-2 md:mb-3">Настройка завершена!</h1>

            <p class="text-sm md:text-base text-slate-600 dark:text-slate-400 max-w-md mx-auto mb-6 md:mb-8">
                Ваш бизнес успешно настроен. Теперь вы можете принимать записи от клиентов.
            </p>
        </div>

        <!-- Резюме результатов -->
        <div class="w-full max-w-4xl mb-8 md:mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                <!-- Бизнес -->
                <div class="rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-4 md:p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex-shrink-0">
                            <i class="fa-solid fa-store text-base text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="text-sm md:text-base font-medium text-slate-900 dark:text-white mb-1">{{ $business->name }}</p>
                            <a href="https://cliently.by/{{ $business->slug }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                <span>https://cliently.by/{{ $business->slug }}</span>
                                <i class="fa-solid fa-external-link-alt text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Локация -->
                <div class="rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-4 md:p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex-shrink-0">
                            <i class="fa-solid fa-location-dot text-base text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            @if($business->locations->first())
                                <p class="text-sm md:text-base font-medium text-slate-900 dark:text-white mb-1">{{ $business->locations->first()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $business->locations->first()->full_address }}</p>
                            @else
                                <p class="text-xs text-slate-500 dark:text-slate-400">Не добавлена</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Услуга -->
                <div class="rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-4 md:p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex-shrink-0">
                            <i class="fa-solid fa-scissors text-base text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            @if($business->services->first())
                                <p class="text-sm md:text-base font-medium text-slate-900 dark:text-white mb-1">{{ $business->services->first()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ number_format($business->services->first()->price, 0, ',', ' ') }} BYN</p>
                            @else
                                <p class="text-xs text-slate-500 dark:text-slate-400">Не добавлена</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Мастер -->
                <div class="rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-4 md:p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex-shrink-0">
                            <i class="fa-solid fa-user-check text-base text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            @if($business->masters->first())
                                @php
                                    $master = $business->masters->first();
                                    $masterName = trim(($master->first_name ?? '') . ' ' . ($master->last_name ?? ''));
                                @endphp
                                <p class="text-sm md:text-base font-medium text-slate-900 dark:text-white mb-1">{{ $masterName ?: 'Мастер' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $master->specialization }}</p>
                            @else
                                <p class="text-xs text-slate-500 dark:text-slate-400">Не добавлен</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="w-full max-w-sm">
            <a href="{{ route('dashboard') }}"
               class="block w-full px-4 md:px-6 py-2.5 md:py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm md:text-base font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Перейти в дашборд
            </a>
        </div>
    </div>
@endsection
