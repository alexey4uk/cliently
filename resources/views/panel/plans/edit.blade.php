@extends('layouts.panel')

@section('title', 'Редактирование тарифа')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 sm:pb-8">
        <!-- Breadcrumbs -->
        <nav class="mb-4 sm:mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 overflow-x-auto">
                <li class="flex-shrink-0">
                    <a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-home sm:hidden"></i>
                        <span class="hidden sm:inline">Главная</span>
                    </a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0">
                    <a href="{{ route('panel.plans.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Тарифы
                    </a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0 text-slate-900 dark:text-white font-medium">Редактирование</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-edit text-white text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 truncate">{{ $plan->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Измените параметры тарифа ниже</p>
                </div>
                @if($plan->subscriptions_count > 0)
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                            <i class="fa-solid fa-users"></i>
                            {{ $plan->subscriptions_count }} {{ $plan->subscriptions_count === 1 ? 'подписка' : 'подписок' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('panel.plans.update', $plan) }}" id="plan-form" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Секция 1: Основная информация -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">1</span>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Основная информация</h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Название, описание и идентификатор тарифа</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-tag text-xs text-slate-400 mr-1.5"></i>Название тарифа
                                <span class="text-rose-500 ml-1" aria-label="обязательное поле">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $plan->name) }}"
                                required
                                autocomplete="off"
                                aria-required="true"
                                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('name') ? 'name-error' : 'name-help' }}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                placeholder="Например: Базовый тариф"
                            />
                            @if(!$errors->has('name'))
                                <p id="name-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    Отображается пользователям
                                </p>
                            @endif
                            @error('name')
                                <p id="name-error" class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-link text-xs text-slate-400 mr-1.5"></i>Slug (URL-идентификатор)
                            </label>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                value="{{ old('slug', $plan->slug) }}"
                                autocomplete="off"
                                pattern="[a-z0-9-]+"
                                aria-describedby="slug-help"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                placeholder="bazovyj-tarif"
                            />
                            <p id="slug-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Используется в URL и API
                            </p>
                            @error('slug')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-align-left text-xs text-slate-400 mr-1.5"></i>Описание тарифа
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            maxlength="500"
                            aria-describedby="description-help description-count"
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all resize-none text-sm sm:text-base"
                            placeholder="Опишите особенности и преимущества тарифа..."
                        >{{ old('description', $plan->description) }}</textarea>
                        <div class="mt-2 flex items-center justify-between">
                            <p id="description-help" class="text-xs text-slate-500 dark:text-slate-400">
                                Краткое описание для пользователей
                            </p>
                            <span id="description-count" class="text-xs text-slate-400 dark:text-slate-500">
                                <span id="description-length">{{ mb_strlen(old('description', $plan->description)) }}</span>/500
                            </span>
                        </div>
                        @error('description')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Секция 2: Цена и подписка -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">2</span>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Цена и подписка</h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Настройте стоимость и период подписки</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-coins text-xs text-slate-400 mr-1.5"></i>Цена (BYN)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    value="{{ old('price', $plan->price) }}"
                                    step="0.01"
                                    min="0"
                                    inputmode="decimal"
                                    aria-describedby="price-help"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>
                            <p id="price-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Укажите 0 для бесплатного тарифа
                            </p>
                            @error('price')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="interval" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-calendar text-xs text-slate-400 mr-1.5"></i>Период подписки
                                <span class="text-rose-500 ml-1" aria-label="обязательное поле">*</span>
                            </label>
                            <select
                                id="interval"
                                name="interval"
                                required
                                aria-required="true"
                                aria-invalid="{{ $errors->has('interval') ? 'true' : 'false' }}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('interval') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                            >
                                <option value="monthly" {{ old('interval', $plan->interval) === 'monthly' ? 'selected' : '' }}>Ежемесячно</option>
                                <option value="yearly" {{ old('interval', $plan->interval) === 'yearly' ? 'selected' : '' }}>Ежегодно</option>
                            </select>
                            @error('interval')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="trial_days" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-clock text-xs text-slate-400 mr-1.5"></i>Пробный период (дней)
                            </label>
                            <input
                                type="number"
                                id="trial_days"
                                name="trial_days"
                                value="{{ old('trial_days', $plan->trial_days) }}"
                                min="0"
                                step="1"
                                inputmode="numeric"
                                aria-describedby="trial_days-help"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                            />
                            <p id="trial_days-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Количество дней бесплатного пробного периода
                            </p>
                            @error('trial_days')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Секция 3: Настройки видимости -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-purple-600 dark:text-purple-400">3</span>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Настройки видимости</h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Управление доступностью тарифа</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-start gap-3 p-4 rounded-lg border-2 {{ old('is_active', $plan->is_active) ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600' }} bg-slate-50 dark:bg-slate-800/50 cursor-pointer transition-all group">
                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                                class="mt-0.5 w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2 flex-shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Активен</span>
                                    @if(old('is_active', $plan->is_active))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">
                                            <i class="fa-solid fa-check mr-1"></i>Включено
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Тариф будет доступен для выбора пользователями</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 rounded-lg border-2 {{ old('is_default', $plan->is_default) ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600' }} bg-slate-50 dark:bg-slate-800/50 cursor-pointer transition-all group">
                            <input
                                type="checkbox"
                                id="is_default"
                                name="is_default"
                                value="1"
                                {{ old('is_default', $plan->is_default) ? 'checked' : '' }}
                                class="mt-0.5 w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2 flex-shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">По умолчанию</span>
                                    @if(old('is_default', $plan->is_default))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                            <i class="fa-solid fa-star mr-1"></i>Активно
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Автоматически назначается новым пользователям при регистрации</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Секция 4: Свойства тарифа -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">4</span>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Свойства тарифа</h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Настройте лимиты и возможности тарифа</p>
                            </div>
                        </div>
                        @if($plan->features->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                {{ $plan->features->count() }} {{ $plan->features->count() === 1 ? 'свойство' : 'свойств' }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <div id="features-container" class="space-y-4 mb-4">
                        <!-- Features will be populated from existing plan -->
                    </div>

                    <button 
                        type="button" 
                        id="add-feature-btn"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-all shadow-sm hover:shadow-md text-sm sm:text-base">
                        <i class="fa-solid fa-plus"></i>
                        <span>Добавить свойство</span>
                    </button>

                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Свойства определяют лимиты и возможности тарифа
                    </p>
                </div>
            </div>

            <!-- Sticky Actions Bar -->
            <div class="sticky bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm border-t-2 border-slate-200 dark:border-slate-800 shadow-2xl sm:shadow-none sm:border-t-0 sm:static sm:bg-transparent sm:backdrop-blur-none">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="py-4 sm:py-6">
                        <!-- Информационная панель (только на десктопе) -->
                        <div class="hidden sm:flex items-center justify-between mb-4 pb-4 border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    <span>Все обязательные поля заполнены</span>
                                </div>
                                <div class="flex items-center gap-1.5" id="features-count-display">
                                    <i class="fa-solid fa-list-check text-blue-500"></i>
                                    <span>Свойства: <strong class="text-slate-700 dark:text-slate-300" id="features-count">{{ $plan->features->count() }}</strong></span>
                                </div>
                                @if($plan->subscriptions_count > 0)
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-users text-amber-500"></i>
                                        <span>Активных подписок: <strong class="text-slate-700 dark:text-slate-300">{{ $plan->subscriptions_count }}</strong></span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-keyboard mr-1"></i>
                                <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-xs">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-xs">Enter</kbd> для сохранения
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                            <div class="flex items-center gap-3 order-2 sm:order-1">
                                <a
                                    href="{{ route('panel.plans.index') }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium transition-all shadow-sm hover:shadow-md text-sm sm:text-base">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span class="hidden sm:inline">Вернуться к списку</span>
                                    <span class="sm:hidden">Отмена</span>
                                </a>
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="hidden sm:inline-flex items-center justify-center gap-2 px-4 py-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-medium transition-all text-sm">
                                    <i class="fa-solid fa-undo"></i>
                                    <span>Назад</span>
                                </button>
                            </div>
                            
                            <div class="flex items-center gap-3 order-1 sm:order-2">
                                <button
                                    type="submit"
                                    id="submit-btn"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-5 sm:px-6 py-3 bg-gradient-to-r from-indigo-600 via-indigo-600 to-indigo-700 hover:from-indigo-700 hover:via-indigo-700 hover:to-indigo-800 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl text-sm sm:text-base group">
                                    <i class="fa-solid fa-save group-hover:scale-110 transition-transform"></i>
                                    <span>Сохранить изменения</span>
                                    <i class="fa-solid fa-arrow-right ml-1 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Мобильная подсказка -->
                        <p class="mt-3 sm:hidden text-center text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Проверьте все поля перед сохранением
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    @php
        $featuresData = $plan->features->map(function($f) {
            return [
                'key' => $f->feature_key,
                'value' => $f->feature_value,
                'type' => $f->feature_type,
            ];
        })->toArray();
    @endphp
    <x-plan-features-init :availableFeatures="$availableFeatures ?? []" :existingFeatures="$featuresData" />
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Счетчик символов для описания
            const descriptionTextarea = document.getElementById('description');
            const descriptionCount = document.getElementById('description-length');
            if (descriptionTextarea && descriptionCount) {
                descriptionTextarea.addEventListener('input', function() {
                    descriptionCount.textContent = this.value.length;
                });
            }

            // Обновление счетчика свойств
            const updateFeaturesCount = () => {
                const featuresCount = document.getElementById('features-count');
                const container = document.getElementById('features-container');
                if (featuresCount && container) {
                    const count = container.querySelectorAll('.feature-item').length;
                    featuresCount.textContent = count;
                }
            };

            // Наблюдаем за изменениями в контейнере свойств
            const featuresContainer = document.getElementById('features-container');
            if (featuresContainer) {
                const observer = new MutationObserver(updateFeaturesCount);
                observer.observe(featuresContainer, { childList: true, subtree: true });
                updateFeaturesCount();
            }

            // Горячие клавиши для сохранения
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
            });

            // Индикатор загрузки при отправке формы
            const form = document.getElementById('plan-form');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Сохранение...</span>';
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
