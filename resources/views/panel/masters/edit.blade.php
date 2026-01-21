@extends('layouts.panel')

@section('title', 'Редактирование мастера')

@section('content')
    <div class="space-y-6">
        <!-- Flash сообщения -->
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-3 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm sm:text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
                </div>
                <button @click="show = false"
                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                    <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
                </button>
            </div>
        @endif

        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-user-tie text-white text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Редактирование мастера</h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Измените информацию о мастере</p>
                    </div>
                </div>
                <a href="{{ route('panel.masters.show', $master) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs sm:text-sm"></i>
                    <span>Назад</span>
                </a>
            </div>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <form method="POST" action="{{ route('panel.masters.update', $master) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Бизнес -->
                    <div>
                        <label for="business_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Бизнес <span class="text-rose-500">*</span>
                        </label>
                        <select id="business_id"
                                name="business_id"
                                class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('business_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                required>
                            <option value="">Выберите бизнес</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" {{ old('business_id', $master->business_id) == $business->id ? 'selected' : '' }}>
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Имя -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Имя <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="{{ old('first_name', $master->first_name) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   placeholder="Введите имя"
                                   required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Фамилия -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Фамилия
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                            </label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="{{ old('last_name', $master->last_name) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   placeholder="Введите фамилию">
                            @error('last_name')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Специализация -->
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Специализация <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="specialization"
                               name="specialization"
                               value="{{ old('specialization', $master->specialization) }}"
                               class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="Например: Парикмахер, Массажист"
                               required>
                        @error('specialization')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Описание -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Описание
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors resize-none"
                                  placeholder="Краткое описание мастера...">{{ old('description', $master->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Телефон -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Телефон <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $master->phone) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   placeholder="+375XXXXXXXXX"
                                   required>
                            @error('phone')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Email
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $master->email) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   placeholder="example@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Локации -->
                    @if($locations->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Локации
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                            </label>
                            <div class="space-y-2 max-h-48 overflow-y-auto p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                @foreach($locations as $location)
                                    <label class="flex items-center gap-3 cursor-pointer hover:bg-white dark:hover:bg-slate-800 p-2 rounded transition-colors">
                                        <input type="checkbox"
                                               name="location_ids[]"
                                               value="{{ $location->id }}"
                                               {{ in_array($location->id, old('location_ids', $master->locations->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2">
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $location->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('location_ids')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Услуги -->
                    @if($services->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Услуги
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                            </label>
                            <div class="space-y-2 max-h-48 overflow-y-auto p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                @foreach($services as $service)
                                    <label class="flex items-center gap-3 cursor-pointer hover:bg-white dark:hover:bg-slate-800 p-2 rounded transition-colors">
                                        <input type="checkbox"
                                               name="service_ids[]"
                                               value="{{ $service->id }}"
                                               {{ in_array($service->id, old('service_ids', $master->services->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2">
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $service->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('service_ids')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Статус -->
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $master->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Мастер активен</span>
                        </label>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Активные мастера доступны для записи
                        </p>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.masters.show', $master) }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Сохранить изменения</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
