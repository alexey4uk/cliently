@extends('layouts.user')

@section('title', 'Добавление локации - Cliently')
@section('page-title', 'Добавление локации')
@section('page-description', 'Добавьте новую локацию для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Локации', 'url' => route('settings.locations')],
        ['title' => 'Добавление', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('settings.locations.store') }}" class="space-y-6">
    @csrf

    <div class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400"></i>
                        Основная информация
                    </h3>
                </div>
                
                <div>
                    <label for="name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Название локации*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           autofocus>
                    @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Адрес -->
                <div>
                    <label class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Адрес*</span>
                    </label>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Город*</label>
                                <input type="text" id="city" name="city" required value="{{ old('city') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('city') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('city')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="street" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Улица*</label>
                                <input type="text" id="street" name="street" required value="{{ old('street') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('street') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('street')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="house" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Дом*</label>
                                <input type="text" id="house" name="house" required value="{{ old('house') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('house') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('house')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="building" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Корпус</label>
                                <input type="text" id="building" name="building" value="{{ old('building') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            </div>
                            <div>
                                <label for="apartment" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Квартира/Офис</label>
                                <input type="text" id="apartment" name="apartment" value="{{ old('apartment') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                        Контактная информация
                    </h3>
                </div>
                
                <div>
                    <label for="phone" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Телефон*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="+375 (29) 123-45-67">
                    <p class="mt-2.5 text-xs text-slate-500 dark:text-slate-400">
                        Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                    </p>
                    @error('phone')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400"></i>
                        Дополнительная информация
                    </h3>
                </div>
                
                <div>
                    <label for="description" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Описание</span>
                    </label>
                    <textarea id="description" name="description" rows="3" maxlength="500"
                          class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Время работы -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                        Время работы
                    </h3>
                </div>
                
                <div>
                    <label class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>График работы*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="workingHours24" name="working_hours[24_hours]" value="1"
                                   class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                   {{ old('working_hours.24_hours') ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">Круглосуточно</span>
                        </label>

                        <div id="workingHoursFields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="workingHoursFrom" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">С</label>
                                <input type="time" name="working_hours[from]" id="workingHoursFrom"
                                       value="{{ old('working_hours.from', '09:00') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('working_hours.from')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="workingHoursTo" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">До</label>
                                <input type="time" name="working_hours[to]" id="workingHoursTo"
                                       value="{{ old('working_hours.to', '18:00') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('working_hours.to')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @error('working_hours')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('settings.locations') }}" 
           class="px-4 py-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            Отмена
        </a>
        <button type="submit"
            class="px-4 py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Сохранить
        </button>
    </div>
</form>

@endsection

