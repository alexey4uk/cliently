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

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Добавить услугу</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Заполните информацию о новой услуге</p>
    </div>

    <form method="POST" action="{{ route('services.store') }}" class="space-y-6">
        @csrf

        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Название <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Описание
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              maxlength="1000"
                              class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors">{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Максимум 1000 символов
                    </p>
                    @error('description')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Цена и длительность -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Цена и длительность</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Цена (BYN) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           required 
                           min="0" 
                           step="0.01"
                           value="{{ old('price') }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('price') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                    @error('price')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Длительность (мин) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           id="duration"
                           name="duration"
                           required
                           min="1"
                           value="{{ old('duration', 60) }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('duration') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                    @error('duration')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Мастера -->
        @php
            $masters = $business->masters ?? collect();
        @endphp
        @if($masters->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Мастера</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Выберите мастеров, которые могут оказывать эту услугу</p>
            
            <div class="space-y-3">
                @foreach($masters as $master)
                    <label class="flex items-center p-3 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" 
                               name="masters[]" 
                               value="{{ $master->id }}"
                               {{ in_array($master->id, old('masters', [])) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($master->name) }}&background=6366f1&color=fff" 
                             class="w-8 h-8 rounded-full ml-3" 
                             alt="{{ $master->name }}">
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-white">{{ $master->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Настройки -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Настройки</h2>
            
            <div class="space-y-4">
                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Активна</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Услуга будет доступна для записи</p>
                    </div>
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500">
                </label>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('services.index') }}" 
               class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                Сохранить услугу
            </button>
        </div>
    </form>
</div>

@endsection
