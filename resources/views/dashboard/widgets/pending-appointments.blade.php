<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full flex flex-col">
    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">
                <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-exclamation-circle text-amber-600 dark:text-amber-400 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                        Требуют внимания
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Записи ожидают подтверждения
                    </p>
                </div>
            </div>
            @if(($appointments['pending'] ?? collect())->isNotEmpty())
                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-[10px] md:text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 flex-shrink-0">
                    {{ $appointments['pending']->count() }}
                </span>
            @endif
        </div>
    </div>
    <div class="flex-1 overflow-y-auto min-h-0 flex flex-col">
        @if(($appointments['pending'] ?? collect())->isNotEmpty())
            @foreach($appointments['pending'] as $appointment)
                <a href="{{ route('appointments.show', $appointment->id) }}" 
                   class="block p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <div class="flex items-center justify-between gap-2 md:gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 md:gap-2.5 mb-1 md:mb-1.5 flex-wrap">
                                <span class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400">
                                    {{ $appointment->date->locale('ru')->isoFormat('D MMMM') }}
                                </span>
                                <span class="text-xs md:text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                </span>
                            </div>
                            <p class="text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 truncate mb-0.5 md:mb-1">
                                {{ $appointment->service->name }}
                            </p>
                            <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $appointment->client->full_name }}
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-slate-400 flex-shrink-0"></i>
                    </div>
                </a>
            @endforeach
        @else
            <div class="flex-1 flex flex-col items-center justify-center py-12 px-4">
                <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-check-circle text-slate-400 dark:text-slate-500 text-3xl"></i>
                </div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Все записи обработаны</h4>
                <a href="{{ route('appointments.index', ['status' => 'pending']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-list text-xs"></i>
                    <span>Все записи</span>
                </a>
            </div>
        @endif
    </div>
</div>
