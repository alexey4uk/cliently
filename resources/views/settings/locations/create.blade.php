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
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Основная информация</span>
                </h2>
            </div>
            <div class="p-4 md:p-6 space-y-5">
                
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Название локации <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                           autofocus placeholder="Введите название локации">
                    @error('name')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>

                <!-- Адрес -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Адрес <span class="text-rose-500">*</span>
                    </label>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label for="city" class="block text-xs font-medium text-slate-600 dark:text-slate-400">Город <span class="text-rose-500">*</span></label>
                                <input type="text" id="city" name="city" required value="{{ old('city') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('city') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                       placeholder="Минск">
                                @error('city')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label for="street" class="block text-xs font-medium text-slate-600 dark:text-slate-400">Улица <span class="text-rose-500">*</span></label>
                                <input type="text" id="street" name="street" required value="{{ old('street') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('street') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                       placeholder="пр. Независимости">
                                @error('street')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label for="house" class="block text-xs font-medium text-slate-600 dark:text-slate-400">Дом <span class="text-rose-500">*</span></label>
                                <input type="text" id="house" name="house" required value="{{ old('house') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('house') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                       placeholder="1">
                                @error('house')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="building" class="block text-xs font-medium text-slate-600 dark:text-slate-400">Корпус</label>
                                <input type="text" id="building" name="building" value="{{ old('building') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                       placeholder="1">
                            </div>
                            <div class="space-y-1.5">
                                <label for="apartment" class="block text-xs font-medium text-slate-600 dark:text-slate-400">Квартира/Офис</label>
                                <input type="text" id="apartment" name="apartment" value="{{ old('apartment') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                       placeholder="101">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Контактная информация</span>
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="space-y-1.5">
                    <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Телефон <span class="text-rose-500">*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                           placeholder="+375XXXXXXXXX">
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                        Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                    </p>
                    @error('phone')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Дополнительная информация</span>
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Описание
                    </label>
                    <textarea id="description" name="description" rows="4" maxlength="500"
                          class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-150 resize-none"
                          placeholder="Дополнительная информация о локации...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Время работы -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Время работы</span>
                </h2>
            </div>
            <div class="p-4 md:p-6">
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        График работы <span class="text-rose-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <input type="checkbox" id="workingHours24" name="working_hours[24_hours]" value="1"
                                   class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                   {{ old('working_hours.24_hours') ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">Круглосуточно</span>
                        </label>

                        <div id="workingHoursFields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="workingHoursFrom" class="block text-xs font-medium text-slate-600 dark:text-slate-400">С</label>
                                <input type="time" name="working_hours[from]" id="workingHoursFrom"
                                       value="{{ old('working_hours.from', '09:00') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150">
                                @error('working_hours.from')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="workingHoursTo" class="block text-xs font-medium text-slate-600 dark:text-slate-400">До</label>
                                <input type="time" name="working_hours[to]" id="workingHoursTo"
                                       value="{{ old('working_hours.to', '18:00') }}"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150">
                                @error('working_hours.to')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @error('working_hours')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('settings.locations') }}" 
           class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-150">
            Отмена
        </a>
        <button type="submit"
            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900">
            Создать локацию
        </button>
    </div>
</form>

@endsection

