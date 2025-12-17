@extends('layouts.user')
@section('content')
    <!-- Центральный блок -->
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-200px)] text-center px-4">
        <div class="mb-8">
            <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20 mb-6">
                <i class="fa-solid fa-check text-3xl text-emerald-600 dark:text-emerald-300"></i>
            </div>

            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-white mb-3">Настройка завершена!</h1>

            <p class="text-base text-slate-600 dark:text-slate-400 max-w-md mx-auto mb-8">
                Ваш бизнес успешно настроен. Теперь вы можете принимать записи от клиентов.
            </p>
        </div>

        <!-- Карточки настроенных элементов -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 w-full max-w-3xl">
            <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-500/20 mb-4 mx-auto">
                    <i class="fa-solid fa-store text-lg text-indigo-600 dark:text-indigo-300"></i>
                </div>
                <h3 class="font-medium text-slate-900 dark:text-white mb-1">Бизнес</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Основная информация настроена</p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-500/20 mb-4 mx-auto">
                    <i class="fa-solid fa-location-dot text-lg text-indigo-600 dark:text-indigo-300"></i>
                </div>
                <h3 class="font-medium text-slate-900 dark:text-white mb-1">Локация</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ session('onboarding.location_name', 'Не добавлена') }}</p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-500/20 mb-4 mx-auto">
                    <i class="fa-solid fa-scissors text-lg text-indigo-600 dark:text-indigo-300"></i>
                </div>
                <h3 class="font-medium text-slate-900 dark:text-white mb-1">Услуги и мастера</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Добавлены для работы</p>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="space-y-4 w-full max-w-sm">
            <a href="{{ route('dashboard') }}"
               class="block w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Перейти в дашборд
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('services.index') }}"
                   class="flex-1 px-4 py-2 text-center text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                    Услуги
                </a>

                <a href=""
                   class="flex-1 px-4 py-2 text-center text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                    Мастера
                </a>

                <a href=""
                   class="flex-1 px-4 py-2 text-center text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                    Расписание
                </a>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 mt-6">
                Вы всегда можете изменить настройки в разделе "Настройки бизнеса"
            </p>
        </div>
    </div>
@endsection
