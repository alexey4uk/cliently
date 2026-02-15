@extends('layouts.panel')

@section('title', 'Тикеты')

@section('content')
    <div class="max-w-[1400px] mx-auto">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Управление тикетами</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Просмотр и управление всеми тикетами системы</p>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-search mr-2 text-slate-400"></i>Поиск
                        </label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Поиск по теме..."
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-filter mr-2 text-slate-400"></i>Статус
                        </label>
                        <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Все статусы</option>
                            <option value="new" {{ $statusFilter === 'new' ? 'selected' : '' }}>Новый</option>
                            <option value="open" {{ $statusFilter === 'open' ? 'selected' : '' }}>В работе</option>
                            <option value="resolved" {{ $statusFilter === 'resolved' ? 'selected' : '' }}>Решен</option>
                            <option value="closed" {{ $statusFilter === 'closed' ? 'selected' : '' }}>Закрыт</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-building mr-2 text-slate-400"></i>Бизнес
                        </label>
                        <select name="business_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Все бизнесы</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" {{ $businessFilter == $business->id ? 'selected' : '' }}>{{ $business->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-user mr-2 text-slate-400"></i>Назначен
                        </label>
                        <select name="assigned_to" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Все</option>
                            <option value="unassigned" {{ $assignedFilter === 'unassigned' ? 'selected' : '' }}>Не назначен</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $assignedFilter == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fa-solid fa-filter mr-2"></i>Применить
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Таблица -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тема</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Бизнес</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Назначен</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Дата</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                    #{{ $ticket->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('panel.tickets.show', $ticket) }}" 
                                       class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                        {{ Str::limit($ticket->title, 50) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                    {{ $ticket->business?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                        {{ $ticket->status === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' : '' }}
                                        {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : '' }}
                                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : '' }}
                                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' : '' }}">
                                        {{ $ticket->status === 'new' ? 'Новый' : ($ticket->status === 'open' ? 'В работе' : ($ticket->status === 'resolved' ? 'Решен' : 'Закрыт')) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                    {{ $ticket->assignedUser?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    {{ $ticket->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('panel.tickets.show', $ticket) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-colors"
                                           title="Просмотр">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                            <span class="hidden sm:inline">Просмотр</span>
                                        </a>
                                        
                                        @can('panel.tickets.update')
                                        <a href="{{ route('panel.tickets.edit', $ticket) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors"
                                           title="Редактировать">
                                            <i class="fa-solid fa-edit text-xs"></i>
                                            <span class="hidden sm:inline">Редактировать</span>
                                        </a>
                                        @endcan
                                        
                                        @can('panel.tickets.delete')
                                        <form method="POST" action="{{ route('panel.tickets.destroy', $ticket) }}" 
                                              class="inline-block"
                                              onsubmit="return confirm('Вы уверены, что хотите удалить этот тикет?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors"
                                                    title="Удалить">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                                <span class="hidden sm:inline">Удалить</span>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                            <i class="fa-solid fa-ticket text-2xl text-slate-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Тикеты не найдены</h3>
                                        <p class="text-slate-600 dark:text-slate-400">Попробуйте изменить параметры фильтрации</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
        </div>
    </div>
@endsection
