@extends('layouts.panel')

@section('title', 'Аналитика')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Аналитика</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Статистика и показатели системы</p>
            </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего записей</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalAppointments }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего клиентов</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalClients }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего пользователей</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalUsers }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-users-gear text-amber-600 dark:text-amber-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Последние записи -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние записи</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Клиент</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Услуга</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Мастер</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentAppointments as $appointment)
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recentAppointments->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-slate-500 dark:text-slate-400">Записи не найдены</p>
                </div>
            @endif
        </div>
    </div>
@endsection
