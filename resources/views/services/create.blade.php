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
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Основная информация</span>
                </h2>
            </div>
            <div class="p-4 md:p-6 space-y-5">
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Название услуги <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                           autofocus placeholder="Введите название услуги">
                    @error('name')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Описание
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-150 resize-none"
                              placeholder="Опишите услугу...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Параметры услуги -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-cog text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Параметры услуги</span>
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="duration" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Длительность <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="duration" name="duration" required min="15" step="15" value="{{ old('duration', 60) }}"
                                   class="w-full px-3 py-2.5 pr-12 text-sm rounded-lg border {{ $errors->has('duration') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm font-medium">мин</span>
                        </div>
                        @error('duration')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="price" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Цена <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="price" name="price" required min="0" step="0.01" value="{{ old('price') }}"
                                   class="w-full px-3 py-2.5 pr-12 text-sm rounded-lg border {{ $errors->has('price') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm font-medium">Br</span>
                        </div>
                        @error('price')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('services.index') }}"
           class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-150">
            Отмена
        </a>
        <button type="submit"
                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900">
            Создать услугу
        </button>
    </div>
</form>

@endsection

