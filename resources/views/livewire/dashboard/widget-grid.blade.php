<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
    @foreach($widgetOrder as $widgetKey)
        @if(isset($widgets[$widgetKey]) && $widgets[$widgetKey])
            @switch($widgetKey)
                @case('next_appointment')
                    @if($appointments['next'] ?? null)
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-500/10 dark:to-indigo-600/10 rounded-lg border border-indigo-200 dark:border-indigo-500/20 shadow-sm p-3 md:p-4 lg:p-5 hover:shadow-md transition-all">
                            <div class="flex items-center gap-2 mb-2 md:mb-3">
                                <div class="h-8 w-8 rounded-lg bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-clock text-white text-xs"></i>
                                </div>
                                <h3 class="text-xs font-semibold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide">
                                    Следующая запись
                                </h3>
                            </div>
                            <div class="space-y-1.5 md:space-y-2">
                                <p class="text-xl md:text-2xl font-bold text-indigo-900 dark:text-indigo-100">
                                    {{ \Carbon\Carbon::parse($appointments['next']->time)->format('H:i') }}
                                </p>
                                <p class="text-xs md:text-sm font-medium text-indigo-800 dark:text-indigo-200 truncate">
                                    {{ $appointments['next']->service->name }}
                                </p>
                                <p class="text-[10px] md:text-xs text-indigo-600 dark:text-indigo-300 truncate">
                                    {{ $appointments['next']->client->full_name }}
                                </p>
                                @if($appointments['next']->master)
                                    <p class="text-[10px] md:text-xs text-indigo-500 dark:text-indigo-400 truncate">
                                        {{ trim($appointments['next']->master->first_name . ' ' . $appointments['next']->master->last_name) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                    @break

                @case('today_appointments')
                    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
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
                        <div>
                            @if($appointments['upcoming']->isNotEmpty())
                                @foreach($appointments['upcoming'] as $appointment)
                                    <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
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
                                                   class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center"
                                                   title="Позвонить">
                                                    <i class="fa-solid fa-phone text-xs"></i>
                                                </a>
                                                <a href="{{ route('appointments.show', $appointment->id) }}" 
                                                   class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center"
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
                                <div class="p-8 md:p-12 text-center">
                                    <div class="h-12 w-12 md:h-16 md:w-16 rounded-full bg-slate-100 dark:bg-slate-800 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                                        <i class="fa-solid fa-calendar-xmark text-xl md:text-2xl text-slate-400 dark:text-slate-600"></i>
                                    </div>
                                    <p class="text-xs md:text-sm font-medium text-slate-500 dark:text-slate-400 mb-3 md:mb-4">Нет записей на сегодня</p>
                                    <a href="{{ route('appointments.create') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        <span>Создать запись</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @break

                @case('pending_appointments')
                    @if($appointments['pending']->isNotEmpty())
                        <div class="bg-white dark:bg-slate-900 rounded-lg border border-amber-200 dark:border-amber-800/50 shadow-sm overflow-hidden">
                            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-amber-200 dark:border-amber-800/50 bg-amber-50 dark:bg-amber-950/20">
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
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-[10px] md:text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 flex-shrink-0">
                                        {{ $appointments['pending']->count() }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                @foreach($appointments['pending'] as $appointment)
                                    <a href="{{ route('appointments.show', $appointment->id) }}" 
                                       class="block p-3 md:p-4 border-b border-amber-200 dark:border-amber-800/50 last:border-0 hover:bg-amber-50/30 dark:hover:bg-amber-900/10 transition-colors">
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
                            </div>
                        </div>
                    @endif
                    @break

                @case('recent_clients')
                    @if($clients->isNotEmpty())
                        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
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
                            <div>
                                @foreach($clients as $client)
                                    <div class="p-3 md:p-4 border-b border-slate-200 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
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
                                                   class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/30 transition-colors flex items-center justify-center"
                                                   title="Позвонить">
                                                    <i class="fa-solid fa-phone text-xs"></i>
                                                </a>
                                                <a href="{{ route('clients.show', $client->id) }}" 
                                                   class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center"
                                                   title="Просмотр">
                                                    <i class="fa-regular fa-eye text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @break

                @case('weekly_chart')
                    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-3 sm:px-4 md:px-6 py-2.5 sm:py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-2">
                                <div class="h-7 w-7 sm:h-8 sm:w-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-chart-line text-purple-600 dark:text-purple-400 text-[10px] sm:text-xs"></i>
                                </div>
                                <h3 class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate">
                                    Недельная статистика
                                </h3>
                            </div>
                        </div>
                        <div class="p-3 sm:p-4 md:p-6">
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
                                if ($maxCount === 0) {
                                    $maxCount = 1;
                                }
                            @endphp
                            
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
                                            {{ array_sum(array_column($weeklyData, 'count')) }}
                                        </p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-1.5 sm:p-2 md:p-3 text-center">
                                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mb-0.5 sm:mb-1">Среднее</p>
                                        <p class="text-sm sm:text-lg md:text-xl font-bold text-slate-900 dark:text-white">
                                            {{ round(array_sum(array_column($weeklyData, 'count')) / 7, 1) }}
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
                        </div>
                    </div>
                    @break
            @endswitch
        @endif
    @endforeach
</div>
