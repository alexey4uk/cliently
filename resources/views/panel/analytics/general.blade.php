@extends('layouts.panel')

@section('title', 'Общая аналитика')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Общая аналитика</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Статистика записей, бизнесов и пользователей</p>
            </div>
            <a href="{{ route('panel.analytics') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <form method="GET" action="{{ route('panel.analytics.general') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Дата начала</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" 
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Дата конца</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" 
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Статус записи</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Все статусы</option>
                        <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>Ожидает</option>
                        <option value="confirmed" {{ $filters['status'] == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                        <option value="completed" {{ $filters['status'] == 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-filter mr-2"></i>
                        Применить фильтры
                    </button>
                    <a href="{{ route('panel.analytics.general') }}" 
                       class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-xmark mr-2"></i>
                        Сбросить
                    </a>
                </div>
            </form>
        </div>

        <!-- Метрики -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Всего записей</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['total'], 0, '.', ' ') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Конверсия</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['conversion_rate'], 1, '.', ' ') }}%</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Завершенные / Всего</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Процент отмен</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['cancellation_rate'], 1, '.', ' ') }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-rose-100 dark:bg-rose-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-xmark-circle text-rose-600 dark:text-rose-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Среднее на бизнес</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['avg_appointments_per_business'], 1, '.', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">записей</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-building text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- График динамики записей -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Динамика записей</h3>
            <div class="h-80">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>

        <!-- Графики распределения -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Статусы записей -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Статусы записей</h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Рост клиентов -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Активность бизнесов</h3>
                <div class="h-64">
                    <canvas id="businessesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Статистика по бизнесам -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Статистика по бизнесам (топ по записям)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Бизнес</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Количество записей</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['stats_by_business'] as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $item['name'] }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ number_format($item['count'], 0, '.', ' ') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Нет данных</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Статистика по услугам -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Популярные услуги</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Услуга</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Количество</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['stats_by_service'] as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $item['service_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ number_format($item['count'], 0, '.', ' ') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Нет данных</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Статистика по мастерам -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Статистика по мастерам</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Мастер</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Количество</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['stats_by_master'] as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $item['master_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ number_format($item['count'], 0, '.', ' ') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Нет данных</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const appointmentsData = @json($data['appointments_by_period']);
                const statusData = @json($data['stats_by_status']);

                const isDarkMode = () => document.documentElement.classList.contains('dark');
                const getThemeColors = () => ({
                    text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                    textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                    grid: 'rgba(148, 163, 184, 0.1)'
                });

                // График динамики записей
                const appointmentsCtx = document.getElementById('appointmentsChart');
                if (appointmentsCtx && appointmentsData.length > 0) {
                    const colors = getThemeColors();
                    new Chart(appointmentsCtx, {
                        type: 'line',
                        data: {
                            labels: appointmentsData.map(item => item.label),
                            datasets: [
                                {
                                    label: 'Всего',
                                    data: appointmentsData.map(item => item.total),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Завершено',
                                    data: appointmentsData.map(item => item.completed),
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Отменено',
                                    data: appointmentsData.map(item => item.cancelled),
                                    borderColor: 'rgb(239, 68, 68)',
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: { color: colors.text }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                },
                                y: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                }
                            }
                        }
                    });
                }

                // График статусов записей
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    const colors = getThemeColors();
                    const statusLabels = {
                        'pending': 'Ожидает',
                        'confirmed': 'Подтверждено',
                        'completed': 'Завершено',
                        'cancelled': 'Отменено',
                    };
                    new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(statusData).map(key => statusLabels[key] || key),
                            datasets: [{
                                data: Object.values(statusData),
                                backgroundColor: [
                                    'rgb(251, 146, 60)',
                                    'rgb(59, 130, 246)',
                                    'rgb(34, 197, 94)',
                                    'rgb(239, 68, 68)',
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: colors.text }
                                }
                            }
                        }
                    });
                }

                // График активности бизнесов (заглушка - можно расширить)
                const businessesCtx = document.getElementById('businessesChart');
                if (businessesCtx) {
                    const colors = getThemeColors();
                    // Простой график для демонстрации
                    new Chart(businessesCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Активные', 'Неактивные'],
                            datasets: [{
                                label: 'Бизнесы',
                                data: [{{ $data['stats_by_business']->count() }}, 0],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(148, 163, 184, 0.8)',
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                },
                                y: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
