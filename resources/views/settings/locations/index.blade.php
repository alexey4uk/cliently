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
    <!-- Заголовок страницы -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Локации
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Управление адресами и рабочими часами ваших локаций
            </p>
        </div>
        <a href="{{ route('settings.locations.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить локацию</span>
        </a>
    </div>

    <!-- Таблица локаций -->
    @if($locations->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Название
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden md:table-cell max-w-[200px]">
                                Адрес
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                                Телефон
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden xl:table-cell">
                                Время работы
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider w-24">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                        @foreach($locations as $location)
                            @php
                                $workingHours = json_decode($location->working_hours, true);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-4 py-3.5">
                                    <div class="space-y-0.5">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                            {{ $location->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 md:hidden truncate">
                                            {{ $location->full_address }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell max-w-[200px]">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 break-words">
                                        {{ $location->full_address }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-xs text-slate-400"></i>
                                        <span>{{ $location->phone }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden xl:table-cell">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        @if($workingHours)
                                            @if($workingHours['24_hours'] ?? false)
                                                <span class="inline-flex items-center gap-1.5">
                                                    <i class="fa-solid fa-clock text-xs text-indigo-500"></i>
                                                    <span class="font-medium">Круглосуточно</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5">
                                                    <i class="fa-solid fa-clock text-xs text-slate-400"></i>
                                                    <span>{{ $workingHours['from'] ?? '—' }} - {{ $workingHours['to'] ?? '—' }}</span>
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 italic">Не указано</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('settings.locations.edit', $location) }}"
                                           class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 transition-all duration-150"
                                           title="Редактировать локацию">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('settings.locations.destroy', $location) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить эту локацию?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-all duration-150"
                                                    title="Удалить локацию">
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
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-16 text-center">
            <div class="max-w-sm mx-auto">
                <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-100 to-indigo-50 dark:from-indigo-900/30 dark:to-indigo-800/20 flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                    Локации не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                    Начните работу с системой, добавив первую локацию с адресом и рабочими часами
                </p>
                <a href="{{ route('settings.locations.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить локацию</span>
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
