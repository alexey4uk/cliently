@extends('layouts.user')

@section('title', 'Редактирование локации - Cliently')
@section('page-title', 'Редактирование локации')
@section('page-description', 'Измените данные локации')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Локации', 'url' => route('settings.locations')],
        ['title' => 'Редактирование', 'url' => null]
    ]" />
@endpush

@section('content')

@php
    $workingHours = json_decode($location->working_hours, true) ?? [];
@endphp

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('settings.locations.update', $location) }}" 
          x-data="{
              is24Hours: {{ old('working_hours.24_hours', $workingHours['24_hours'] ?? false) ? 'true' : 'false' }},
              toggle24Hours() {
                  this.is24Hours = !this.is24Hours;
              },
          }"
          @input="updatePreview()"
          x-init="updatePreview()">
        @csrf
        @method('PATCH')

        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Название -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Название локации <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           value="{{ old('name', $location->name) }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           placeholder="Например: Салон на Пушкинской"
                           autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Адрес -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Адрес</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">

                    <div class="space-y-4">
                        <!-- Первая строка: Город, Улица, Дом -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                                    Город <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="city" 
                                       name="city" 
                                       required 
                                       value="{{ old('city', $location->city) }}"
                                       @input="updatePreview()"
                                       class="w-full px-4 py-2.5 text-sm border {{ $errors->has('city') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                                       placeholder="Минск">
                                @error('city')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="street" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                                    Улица <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="street" 
                                       name="street" 
                                       required 
                                       value="{{ old('street', $location->street) }}"
                                       @input="updatePreview()"
                                       class="w-full px-4 py-2.5 text-sm border {{ $errors->has('street') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                                       placeholder="пр. Независимости">
                                @error('street')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="house" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                                    Дом <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="house" 
                                       name="house" 
                                       required 
                                       value="{{ old('house', $location->house) }}"
                                       @input="updatePreview()"
                                       class="w-full px-4 py-2.5 text-sm border {{ $errors->has('house') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                                       placeholder="1">
                                @error('house')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Вторая строка: Корпус, Квартира/Офис -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="building" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                                    Корпус
                                </label>
                                <input type="text" 
                                       id="building" 
                                       name="building" 
                                       value="{{ old('building', $location->building) }}"
                                       @input="updatePreview()"
                                       class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                                       placeholder="1">
                            </div>
                            <div>
                                <label for="apartment" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                                    Квартира/Офис
                                </label>
                                <input type="text" 
                                       id="apartment" 
                                       name="apartment" 
                                       value="{{ old('apartment', $location->apartment) }}"
                                       @input="updatePreview()"
                                       class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                                       placeholder="101">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        @php
            $phoneCountry = $location->primaryPhone?->country ?? $countries->first();
            $phoneNational = '';
            if ($location->primaryPhone && $phoneCountry) {
                $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                $phoneDig = preg_replace('/\D/', '', $location->primaryPhone->phone);
                $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
            }
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Контактная информация</h2>
            <x-phone-input
                :countries="$countries"
                block-id="locationEditPhoneBlock"
                :old-phone="old('phone', $location->phone)"
                :old-country-id="old('phone_country_id', $location->primaryPhone?->country_id)"
                :old-national="old('phone_national', $phoneNational)"
                :required="true"
                placeholder="29 123 45 67"
                helper-text="Формат: код страны + номер"
            />
        </div>

        <!-- Время работы -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Время работы</h2>
            <div class="space-y-4">
                <!-- Круглосуточно -->
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                    <input type="checkbox" 
                           name="working_hours[24_hours]" 
                           value="1"
                           x-model="is24Hours"
                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                    <div class="flex-1">
                        <span class="text-sm font-medium text-slate-900 dark:text-white">Круглосуточно</span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Работает 24 часа в сутки</p>
                    </div>
                </label>

                <!-- Время работы -->
                <div x-show="!is24Hours" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="workingHoursFrom" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            С <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" 
                               name="working_hours[from]" 
                               id="workingHoursFrom"
                               value="{{ old('working_hours.from', $workingHours['from'] ?? '09:00') }}"
                               :required="!is24Hours"
                               class="w-full px-4 py-2.5 border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                        @error('working_hours.from')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="workingHoursTo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            До <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" 
                               name="working_hours[to]" 
                               id="workingHoursTo"
                               value="{{ old('working_hours.to', $workingHours['to'] ?? '18:00') }}"
                               :required="!is24Hours"
                               class="w-full px-4 py-2.5 border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                        @error('working_hours.to')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @error('working_hours')
                    <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Дополнительная информация</h2>
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Описание
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          maxlength="500"
                          class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors"
                          placeholder="Дополнительная информация о локации (как добраться, особенности и т.д.)">{{ old('description', $location->description) }}</textarea>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Максимум 500 символов
                </p>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
            <a href="{{ route('settings.locations') }}"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fa-solid fa-check text-sm"></i>
                <span>Сохранить изменения</span>
            </button>
        </div>
    </form>
</div>

@endsection

