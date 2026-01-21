<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full flex flex-col">
    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                        Недавние клиенты
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Последние 5 добавленных
                    </p>
                </div>
            </div>
            <a href="{{ route('clients.index') }}" 
               class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors flex-shrink-0 whitespace-nowrap">
                <span class="hidden sm:inline">Все клиенты</span>
                <span class="sm:hidden">Все</span>
                <span class="hidden sm:inline"> →</span>
            </a>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto min-h-0 flex flex-col">
        @if(($clients ?? collect())->isNotEmpty())
            @foreach($clients as $client)
                <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0">
                    <div class="flex items-center justify-between gap-2 md:gap-4">
                        <div class="flex items-start gap-2 md:gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                    <span class="text-sm md:text-base font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $client->initials }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1 truncate">
                                    {{ $client->full_name }}
                                </h4>
                                <div class="flex items-center gap-1.5 md:gap-2 text-xs text-slate-600 dark:text-slate-400 mb-0.5 md:mb-1">
                                    <i class="fa-solid fa-phone text-slate-400 text-[10px] md:text-xs"></i>
                                    <span class="truncate font-medium">{{ $client->phone }}</span>
                                </div>
                                <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400">
                                    Добавлен: {{ $client->created_at->locale('ru')->isoFormat('D MMMM') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 md:gap-1.5 flex-shrink-0">
                            <a href="tel:{{ $client->phone }}" 
                               class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/30 transition-colors flex items-center justify-center flex-shrink-0"
                               title="Позвонить">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </a>
                            <a href="{{ route('clients.show', $client->id) }}" 
                               class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center flex-shrink-0"
                               title="Просмотр">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="flex-1 flex flex-col items-center justify-center py-12 px-4">
                <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-users text-slate-400 dark:text-slate-500 text-3xl"></i>
                </div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Нет недавних клиентов</h4>
                <a href="{{ route('clients.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить клиента</span>
                </a>
            </div>
        @endif
    </div>
</div>
