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
    <!-- Заголовок и кнопка добавления -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                Мастера
            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                Управляйте мастерами вашего бизнеса
            </p>
        </div>
        <a href="{{ route('settings.masters.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить мастера</span>
        </a>
    </div>

    <!-- Таблица мастеров -->
    @if($masters->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Мастер
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden md:table-cell max-w-[180px]">
                                Специализация
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                                Контакты
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider hidden xl:table-cell">
                                Локации / Услуги
                            </th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider w-20">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($masters as $master)
                            @php
                                $workingHours = json_decode($master->working_hours, true);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                            {{ $master->first_name }} {{ $master->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 md:hidden mt-0.5 line-clamp-1">
                                            {{ $master->specialization }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell max-w-[180px]">
                                    <div class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 break-words">
                                        {{ $master->specialization }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden lg:table-cell">
                                    <div class="space-y-0.5">
                                        <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1 whitespace-nowrap">
                                            <i class="fa-solid fa-phone text-xs text-slate-400 flex-shrink-0"></i>
                                            <span class="truncate">{{ $master->phone }}</span>
                                        </div>
                                        @if($master->email)
                                            <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1">
                                                <i class="fa-solid fa-envelope text-xs text-slate-400 flex-shrink-0"></i>
                                                <span class="truncate max-w-[180px]">{{ $master->email }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden xl:table-cell">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if($master->locations->count() > 0)
                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-400">
                                                <i class="fa-solid fa-location-dot text-xs"></i>
                                                <span>{{ $master->locations->count() }}</span>
                                            </div>
                                        @endif
                                        @if($master->services->count() > 0)
                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-400">
                                                <i class="fa-solid fa-scissors text-xs"></i>
                                                <span>{{ $master->services->count() }}</span>
                                            </div>
                                        @endif
                                        @if($master->locations->count() === 0 && $master->services->count() === 0)
                                            <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('settings.masters.edit', $master) }}"
                                           class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                           title="Редактировать">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('settings.masters.destroy', $master) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить этого мастера?');"
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
                    <i class="fa-solid fa-user-tie text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Нет мастеров
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Добавьте первого мастера для вашего бизнеса
                </p>
                <a href="{{ route('settings.masters.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить мастера</span>
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
