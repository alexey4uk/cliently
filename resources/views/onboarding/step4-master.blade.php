@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-baseline justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Добавление мастера</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">Шаг 4 из 4</p>
        </div>

        <!-- Индикатор прогресса -->
        <div class="flex items-center gap-2">
            <div class="flex items-center">
                @for($i = 1; $i <= 4; $i++)
                    <div class="w-2 h-2 rounded-full {{ $i <= 4 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }} {{ $i < 4 ? 'mr-1' : '' }}"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Информационная карточка -->
    <div class="rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-user-check text-slate-600 dark:text-slate-400 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    Добавьте хотя бы одного мастера, который будет оказывать услуги.
                    Вы можете добавить больше мастеров позже в разделе "Мастера".
                </p>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.master.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Имя мастера*</label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Анна Иванова"
                       autofocus>
                @error('name')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="specialization" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Специализация*</label>
                <input type="text" id="specialization" name="specialization" required value="{{ old('specialization') }}"
                       class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Парикмахер, барбер, косметолог">
                @error('specialization')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание (необязательно)</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Опыт работы, образование, достижения...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Телефон*</label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="+7 (999) 123-45-67">
                    @error('phone')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Почта (необязательно)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="anna@example.com">
                    @error('email')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Завершить настройку <i class="fa-solid fa-check ml-2"></i>
            </button>
        </div>
    </form>
@endsection
