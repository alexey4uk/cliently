@extends('layouts.user')

@section('title', 'Редактирование клиента - Cliently')
@section('page-title', 'Редактирование клиента')
@section('page-description', 'Изменение данных клиента')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Клиенты', 'url' => route('clients.index')], ['title' => 'Редактирование', 'url' => null]]" />
@endpush

@section('content')

    <div class="max-w-2xl mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">
                Редактирование клиента
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Изменение данных клиента: {{ $client->full_name }}
            </p>
        </div>

        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
        @csrf
        @method('PATCH')

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
                                    value="{{ old('first_name', $client->first_name) }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('first_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400"autofocus>
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
                                <input type="text" id="last_name" name="last_name"
                                    value="{{ old('last_name', $client->last_name) }}"
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
                    @php
                        $phoneCountry = $client->phoneCountry ?? $countries->first();
                        $phoneNational = '';
                        if ($client->phone && $phoneCountry) {
                            $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                            $phoneDig = preg_replace('/\D/', '', $client->phone);
                            $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                        }
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <x-phone-input
                                :countries="$countries"
                                block-id="clientPhoneBlockEdit"
                                :old-phone="old('phone', $client->phone)"
                                :old-country-id="old('phone_country_id', $client->phone_country_code ? \App\Models\Country::where('code', $client->phone_country_code)->value('id') : null)"
                                :old-national="old('phone_national', $phoneNational)"
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
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $client->email) }}"
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
                <i class="fa-solid fa-check text-sm"></i>
                Сохранить изменения
            </button>
        </div>
        </form>
    </div>

@endsection

