@extends('layouts.panel')

@section('title', 'Редактировать тикет')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Редактировать тикет #{{ $ticket->id }}</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Измените информацию о тикете</p>
            </div>
            <a href="{{ route('panel.tickets.show', $ticket) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                Назад
            </a>
        </div>

        <form method="POST" action="{{ route('panel.tickets.update', $ticket) }}">
            @csrf
            @method('PATCH')
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Тема</label>
                    <input type="text" name="title" value="{{ $ticket->title }}" required 
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Описание</label>
                    <textarea name="description" rows="8" required 
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none">{{ $ticket->description }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-tag mr-2 text-slate-400"></i>Категория
                        </label>
                        <select name="category_id" 
                            class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $ticket->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-info-circle mr-2 text-slate-400"></i>Статус
                        </label>
                        <select name="status" 
                            class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="new" {{ $ticket->status === 'new' ? 'selected' : '' }}>Новый</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>В работе</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Решен</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Закрыт</option>
                        </select>
                    </div>
                </div>

                @can('panel.tickets.assign')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-user mr-2 text-slate-400"></i>Назначен
                    </label>
                    <select name="assigned_to" 
                        class="w-full px-4 py-3 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Не назначен</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endcan

                <div class="flex items-center gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-save"></i>
                        Сохранить изменения
                    </button>
                    <a href="{{ route('panel.tickets.show', $ticket) }}" 
                        class="px-6 py-3 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg font-medium transition-colors">
                        Отмена
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
