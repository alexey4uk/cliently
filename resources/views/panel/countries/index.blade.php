@extends('layouts.panel')

@section('title', 'Страны')

@section('content')
    <div class="max-w-[1400px] mx-auto">
        <div class="space-y-6">
            <!-- Заголовок -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                            <i class="fa-solid fa-globe text-white text-base sm:text-lg"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Страны</h1>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Справочник стран для телефонов и определения по префиксу</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4">
                        <div class="text-left sm:text-right">
                            <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ $countries->total() }}</p>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Всего стран</p>
                        </div>
                        @can('panel.countries.create')
                            <a href="{{ route('panel.countries.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl text-xs sm:text-sm font-medium hover:from-indigo-700 hover:to-indigo-800 shadow-sm hover:shadow-md transition-all duration-200 whitespace-nowrap">
                                <i class="fa-solid fa-plus text-xs sm:text-sm"></i>
                                <span class="hidden sm:inline">Добавить страну</span>
                                <span class="sm:hidden">Добавить</span>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-800 dark:text-rose-200">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Поиск и фильтры -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <form method="GET" action="{{ route('panel.countries.index') }}">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 sm:gap-4">
                        <div class="flex-1 min-w-0">
                            <label for="search" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Поиск</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                                </div>
                                <input type="text" id="search" name="search" value="{{ $search }}"
                                    placeholder="Название, код, код страны..."
                                    class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="sm:w-48">
                            <label for="is_for_phone_select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">В селекте телефона</label>
                            <select id="is_for_phone_select" name="is_for_phone_select" onchange="this.form.submit()"
                                class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Все</option>
                                <option value="1" {{ $isForPhoneSelect === '1' ? 'selected' : '' }}>Да</option>
                                <option value="0" {{ $isForPhoneSelect === '0' ? 'selected' : '' }}>Нет</option>
                            </select>
                        </div>
                        <div class="sm:w-40">
                            <label for="is_active" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Активна</label>
                            <select id="is_active" name="is_active" onchange="this.form.submit()"
                                class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Все</option>
                                <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>Да</option>
                                <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>Нет</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-search mr-2"></i>Найти
                        </button>
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                    </div>
                </form>
            </div>

            <!-- Таблица -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <a href="{{ route('panel.countries.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1">
                                        Страна
                                        @if($sort === 'name')
                                            <i class="fa-solid fa-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Код / Код 3</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Телефон</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Валюта</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">В селекте</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Телефонов</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($countries as $country)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $country->name }}</div>
                                        @if($country->name_en)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $country->name_en }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                        {{ $country->code }} @if($country->code_3)/ {{ $country->code_3 }}@endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ $country->calling_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                        @if($country->currency){{ $country->currency }} @if($country->currency_symbol)({{ $country->currency_symbol }})@endif @else — @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($country->is_for_phone_select)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">Да</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-slate-500 dark:text-slate-400 rounded-full">Нет</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">{{ $country->phones_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('panel.countries.update')
                                                <a href="{{ route('panel.countries.edit', $country) }}"
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/20">
                                                    <i class="fa-solid fa-edit text-sm"></i>
                                                </a>
                                            @endcan
                                            @can('panel.countries.delete')
                                                <form method="POST" action="{{ route('panel.countries.destroy', $country) }}"
                                                      onsubmit="return confirm('Удалить страну «{{ addslashes($country->name) }}»?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20">
                                                        <i class="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">Страны не найдены</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($countries->hasPages())
                    <div class="px-6 py-3 border-t border-slate-200 dark:border-slate-800">
                        {{ $countries->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
