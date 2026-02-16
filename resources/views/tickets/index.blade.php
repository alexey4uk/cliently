@extends('layouts.user')

@section('title', 'Тикеты - Cliently')
@section('page-title', 'Тикеты')
@section('page-description', 'Управление тикетами')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Тикеты', 'url' => null]]" />
@endpush

@section('content')

    <div class="max-w-[1400px] mx-auto">
        <div class="space-y-6">
            @if(!$business)
            <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 p-4">
                <p class="text-amber-800 dark:text-amber-200 font-medium">Создайте бизнес или примите приглашение, чтобы создавать обращения.</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <a href="{{ route('settings.businesses.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg">Управление бизнесами</a>
            </div>
            </div>
            @else
            <!-- Строка: заголовок + действие -->
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-lg font-semibold text-slate-900 dark:text-white">Обращения</h1>
                @if($canCreateTickets)
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Создать</span>
                </a>
                @endif
            </div>

        <!-- Поиск и фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
            <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-search mr-2 text-slate-400"></i>Поиск
                        </label>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Поиск по теме или описанию..."
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-filter mr-2 text-slate-400"></i>Статус
                        </label>
                        <select name="status" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">Все статусы</option>
                            <option value="new" {{ ($status ?? '') === 'new' ? 'selected' : '' }}>Новый</option>
                            <option value="open" {{ ($status ?? '') === 'open' ? 'selected' : '' }}>В работе</option>
                            <option value="resolved" {{ ($status ?? '') === 'resolved' ? 'selected' : '' }}>Решен</option>
                            <option value="closed" {{ ($status ?? '') === 'closed' ? 'selected' : '' }}>Закрыт</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-tag mr-2 text-slate-400"></i>Категория
                        </label>
                        <select name="category_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">Все категории</option>
                            @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ ($category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-search mr-1.5"></i>Найти
                    </button>
                    @if(($search ?? '') !== '' || ($status ?? '') !== '' || ($category_id ?? '') !== '')
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Сбросить
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Список тикетов -->
        @if($tickets->count() > 0)
            <div class="grid gap-4">
                @foreach($tickets as $ticket)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">#{{ $ticket->id }}</span>
                                    <a href="{{ route('tickets.show', $ticket->id) }}" 
                                       class="text-lg font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        {{ $ticket->title }}
                                    </a>
                                </div>
                                
                                <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400 mb-3">
                                    @if($ticket->category)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-tag text-xs"></i>
                                            {{ $ticket->category->name }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                        {{ $ticket->created_at->format('d.m.Y H:i') }}
                                    </span>
                                    @if($ticket->comments_count > 0)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-comments text-xs"></i>
                                            {{ $ticket->comments_count }} {{ $ticket->comments_count === 1 ? 'комментарий' : 'комментариев' }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
                                    {{ Str::limit($ticket->description, 150) }}
                                </p>
                            </div>

                            <div class="ml-4 flex flex-col items-end gap-3">
                                <span class="px-3 py-1.5 text-xs font-medium rounded-full whitespace-nowrap
                                    {{ $ticket->status === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' : '' }}
                                    {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : '' }}
                                    {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : '' }}
                                    {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' : '' }}">
                                    {{ $ticket->status === 'new' ? 'Новый' : ($ticket->status === 'open' ? 'В работе' : ($ticket->status === 'resolved' ? 'Решен' : 'Закрыт')) }}
                                </span>
                                
                                <a href="{{ route('tickets.show', $ticket->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    Открыть
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if($tickets->hasPages())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4">
                    {{ $tickets->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <i class="fa-solid fa-ticket text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Тикеты не найдены</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                        @if(($search ?? '') !== '' || ($status ?? '') !== '' || ($category_id ?? '') !== '')
                            По вашему запросу ничего не найдено. Попробуйте изменить параметры или сбросить фильтры.
                        @else
                            Создайте свой первый тикет для обращения в поддержку
                        @endif
                    </p>
                    @if(($search ?? '') !== '' || ($status ?? '') !== '' || ($category_id ?? '') !== '')
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors mr-2">
                            Сбросить фильтры
                        </a>
                    @endif
                    @if(($search ?? '') === '' && ($status ?? '') === '' && ($category_id ?? '') === '')
                        <a href="{{ route('tickets.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            Создать тикет
                        </a>
                    @endif
                </div>
            </div>
        @endif
        @endif
        </div>
    </div>
@endsection
