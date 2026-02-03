@extends('layouts.user')

@section('title', 'Добавление мастера - Cliently')
@section('page-title', 'Добавление мастера')
@section('page-description', 'Добавьте нового мастера для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => route('settings.masters')],
        ['title' => 'Добавление', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Добавить мастера</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Заполните информацию о новом мастере</p>
    </div>

    <form method="POST" action="{{ route('settings.masters.store') }}">
        @csrf

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
                               value="{{ old('first_name') }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
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
                               value="{{ old('last_name') }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
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
                           value="{{ old('specialization') }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           placeholder="Парикмахер, Массажист">
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
                              class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Контактная информация</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-phone-input
                        :countries="$countries"
                        block-id="masterCreatePhoneBlock"
                        :old-phone="old('phone')"
                        :old-country-id="old('phone_country_id')"
                        :old-national="old('phone_national')"
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
                           value="{{ old('email') }}"
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
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Расписание можно настроить после создания мастера</p>
                </div>
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
                                       {{ in_array($location->id, old('location_ids', [])) ? 'checked' : '' }}
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
                                       {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}
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
                Сохранить мастера
            </button>
        </div>
    </form>
</div>

@endsection

