@extends('layouts.user')

@section('title', 'Добавление услуги - Cliently')
@section('page-title', 'Добавление услуги')
@section('page-description', 'Создание новой услуги для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Услуги', 'url' => route('services.index')],
        ['title' => 'Добавление услуги', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('services.store') }}" class="space-y-6">
    @csrf

    <div class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400"></i>
                        Основная информация
                    </h3>
                </div>
            
                <div>
                    <label for="name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Название услуги*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           autofocus>
                    @error('name')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Описание</span>
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Параметры услуги -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-cog text-indigo-600 dark:text-indigo-400"></i>
                        Параметры услуги
                    </h3>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="duration" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Длительность (минуты)*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="duration" name="duration" required min="15" step="15" value="{{ old('duration', 60) }}"
                                   class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('duration') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            <span class="absolute right-2.5 md:right-3 top-1/2 transform -translate-y-1/2 text-slate-500 dark:text-slate-400 text-base md:text-sm">мин</span>
                        </div>
                        @error('duration')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Цена (Br)*</span>
                        </label>
                        <input type="number" id="price" name="price" required min="0" step="0.01" value="{{ old('price') }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('price') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('price')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('services.index') }}"
           class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            Отмена
        </a>
        <button type="submit"
                class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Сохранить
        </button>
    </div>
</form>

@endsection

