@extends('layouts.panel')

@section('title', 'Клиенты')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Клиенты</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление клиентами системы</p>
            </div>
        </div>

        <!-- Список клиентов -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Имя</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Бизнес</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Создан</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($clients as $client)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $client->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $client->phone ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $client->business->name ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $client->created_at->format('d.m.Y H:i') }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($clients->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-slate-500 dark:text-slate-400">Клиенты не найдены</p>
                </div>
            @endif
        </div>

        <!-- Пагинация -->
        @if($clients->hasPages())
            <div class="flex justify-center">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
@endsection
