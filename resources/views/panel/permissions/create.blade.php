@extends('layouts.panel')

@section('title', 'Создание права')

@push('breadcrumbs')
    <x-breadcrumbs :base="['title' => 'Главная', 'url' => route('panel.index')]" :items="[['title' => 'Роли и доступы', 'url' => null], ['title' => 'Права доступа', 'url' => route('panel.permissions')], ['title' => 'Создать право', 'url' => null]]" />
@endpush

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-key text-white text-base sm:text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Создание права</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">Добавьте новое право доступа в систему</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <form method="POST" action="{{ route('panel.permissions.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Название права <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-tag text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               placeholder="Например: panel.reports.view"
                               class="w-full pl-10 sm:pl-11 pr-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-colors" />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Латиница, цифры и точка (например: panel.analytics.view)</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Описание</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 sm:left-4 flex items-start pointer-events-none">
                            <i class="fa-solid fa-file-lines text-slate-400 text-sm mt-0.5"></i>
                        </div>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Краткое описание на русском для поиска и понимания"
                                  class="w-full pl-10 sm:pl-11 pr-4 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-colors resize-none">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('panel.permissions') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-sm"></i>
                        Создать право
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
