@extends('layouts.panel')

@section('title', 'Создать категорию')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Создать категорию</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Добавьте новую категорию для тикетов</p>
            </div>
            <a href="{{ route('panel.ticket-categories.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                Назад
            </a>
        </div>

        <form method="POST" action="{{ route('panel.ticket-categories.store') }}">
            @csrf
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Название <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        placeholder="Введите название категории"
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Описание
                    </label>
                    <textarea name="description" rows="4" 
                        placeholder="Введите описание категории (необязательно)"
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Порядок сортировки
                        </label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <p class="mt-1 text-xs text-slate-500">Чем меньше число, тем выше в списке</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Активна</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-save"></i>
                        Создать категорию
                    </button>
                    <a href="{{ route('panel.ticket-categories.index') }}" 
                        class="px-6 py-3 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg font-medium transition-colors">
                        Отмена
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
