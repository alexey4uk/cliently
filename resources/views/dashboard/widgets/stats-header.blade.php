@if($widgets['stats_header'])
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        {{-- Заголовок блока метрик --}}
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <div>
                    <h2 class="text-base md:text-lg font-bold text-slate-900 dark:text-white">
                        Общая статистика
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Ключевые показатели вашего бизнеса
                    </p>
                </div>
            </div>
        </div>
        <div class="p-4 md:p-6">
        
        @php
            $firstRowCards = [];
            $secondRowCards = [];
            
            if($widgets['stat_today'] ?? true) $firstRowCards[] = 'today';
            if($widgets['stat_week'] ?? true) $firstRowCards[] = 'week';
            if($widgets['stat_new_clients'] ?? true) $firstRowCards[] = 'new_clients';
            if($widgets['stat_total_clients'] ?? true) $firstRowCards[] = 'total_clients';
            
            if($widgets['stat_pending'] ?? true) $secondRowCards[] = 'pending';
            if($widgets['stat_completed'] ?? true) $secondRowCards[] = 'completed';
            if($widgets['stat_cancelled'] ?? true) $secondRowCards[] = 'cancelled';
            if($widgets['stat_avg_per_day'] ?? true) $secondRowCards[] = 'avg_per_day';
            
            $firstRowCount = count($firstRowCards);
            $secondRowCount = count($secondRowCards);
            $totalCardsCount = $firstRowCount + $secondRowCount;
        @endphp
        
        @if($totalCardsCount > 0)
            @if($firstRowCount > 0)
            <!-- Первая строка - основные метрики -->
            <div class="grid grid-cols-2 {{ $firstRowCount <= 2 ? 'md:grid-cols-2' : ($firstRowCount == 3 ? 'md:grid-cols-3' : 'md:grid-cols-4') }} gap-3 md:gap-4">
                @if($widgets['stat_today'] ?? true)
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-500/10 dark:to-indigo-600/10 rounded-lg border border-indigo-200 dark:border-indigo-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-day text-white text-xs"></i>
                        </div>
                        @if(isset($stats['appointments_growth_rate']) && $stats['appointments_growth_rate'] > 0)
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($stats['appointments_growth_rate']) }}%
                            </span>
                        @endif
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Сегодня</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['today'] ?? 0 }}</p>
                    @if(isset($stats['appointments_tomorrow']) && $stats['appointments_tomorrow'] > 0)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Завтра: {{ $stats['appointments_tomorrow'] }}</p>
                    @endif
                </div>
                @endif
            @if($widgets['stat_week'] ?? true)
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-500/10 dark:to-emerald-600/10 rounded-lg border border-emerald-200 dark:border-emerald-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-emerald-500 dark:bg-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-check-circle text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">За неделю</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['appointments_week'] ?? 0 }}</p>
                    @if(isset($stats['completed_week']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Завершено: {{ $stats['completed_week'] }}</p>
                    @endif
                </div>
            @endif
            @if($widgets['stat_new_clients'] ?? true)
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-500/10 dark:to-blue-600/10 rounded-lg border border-blue-200 dark:border-blue-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-user-plus text-white text-xs"></i>
                        </div>
                        @if(isset($stats['clients_growth_rate']) && $stats['clients_growth_rate'] > 0)
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($stats['clients_growth_rate']) }}%
                            </span>
                        @endif
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Новые клиенты</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['new_clients_month'] ?? 0 }}</p>
                    @if(isset($stats['new_clients_week']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">За неделю: +{{ $stats['new_clients_week'] }}</p>
                    @endif
                </div>
            @endif
            @if($widgets['stat_total_clients'] ?? true)
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-500/10 dark:to-purple-600/10 rounded-lg border border-purple-200 dark:border-purple-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-purple-500 dark:bg-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-users text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Всего клиентов</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['total_clients'] ?? 0 }}</p>
                    @if(isset($stats['active_clients_rate']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Активных: {{ $stats['active_clients_rate'] }}%</p>
                    @endif
                </div>
                @endif
            </div>
            @endif

            @if($secondRowCount > 0)
            <!-- Вторая строка - метрики эффективности -->
            <div class="grid grid-cols-2 {{ $secondRowCount <= 2 ? 'md:grid-cols-2' : ($secondRowCount == 3 ? 'md:grid-cols-3' : 'md:grid-cols-4') }} gap-3 md:gap-4 mt-4 md:mt-6">
                @if($widgets['stat_pending'] ?? true)
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-500/10 dark:to-amber-600/10 rounded-lg border border-amber-200 dark:border-amber-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-amber-500 dark:bg-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-hourglass-half text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Ожидают</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['pending_count'] ?? 0 }}</p>
                    @if(isset($stats['confirmation_rate']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Подтверждено: {{ $stats['confirmation_rate'] }}%</p>
                    @endif
                </div>
                @endif
            @if($widgets['stat_completed'] ?? true)
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-500/10 dark:to-blue-600/10 rounded-lg border border-blue-200 dark:border-blue-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-check-double text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Завершено</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['completed_count'] ?? 0 }}</p>
                    @if(isset($stats['completion_rate']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Успешность: {{ $stats['completion_rate'] }}%</p>
                    @endif
                </div>
                @endif
            @if($widgets['stat_cancelled'] ?? true)
                <div class="bg-gradient-to-br from-rose-50 to-rose-100 dark:from-rose-500/10 dark:to-rose-600/10 rounded-lg border border-rose-200 dark:border-rose-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-rose-500 dark:bg-rose-600 flex items-center justify-center">
                            <i class="fa-solid fa-xmark-circle text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Отменено</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['cancelled_count'] ?? 0 }}</p>
                    @if(isset($stats['cancellation_rate']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Процент: {{ $stats['cancellation_rate'] }}%</p>
                    @endif
                </div>
            @endif
            @if($widgets['stat_avg_per_day'] ?? true)
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-500/10 dark:to-slate-600/10 rounded-lg border border-slate-200 dark:border-slate-500/20 p-3 md:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-8 w-8 rounded-lg bg-slate-500 dark:bg-slate-600 flex items-center justify-center">
                            <i class="fa-solid fa-chart-bar text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Среднее/день</p>
                    <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['avg_appointments_per_day'] ?? 0 }}</p>
                    @if(isset($stats['total_appointments']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Всего: {{ $stats['total_appointments'] }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endif
        @endif
        </div>
    </div>
@endif
