@extends('layouts.user')

@section('title', 'Мастера - Cliently')
@section('page-title', 'Мастера')
@section('page-description', 'Управление мастерами вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="space-y-6">
    <!-- Заголовок страницы -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Мастера
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Управление мастерами и их рабочим расписанием
            </p>
        </div>
        <a href="{{ route('settings.masters.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить мастера</span>
        </a>
    </div>

    <!-- Таблица мастеров -->
    @if($masters->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Мастер
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden md:table-cell max-w-[180px]">
                                Специализация
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                                Контакты
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden xl:table-cell">
                                Локации / Услуги
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider w-24">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                        @foreach($masters as $master)
                            @php
                                $workingHours = json_decode($master->working_hours, true);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-4 py-3.5">
                                    <div class="space-y-0.5">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                            {{ $master->first_name }} {{ $master->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 md:hidden truncate">
                                            {{ $master->specialization }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell max-w-[180px]">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 break-words">
                                        {{ $master->specialization }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    <div class="space-y-1">
                                        <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1.5 whitespace-nowrap">
                                            <i class="fa-solid fa-phone text-xs text-slate-400"></i>
                                            <span class="truncate">{{ $master->phone }}</span>
                                        </div>
                                        @if($master->email)
                                            <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                                <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                                                <span class="truncate max-w-[180px]">{{ $master->email }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden xl:table-cell">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if($master->locations->count() > 0)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-medium">
                                                <i class="fa-solid fa-location-dot text-xs"></i>
                                                <span>{{ $master->locations->count() }}</span>
                                            </div>
                                        @endif
                                        @if($master->services->count() > 0)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 text-xs font-medium">
                                                <i class="fa-solid fa-scissors text-xs"></i>
                                                <span>{{ $master->services->count() }}</span>
                                            </div>
                                        @endif
                                        @if($master->locations->count() === 0 && $master->services->count() === 0)
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">Не назначены</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('settings.masters.edit', $master) }}"
                                           class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 transition-all duration-150"
                                           title="Редактировать мастера">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('settings.masters.destroy', $master) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить этого мастера?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-all duration-150"
                                                    title="Удалить мастера">
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
                    <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                    Мастера не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                    Начните работу с системой, добавив первого мастера с контактами и рабочим расписанием
                </p>
                <a href="{{ route('settings.masters.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить мастера</span>
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
