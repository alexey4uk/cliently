@extends('layouts.panel')

@section('title', 'Редактирование бизнеса')

@section('content')
    <div class="space-y-6">

        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-building text-white text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Редактирование бизнеса</h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Измените информацию о бизнесе</p>
                    </div>
                </div>
                <a href="{{ route('panel.businesses.show', $business) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs sm:text-sm"></i>
                    <span>Назад</span>
                </a>
            </div>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <form method="POST" action="{{ route('panel.businesses.update', $business) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Название -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Название бизнеса <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $business->name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="Введите название бизнеса"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Телефон -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Телефон
                        </label>
                        <input type="tel"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $business->phone) }}"
                               class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('phone')
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
                                  maxlength="500"
                                  class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors resize-none"
                                  placeholder="Краткое описание бизнеса...">{{ old('description', $business->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                            Максимум 500 символов
                        </p>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.businesses.show', $business) }}"
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
