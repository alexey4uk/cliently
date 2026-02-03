@extends('layouts.panel')

@section('title', 'Добавить страну')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 sm:pb-8">
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
                    <a href="{{ route('panel.countries.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Страны</a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0 text-slate-900 dark:text-white font-medium">Добавить</li>
            </ol>
        </nav>

        <div class="mb-6 sm:mb-8">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-plus text-white text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1">Добавить страну</h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Заполните поля для новой страны</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('panel.countries.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Основные данные</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Коды, названия, телефонный код</p>
                </div>
                <div class="p-4 sm:p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="code" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Код (2 буквы) <span class="text-rose-500">*</span></label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}" required maxlength="2" pattern="[A-Za-z]{2}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('code') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm uppercase"
                                placeholder="BY">
                            @error('code')
                                <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="code_3" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Код ISO 3 (опц.)</label>
                            <input type="text" id="code_3" name="code_3" value="{{ old('code_3') }}" maxlength="3" pattern="[A-Za-z]{3}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('code_3') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm uppercase"
                                placeholder="BLR">
                            @error('code_3')
                                <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Название <span class="text-rose-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('name') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm"
                                placeholder="Беларусь">
                            @error('name')
                                <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="name_en" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Название (англ.)</label>
                            <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('name_en') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm"
                                placeholder="Belarus">
                            @error('name_en')
                                <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="calling_code" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Телефонный код <span class="text-rose-500">*</span></label>
                        <input type="text" id="calling_code" name="calling_code" value="{{ old('calling_code') }}" required
                            class="w-full max-w-xs px-4 py-3 rounded-lg border {{ $errors->has('calling_code') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm"
                            placeholder="+375">
                        @error('calling_code')
                            <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="currency" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Валюта</label>
                            <input type="text" id="currency" name="currency" value="{{ old('currency') }}" maxlength="10"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm"
                                placeholder="BYN">
                            @error('currency')
                                <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="currency_symbol" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Символ валюты</label>
                            <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol') }}" maxlength="10"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm"
                                placeholder="Br">
                            @error('currency_symbol')
                                <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="ioc" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">IOC (3 буквы, опц.)</label>
                        <input type="text" id="ioc" name="ioc" value="{{ old('ioc') }}" maxlength="3"
                            class="w-full max-w-xs px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm uppercase"
                            placeholder="BLR">
                        @error('ioc')
                            <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Отображение и использование</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">В селекте телефона — только страны с включённым флагом</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Активна</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_for_phone_select" value="0">
                        <input type="checkbox" name="is_for_phone_select" value="1" {{ old('is_for_phone_select') ? 'checked' : '' }}
                            class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Показывать в селекте телефона</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-check text-sm"></i>
                    Сохранить
                </button>
                <a href="{{ route('panel.countries.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Отмена
                </a>
            </div>
        </form>
    </div>
@endsection
