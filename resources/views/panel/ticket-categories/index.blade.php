@extends('layouts.panel')

@section('title', 'Категории тикетов')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Категории тикетов</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление категориями для системы тикетов</p>
            </div>
            <a href="{{ route('panel.ticket-categories.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fa-solid fa-plus text-sm"></i>
                Создать категорию
            </a>
        </div>

        <!-- Фильтры -->
        @if(isset($search) && $search)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4">
                <form method="GET" class="flex items-center gap-4">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Поиск по названию..."
                        class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Поиск</button>
                    <a href="{{ route('panel.ticket-categories.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-lg">Сбросить</a>
                </form>
            </div>
        @endif

        <!-- Таблица -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Описание</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тикетов</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</div>
                                    @if($category->sort_order > 0)
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Порядок: {{ $category->sort_order }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $category->description ? Str::limit($category->description, 50) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $category->tickets_count }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                        {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' }}">
                                        {{ $category->is_active ? 'Активна' : 'Неактивна' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('panel.ticket-categories.edit', $category) }}" 
                                       class="inline-flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                        <i class="fa-solid fa-edit text-xs"></i>
                                        Редактировать
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                            <i class="fa-solid fa-tags text-2xl text-slate-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Категории не найдены</h3>
                                        <p class="text-slate-600 dark:text-slate-400 mb-6">Создайте первую категорию для тикетов</p>
                                        <a href="{{ route('panel.ticket-categories.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                            <i class="fa-solid fa-plus"></i>
                                            Создать категорию
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
