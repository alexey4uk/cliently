<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full flex flex-col">
    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                        Записи на сегодня
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $appointments['todayDate'] }}
                    </p>
                </div>
            </div>
            <a href="{{ route('appointments.index') }}" 
               class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors flex-shrink-0 whitespace-nowrap">
                <span class="hidden sm:inline">Все записи</span>
                <span class="sm:hidden">Все</span>
                <span class="hidden sm:inline"> →</span>
            </a>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto min-h-0 flex flex-col">
        @if($appointments['upcoming']->isNotEmpty())
            @foreach($appointments['upcoming'] as $appointment)
                <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0">
                    <div class="flex items-center justify-between gap-2 md:gap-4">
                        <div class="flex items-start gap-2 md:gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="h-10 w-10 md:h-12 md:w-12 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                    <span class="text-sm md:text-base font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1 truncate">
                                    {{ $appointment->service->name }}
                                </h4>
                                <div class="flex items-center gap-1.5 md:gap-2 text-xs text-slate-600 dark:text-slate-400 mb-0.5 md:mb-1">
                                    <i class="fa-solid fa-user text-slate-400 text-[10px] md:text-xs"></i>
                                    <span class="truncate font-medium">{{ $appointment->client->full_name }}</span>
                                </div>
                                @if($appointment->master)
                                    <div class="flex items-center gap-1.5 md:gap-2 text-xs text-slate-500 dark:text-slate-500">
                                        <i class="fa-solid fa-user-tie text-slate-400 text-[10px] md:text-xs"></i>
                                        <span class="truncate">{{ trim($appointment->master->first_name . ' ' . $appointment->master->last_name) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1 md:gap-1.5 flex-shrink-0">
                            <a href="tel:{{ $appointment->client->phone }}" 
                               class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center flex-shrink-0"
                               title="Позвонить">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </a>
                            <a href="{{ route('appointments.show', $appointment->id) }}" 
                               class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center flex-shrink-0"
                               title="Просмотр">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        @if($appointments['completed']->isNotEmpty())
            <div class="px-4 md:px-6 pt-3 md:pt-4 pb-2 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Выполненные</p>
            </div>
            @foreach($appointments['completed'] as $appointment)
                <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors opacity-75">
                    <div class="flex items-center justify-between gap-2 md:gap-4">
                        <div class="flex items-start gap-2 md:gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="h-10 w-10 md:h-12 md:w-12 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-xs md:text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 md:gap-2 mb-0.5 md:mb-1 flex-wrap">
                                    <span class="text-xs md:text-sm font-semibold text-slate-500 dark:text-slate-400 line-through">
                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 md:px-2 py-0.5 rounded text-[10px] md:text-xs font-medium bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                        Выполнено
                                    </span>
                                </div>
                                <h4 class="text-xs md:text-sm font-medium text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1 truncate">
                                    {{ $appointment->service->name }}
                                </h4>
                                <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ $appointment->client->full_name }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('appointments.show', $appointment->id) }}" 
                           class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center flex-shrink-0"
                           title="Просмотр">
                            <i class="fa-regular fa-eye text-xs"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif

        @if($appointments['upcoming']->isEmpty() && $appointments['completed']->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center py-12 px-4">
                <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-calendar-xmark text-slate-400 dark:text-slate-500 text-3xl"></i>
                </div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Нет записей на сегодня</h4>
                <a href="{{ route('appointments.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Создать запись</span>
                </a>
            </div>
        @endif
    </div>
</div>
