@extends('layouts.user')

@section('title', 'Редактирование мастера - Cliently')
@section('page-title', 'Редактирование мастера')
@section('page-description', 'Измените данные мастера')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => route('settings.masters')],
        ['title' => 'Редактирование', 'url' => null]
    ]" />
@endpush

@section('content')

@php
    $workingHours = json_decode($master->working_hours, true) ?? [];
    $selectedLocationIds = $master->locations->pluck('id')->toArray();
    $selectedServiceIds = $master->services->pluck('id')->toArray();
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Редактировать мастера</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Измените информацию о мастере</p>
    </div>

    <form method="POST" action="{{ route('settings.masters.update', $master) }}"
          x-data="{
              is24Hours: {{ old('working_hours.24_hours', $workingHours['24_hours'] ?? false) ? 'true' : 'false' }},
              toggle24Hours() {
                  this.is24Hours = !this.is24Hours;
              },
              openDaysOff: false,
              selectedDays: new Set({{ json_encode(old('working_hours.days_off', $workingHours['days_off'] ?? []), JSON_HEX_TAG) }}),
              days: {
                  'monday': 'Понедельник',
                  'tuesday': 'Вторник',
                  'wednesday': 'Среда',
                  'thursday': 'Четверг',
                  'friday': 'Пятница',
                  'saturday': 'Суббота',
                  'sunday': 'Воскресенье'
              },
              toggleDay(day) {
                  if (this.selectedDays.has(day)) {
                      this.selectedDays.delete(day);
                  } else {
                      this.selectedDays.add(day);
                  }
              },
              removeDay(day) {
                  this.selectedDays.delete(day);
              }
          }"
          x-init="
              const oldDays = {{ json_encode(old('working_hours.days_off', $workingHours['days_off'] ?? []), JSON_HEX_TAG) }};
              oldDays.forEach(day => selectedDays.add(day));
          ">
        @csrf
        @method('PATCH')

        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Имя <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               required
                               value="{{ old('first_name', $master->first_name) }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                               placeholder="Введите имя"
                               autofocus>
                        @error('first_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Фамилия
                        </label>
                        <input type="text"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name', $master->last_name) }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                               placeholder="Введите фамилию">
                        @error('last_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="specialization" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Специализация <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="specialization"
                           name="specialization"
                           required
                           value="{{ old('specialization', $master->specialization) }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           placeholder="Например: Парикмахер, Массажист">
                    @error('specialization')
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
                              class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors"
                              placeholder="Расскажите о мастере...">{{ old('description', $master->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Контактная информация</h2>

            @php
                $phoneCountry = $master->primaryPhone?->country ?? $countries->first();
                $phoneNational = '';
                if ($master->primaryPhone && $phoneCountry) {
                    $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                    $phoneDig = preg_replace('/\D/', '', $master->primaryPhone->phone);
                    $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-phone-input
                        :countries="$countries"
                        block-id="masterEditPhoneBlock"
                        :old-phone="old('phone', $master->phone)"
                        :old-country-id="old('phone_country_id', $master->primaryPhone?->country_id)"
                        :old-national="old('phone_national', $phoneNational)"
                        :required="true"
                        helper-text="Формат: код страны + номер"
                    />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $master->email) }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           placeholder="example@mail.com">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Расписание -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Расписание мастера</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управление рабочим временем, перерывами и исключениями</p>
                </div>
                <a href="{{ route('settings.masters.schedule.edit', $master) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                    <i class="fa-solid fa-calendar"></i>
                    <span>Настроить расписание</span>
                </a>
            </div>
        </div>

        <!-- Локации и услуги -->
        @if($locations->count() > 0 || $services->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6"
             x-data="{
                 locationCheckboxes: null,
                 serviceCheckboxes: null,
                 selectAllLocations() { (this.locationCheckboxes || document.querySelectorAll('input[name=\'location_ids[]\']')).forEach(c => c.checked = true); },
                 clearAllLocations() { (this.locationCheckboxes || document.querySelectorAll('input[name=\'location_ids[]\']')).forEach(c => c.checked = false); },
                 selectAllServices() { (this.serviceCheckboxes || document.querySelectorAll('input[name=\'service_ids[]\']')).forEach(c => c.checked = true); },
                 clearAllServices() { (this.serviceCheckboxes || document.querySelectorAll('input[name=\'service_ids[]\']')).forEach(c => c.checked = false); }
             }"
             x-init="locationCheckboxes = document.querySelectorAll('input[name=\'location_ids[]\']'); serviceCheckboxes = document.querySelectorAll('input[name=\'service_ids[]\']');">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Связи</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Укажите, в каких локациях работает мастер и какие услуги оказывает</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                @if($locations->count() > 0)
                <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 border-l-4 border-l-indigo-500 dark:border-l-indigo-600 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-location-dot text-indigo-500 dark:text-indigo-400 mr-1.5 text-xs"></i>Локации
                        </label>
                        <div class="flex gap-2 text-xs">
                            <button type="button" @click="selectAllLocations()" class="text-indigo-600 dark:text-indigo-400 hover:underline">Всё</button>
                            <span class="text-slate-400">·</span>
                            <button type="button" @click="clearAllLocations()" class="text-slate-500 dark:text-slate-400 hover:underline">Снять</button>
                        </div>
                    </div>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($locations as $location)
                            <label class="flex items-center gap-2.5 py-2 px-2.5 rounded-md hover:bg-white dark:hover:bg-slate-800/80 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="location_ids[]"
                                       value="{{ $location->id }}"
                                       {{ in_array($location->id, old('location_ids', $selectedLocationIds)) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-2 focus:ring-indigo-500 shrink-0">
                                <span class="text-sm text-slate-900 dark:text-white truncate">{{ $location->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($services->count() > 0)
                <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 border-l-4 border-l-emerald-500 dark:border-l-emerald-600 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-scissors text-emerald-500 dark:text-emerald-400 mr-1.5 text-xs"></i>Услуги
                        </label>
                        <div class="flex gap-2 text-xs">
                            <button type="button" @click="selectAllServices()" class="text-indigo-600 dark:text-indigo-400 hover:underline">Всё</button>
                            <span class="text-slate-400">·</span>
                            <button type="button" @click="clearAllServices()" class="text-slate-500 dark:text-slate-400 hover:underline">Снять</button>
                        </div>
                    </div>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($services as $service)
                            <label class="flex items-center gap-2.5 py-2 px-2.5 rounded-md hover:bg-white dark:hover:bg-slate-800/80 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="service_ids[]"
                                       value="{{ $service->id }}"
                                       {{ in_array($service->id, old('service_ids', $selectedServiceIds)) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-2 focus:ring-indigo-500 shrink-0">
                                <span class="text-sm text-slate-900 dark:text-white truncate">{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('settings.masters') }}"
               class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                Сохранить изменения
            </button>
        </div>
    </form>
</div>

@endsection

