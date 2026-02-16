@extends('layouts.user')

@section('title', 'Добавление клиента - Cliently')
@section('page-title', 'Новый клиент')
@section('page-description', 'Добавление нового клиента в базу')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => 'Добавление клиента', 'url' => null],
    ]" />
@endpush

@section('content')

    <div class="max-w-2xl mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">
                Новый клиент
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Заполните информацию о новом клиенте
            </p>
        </div>

        <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-6">
            <!-- Карточка основной информации -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Основная информация</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Имя -->
                        <div class="space-y-2">
                            <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Имя <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="first_name" name="first_name" required
                                    value="{{ old('first_name') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('first_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400" autofocus>
                                @if ($errors->has('first_name'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Фамилия -->
                        <div class="space-y-2">
                            <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Фамилия
                            </label>
                            <div class="relative">
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('last_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400">
                                @if ($errors->has('last_name'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Карточка контактной информации -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Контактная информация</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <x-phone-input
                                :countries="$countries"
                                block-id="clientPhoneBlock"
                                :old-phone="old('phone')"
                                :old-country-id="old('phone_country_id')"
                                :old-national="old('phone_national')"
                                :required="true"
                                helper-text="Формат: код страны + номер"
                            />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Email
                            </label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('email') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400">
                                @if ($errors->has('email'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('clients.index') }}"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left text-sm"></i>
                Отмена
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                Создать клиента
            </button>
        </div>
        </form>
    </div>

@endsection

