<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full flex flex-col">
    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="h-8 w-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-chart-line text-purple-600 dark:text-purple-400 text-xs"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Недельная статистика</h3>
        </div>
    </div>
    <div class="p-4 md:p-6 flex-1 flex flex-col">
        @php
            // Получаем данные за последние 7 дней
            $weeklyData = [];
            $today = \Carbon\Carbon::today();
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $count = $business->appointments()
                    ->where('date', $date->format('Y-m-d'))
                    ->where('status', '!=', 'cancelled')
                    ->count();
                $weeklyData[] = [
                    'date' => $date,
                    'count' => $count,
                    'label' => $date->locale('ru')->isoFormat('D MMM'),
                ];
            }
            
            // Находим максимальное значение для масштабирования
            $counts = array_column($weeklyData, 'count');
            $maxCount = max($counts);
            $totalCount = array_sum($counts);
        @endphp
        
        @if($totalCount > 0)
            <div class="space-y-3 sm:space-y-4">
                <!-- График -->
                <div class="flex items-end justify-between gap-[2px] sm:gap-1 h-24 sm:h-28 md:h-40">
                    @foreach($weeklyData as $item)
                        <div class="flex-1 flex flex-col items-center gap-[2px] sm:gap-1">
                            <div class="w-full bg-purple-100 dark:bg-purple-500/20 rounded-t-md transition-all hover:bg-purple-200 dark:hover:bg-purple-500/30"
                                 style="height: {{ ($item['count'] / $maxCount) * 100 }}%; min-height: 3px;"
                                 title="{{ $item['label'] }}: {{ $item['count'] }} записей">
                            </div>
                            <span class="text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1">
                                {{ $item['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
                
                <!-- Статистика -->
                <div class="grid grid-cols-3 gap-1.5 sm:gap-2 md:gap-3">
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-1.5 sm:p-2 md:p-3 text-center">
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mb-0.5 sm:mb-1">Всего</p>
                        <p class="text-sm sm:text-lg md:text-xl font-bold text-slate-900 dark:text-white">
                            {{ $totalCount }}
                        </p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-1.5 sm:p-2 md:p-3 text-center">
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mb-0.5 sm:mb-1">Среднее</p>
                        <p class="text-sm sm:text-lg md:text-xl font-bold text-slate-900 dark:text-white">
                            {{ round($totalCount / 7, 1) }}
                        </p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-1.5 sm:p-2 md:p-3 text-center">
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mb-0.5 sm:mb-1">Максимум</p>
                        <p class="text-sm sm:text-lg md:text-xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $maxCount }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center py-12 px-4">
                <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-chart-line text-slate-400 dark:text-slate-500 text-3xl"></i>
                </div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white">Нет данных за неделю</h4>
            </div>
        @endif
    </div>
</div>
