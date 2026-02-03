@extends('layouts.panel')

@section('title', 'Редактирование клиента')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Редактирование клиента</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Измените данные клиента</p>
            </div>
            <a href="{{ route('panel.clients') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('panel.clients.update', $client) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Бизнес -->
                    <div>
                        <label for="business_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Бизнес</label>
                        <select id="business_id"
                                name="business_id"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                required>
                            <option value="">Выберите бизнес</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" {{ old('business_id', $client->business_id) == $business->id ? 'selected' : '' }}>
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Имя -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Имя</label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               value="{{ old('first_name', $client->first_name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Фамилия -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Фамилия</label>
                        <input type="text"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name', $client->last_name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('last_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $phoneCountry = $client->primaryPhone?->country ?? $countries->first();
                        $phoneNational = '';
                        if ($client->primaryPhone && $phoneCountry) {
                            $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                            $phoneDig = preg_replace('/\D/', '', $client->primaryPhone->phone);
                            $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                        }
                    @endphp
                    <x-phone-input
                        :countries="$countries"
                        block-id="panelClientEditPhoneBlock"
                        :old-phone="old('phone', $client->phone)"
                        :old-country-id="old('phone_country_id', $client->primaryPhone?->country_id)"
                        :old-national="old('phone_national', $phoneNational)"
                        :required="true"
                        helper-text=""
                    />

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $client->email) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('email')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.clients') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-sm hover:shadow-md">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection