@extends('layouts.panel')

@section('title', 'Услуги')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Услуги</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление услугами системы</p>
            </div>
        </div>

        <!-- Список услуг -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Цена</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Длительность</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Бизнес</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($services as $service)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $service->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ number_format($service->price, 0, '.', ' ') }} ₽</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $service->duration }} мин</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $service->business->name ?? '-' }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($services->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-slate-500 dark:text-slate-400">Услуги не найдены</p>
                </div>
            @endif
        </div>

        <!-- Пагинация -->
        @if($services->hasPages())
            <div class="flex justify-center">
                {{ $services->links() }}
            </div>
        @endif
    </div>
@endsection
