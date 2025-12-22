@extends('layouts.user')

@section('title', 'Клиенты - Cliently')
@section('page-title', 'Клиенты')
@section('page-description', 'Ваша клиентская база')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="space-y-6">
    <!-- Заголовок и кнопка добавления -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                Клиенты
            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                Управляйте клиентской базой вашего бизнеса
            </p>
        </div>
        <a href="{{ route('clients.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить клиента</span>
        </a>
    </div>

    <!-- Поиск и сортировка -->
    <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col sm:flex-row gap-3">
        <!-- Поиск -->
        <div class="flex-1 relative">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-slate-400"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Поиск по имени, телефону или email..."
                    class="pl-10 pr-4 py-2.5 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-200 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-sm"
                >
            </div>
        </div>

        <!-- Сортировка -->
        <div class="sm:w-48">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-sort text-slate-400"></i>
                </div>
                <select name="sort" onchange="updateSortDirection(this); this.form.submit()"
                    class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-200 text-slate-900 dark:text-white text-sm appearance-none">
                    <option value="name" data-direction="asc" {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По имени (А-Я)</option>
                    <option value="name" data-direction="desc" {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По имени (Я-А)</option>
                    <option value="created_at" data-direction="desc" {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате</option>
                </select>
                <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400"></i>
                </div>
            </div>
        </div>
    </form>

    <!-- Таблица клиентов -->
    @if($clients->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Клиент
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden md:table-cell">
                                Телефон
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                                Email
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden xl:table-cell">
                                Дата добавления
                            </th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider w-20">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($clients as $client)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Аватар -->
                                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                            {{ $client->initials }}
                                        </div>
                                        <!-- Информация -->
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('clients.show', $client) }}" class="block">
                                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                                    {{ $client->full_name }}
                                                </div>
                                            </a>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 md:hidden mt-0.5 line-clamp-1">
                                                {{ $client->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1 whitespace-nowrap">
                                        <i class="fa-solid fa-phone text-xs text-slate-400"></i>
                                        <span>{{ $client->phone }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden lg:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1">
                                        @if($client->email)
                                            <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                                            <span class="truncate max-w-[200px]">{{ $client->email }}</span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden xl:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        {{ $client->created_at->format('d.m.Y') }}
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('clients.show', $client) }}"
                                           class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                           title="Просмотр">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}"
                                           class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                           title="Редактировать">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить этого клиента?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                    title="Удалить">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            @if($clients->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Показано <span class="font-medium text-slate-900 dark:text-white">{{ $clients->firstItem() }}</span> - 
                            <span class="font-medium text-slate-900 dark:text-white">{{ $clients->lastItem() }}</span> из 
                            <span class="font-medium text-slate-900 dark:text-white">{{ $clients->total() }}</span> клиентов
                        </div>

                        <div class="flex items-center space-x-1">
                            @if($clients->onFirstPage())
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                            @else
                                <a href="{{ $clients->previousPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            @foreach($clients->getUrlRange(1, min(5, $clients->lastPage())) as $page => $url)
                                @if($page == $clients->currentPage())
                                    <button disabled
                                        class="w-8 h-8 flex items-center justify-center bg-indigo-600 text-white rounded-lg font-medium cursor-default text-sm">
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 text-sm">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if($clients->hasMorePages())
                                <a href="{{ $clients->nextPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-users text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    @if($search)
                        Клиенты не найдены
                    @else
                        Нет клиентов
                    @endif
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    @if($search)
                        Попробуйте изменить параметры поиска
                    @else
                        Добавьте первого клиента для вашего бизнеса
                    @endif
                </p>
                @if(!$search)
                    <a href="{{ route('clients.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Добавить клиента</span>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    function updateSortDirection(select) {
        const selectedOption = select.options[select.selectedIndex];
        const direction = selectedOption.getAttribute('data-direction');
        document.getElementById('sort-direction').value = direction;
    }
</script>

@endsection
