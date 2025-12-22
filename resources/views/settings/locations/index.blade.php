@extends('layouts.user')

@section('title', 'Локации - Cliently')
@section('page-title', 'Локации')
@section('page-description', 'Управление локациями вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Локации', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="space-y-6">
    <!-- Заголовок и кнопка добавления -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                Локации
            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                Управляйте адресами и рабочими часами ваших локаций
            </p>
        </div>
        <a href="{{ route('settings.locations.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить локацию</span>
        </a>
    </div>

    <!-- Таблица локаций -->
    @if($locations->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Название
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden md:table-cell max-w-[200px]">
                                Адрес
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                                Телефон
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden xl:table-cell">
                                Время работы
                            </th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider w-20">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($locations as $location)
                            @php
                                $workingHours = json_decode($location->working_hours, true);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                            {{ $location->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 md:hidden mt-0.5 line-clamp-1">
                                            {{ $location->full_address }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell max-w-[200px]">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 break-words">
                                        {{ $location->full_address }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden lg:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        {{ $location->phone }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden xl:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        @if($workingHours)
                                            @if($workingHours['24_hours'] ?? false)
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-clock text-xs"></i>
                                                    <span>Круглосуточно</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-clock text-xs"></i>
                                                    <span>{{ $workingHours['from'] ?? '—' }} - {{ $workingHours['to'] ?? '—' }}</span>
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('settings.locations.edit', $location) }}"
                                           class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                           title="Редактировать">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('settings.locations.destroy', $location) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить эту локацию?');"
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
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-location-dot text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Нет локаций
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Добавьте первую локацию для вашего бизнеса
                </p>
                <a href="{{ route('settings.locations.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить локацию</span>
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
