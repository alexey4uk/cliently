@if(($widgets['appointments_chart'] ?? false) || ($widgets['clients_chart'] ?? false))
    <div class="relative w-full">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        @if($widgets['appointments_chart'] ?? false)
            <!-- График записей за неделю -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Активность записей (7 дней)</h3>
                </div>
                <div class="relative h-64" id="appointmentsChartContainer">
                    <canvas id="appointmentsChart"></canvas>
                    <div id="appointmentsChartEmpty" class="hidden flex flex-col items-center justify-center h-full px-4">
                        <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                            <i class="fa-solid fa-chart-line text-slate-400 dark:text-slate-500 text-3xl"></i>
                        </div>
                        <h4 class="text-base font-semibold text-slate-900 dark:text-white">Нет данных за неделю</h4>
                    </div>
                </div>
            </div>
        @endif

        @if($widgets['clients_chart'] ?? false)
            <!-- График новых клиентов -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Новые клиенты (7 дней)</h3>
                </div>
                <div class="relative h-64" id="clientsChartContainer">
                    <canvas id="clientsChart"></canvas>
                    <div id="clientsChartEmpty" class="hidden flex flex-col items-center justify-center h-full px-4">
                        <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                            <i class="fa-solid fa-users text-slate-400 dark:text-slate-500 text-3xl"></i>
                        </div>
                        <h4 class="text-base font-semibold text-slate-900 dark:text-white">Нет данных за неделю</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Дополнительные графики -->
    @if($widgets['appointments_chart'] ?? false)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
            <!-- График по статусам -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Записи по статусам (7 дней)</h3>
                </div>
                <div class="relative h-64" id="statusChartContainer">
                    <canvas id="statusChart"></canvas>
                    <div id="statusChartEmpty" class="hidden flex flex-col items-center justify-center h-full px-4">
                        <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                            <i class="fa-solid fa-chart-pie text-slate-400 dark:text-slate-500 text-3xl"></i>
                        </div>
                        <h4 class="text-base font-semibold text-slate-900 dark:text-white">Нет данных за неделю</h4>
                    </div>
                </div>
            </div>

            <!-- График активности по дням недели -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Активность по дням недели</h3>
                </div>
                <div class="relative h-64" id="weekdayChartContainer">
                    <canvas id="weekdayChart"></canvas>
                    <div id="weekdayChartEmpty" class="hidden flex flex-col items-center justify-center h-full px-4">
                        <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                            <i class="fa-solid fa-calendar-week text-slate-400 dark:text-slate-500 text-3xl"></i>
                        </div>
                        <h4 class="text-base font-semibold text-slate-900 dark:text-white">Нет данных</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
@endif
