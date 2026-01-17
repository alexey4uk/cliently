@extends('layouts.panel')

@section('title', 'Создание права')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-slate-500 dark:text-slate-400">
                <li>
                    <a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Главная
                    </a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li>
                    <a href="{{ route('panel.permissions') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Права доступа
                    </a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="text-slate-900 dark:text-white font-medium">
                    Создание права
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Создание права</h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">Создайте новое право доступа</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="POST" action="{{ route('panel.permissions.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Permission Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Название права *
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Например: reports.view"
                    />
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                        Используйте латинские буквы, цифры и точку, без пробелов
                    </p>
                </div>

                <!-- Permission Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Описание права
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="2"
                        class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Описание права доступа"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Создать право
                    </button>
                    <a
                        href="{{ route('panel.permissions') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium transition-colors">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
