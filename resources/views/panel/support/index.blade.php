@extends('layouts.panel')

@section('title', 'Поддержка')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Поддержка</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление тикетами поддержки</p>
            </div>
            <a href="{{ route('panel.tickets') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Все тикеты
            </a>
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Поиск..."
                        class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    <select name="status" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Все статусы</option>
                        <option value="new" {{ $statusFilter === 'new' ? 'selected' : '' }}>Новый</option>
                        <option value="in_progress" {{ $statusFilter === 'in_progress' ? 'selected' : '' }}>В работе</option>
                        <option value="resolved" {{ $statusFilter === 'resolved' ? 'selected' : '' }}>Решен</option>
                        <option value="closed" {{ $statusFilter === 'closed' ? 'selected' : '' }}>Закрыт</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Применить</button>
                </div>
            </form>
        </div>

        <!-- Список тикетов -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Тема</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Бизнес</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Комментариев</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                                <td class="px-6 py-4">#{{ $ticket->id }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('panel.tickets.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $ticket->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">{{ $ticket->business->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $ticket->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $ticket->status === 'new' ? 'Новый' : ($ticket->status === 'in_progress' ? 'В работе' : ($ticket->status === 'resolved' ? 'Решен' : 'Закрыт')) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $ticket->comments_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('panel.tickets.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-800">Просмотр</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-slate-500">Тикеты не найдены</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
@endsection
