@extends('layouts.panel')

@section('title', 'Записи')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Записи</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Просмотр всех записей системы</p>
            </div>
        </div>

        <!-- Список записей -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Клиент</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Услуга</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Мастер</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Дата и время</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($appointments as $appointment)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->client->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $appointment->service->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $appointment->master->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $appointment->start_time->format('d.m.Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium 
                                        @if($appointment->status === 'confirmed') bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400
                                        @elseif($appointment->status === 'pending') bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400
                                        @elseif($appointment->status === 'cancelled') bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400
                                        @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 @endif rounded-full">
                                        {{ $appointment->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($appointments->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-slate-500 dark:text-slate-400">Записи не найдены</p>
                </div>
            @endif
        </div>

        <!-- Пагинация -->
        @if($appointments->hasPages())
            <div class="flex justify-center">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
@endsection
