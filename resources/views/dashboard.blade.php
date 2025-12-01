@extends('layouts.user')

@section('title', 'Панель управления - Cliently')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Приветствие -->
        <div class="mb-8">
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Добро пожаловать, {{ auth()->user()->first_name }}! 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-base">
                {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }} •
                <span class="text-blue-600 dark:text-blue-400">{{ now()->format('H:i') }}</span>
            </p>
        </div>

        <!-- Основные метрики -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Активные клиенты -->
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Ваши клиенты</p>
                        <p class="text-2xl lg:text-3xl font-bold mt-2">24</p>
                        <div class="flex items-center mt-2">
                            <div class="w-16 bg-blue-400 rounded-full h-1 mr-2">
                                <div class="bg-white h-1 rounded-full" style="width: 80%"></div>
                            </div>
                            <span class="text-blue-100 text-xs">80% заполнено</span>
                        </div>
                    </div>
                    <div class="text-xl lg:text-2xl opacity-80">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Записи на сегодня -->
            <div
                class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Сегодня</p>
                        <p class="text-2xl lg:text-3xl font-bold mt-2">5</p>
                        <p class="text-green-100 text-xs lg:text-sm mt-2">Следующая: 14:00</p>
                    </div>
                    <div class="text-xl lg:text-2xl opacity-80">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>

            <!-- Доход -->
            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Доход за месяц</p>
                        <p class="text-2xl lg:text-3xl font-bold mt-2">28.5k</p>
                        <div class="flex items-center mt-2 text-green-300">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span class="text-xs font-medium">+12%</span>
                        </div>
                    </div>
                    <div class="text-xl lg:text-2xl opacity-80">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>

            <!-- Рейтинг -->
            <div
                class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Ваш рейтинг</p>
                        <div class="flex items-center mt-2">
                            <span class="text-2xl lg:text-3xl font-bold">4.8</span>
                            <div class="ml-3">
                                <div class="flex text-yellow-300">
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star-half-alt text-sm"></i>
                                </div>
                                <p class="text-orange-100 text-xs mt-1">8 новых отзывов</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-xl lg:text-2xl opacity-80">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="mb-8">
            <h2 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white mb-4 lg:mb-6">Быстрые
                действия</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4">
                <button
                    class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl p-3 lg:p-4 flex flex-col sm:flex-row sm:items-center text-center sm:text-left hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-2 sm:mb-0 sm:mr-3 group-hover:scale-110 transition-transform duration-200 mx-auto sm:mx-0">
                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="sm:text-left text-center">
                        <p class="text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Новый клиент</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">Добавить в базу</p>
                    </div>
                </button>

                <button
                    class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl p-3 lg:p-4 flex flex-col sm:flex-row sm:items-center text-center sm:text-left hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                    <div
                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-2 sm:mb-0 sm:mr-3 group-hover:scale-110 transition-transform duration-200 mx-auto sm:mx-0">
                        <i class="fas fa-calendar-plus text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="sm:text-left text-center">
                        <p class="text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Новая запись</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">Запланировать</p>
                    </div>
                </button>

                <button
                    class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl p-3 lg:p-4 flex flex-col sm:flex-row sm:items-center text-center sm:text-left hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                    <div
                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-2 sm:mb-0 sm:mr-3 group-hover:scale-110 transition-transform duration-200 mx-auto sm:mx-0">
                        <i class="fas fa-credit-card text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="sm:text-left text-center">
                        <p class="text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Оплата</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">Принять платеж</p>
                    </div>
                </button>

                <button
                    class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl p-3 lg:p-4 flex flex-col sm:flex-row sm:items-center text-center sm:text-left hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
                    <div
                        class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-2 sm:mb-0 sm:mr-3 group-hover:scale-110 transition-transform duration-200 mx-auto sm:mx-0">
                        <i class="fas fa-bell text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="sm:text-left text-center">
                        <p class="text-xs lg:text-sm font-medium text-gray-900 dark:text-white">Напомнить</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">Клиентам</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">
            <!-- Ближайшие записи -->
            <div class="xl:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-4 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white">Сегодняшние
                            записи</h3>
                        <span
                            class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">5 записей</span>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach([
                            ['time' => '14:00', 'name' => 'Анна Ковалева', 'service' => 'Стрижка и укладка', 'status' => 'confirmed', 'initials' => 'АК', 'color' => 'from-blue-500 to-purple-600'],
                            ['time' => '16:30', 'name' => 'Мария Смирнова', 'service' => 'Маникюр', 'status' => 'confirmed', 'initials' => 'МС', 'color' => 'from-green-500 to-blue-600'],
                            ['time' => '18:00', 'name' => 'Ирина Петрова', 'service' => 'Консультация', 'status' => 'pending', 'initials' => 'ИП', 'color' => 'from-purple-500 to-pink-600'],
                            ['time' => '19:30', 'name' => 'Елена Волкова', 'service' => 'Стрижка', 'status' => 'confirmed', 'initials' => 'ЕВ', 'color' => 'from-orange-500 to-red-600']
                        ] as $appointment)
                            <div
                                class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="text-lg font-bold text-gray-900 dark:text-white">{{ $appointment['time'] }}</span>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 hidden lg:block">час</span>
                                        </div>
                                        <div
                                            class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-r {{ $appointment['color'] }} rounded-lg lg:rounded-xl flex items-center justify-center text-white font-semibold text-sm">
                                            {{ $appointment['initials'] }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $appointment['name'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment['service'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right hidden sm:block">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $appointment['status'] === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $appointment['status'] === 'confirmed' ? '✓ Подтверждена' : '⏳ Ожидает' }}
                                </span>
                                    </div>
                                    <div class="sm:hidden">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $appointment['status'] === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $appointment['status'] === 'confirmed' ? '✓' : '⏳' }}
                                </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div
                        class="px-4 lg:px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <button
                            class="w-full py-2.5 lg:py-3 text-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium rounded-lg border border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200 text-sm lg:text-base">
                            <i class="fas fa-plus mr-2"></i>Добавить запись
                        </button>
                    </div>
                </div>
            </div>

            <!-- Правая колонка -->
            <div class="space-y-6 lg:space-y-8">
                <!-- Статус тарифа -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 lg:p-6">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white mb-4">Ваш тариф</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm lg:text-base mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Бесплатный</span>
                                <span class="font-medium text-gray-900 dark:text-white">24/30 клиентов</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div
                                    class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500"
                                    style="width: 80%"></div>
                            </div>
                        </div>
                        <button
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white py-2.5 lg:py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02] text-sm lg:text-base">
                            Обновить тариф
                        </button>
                    </div>
                </div>

                <!-- Предстоящие события -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white">Завтра</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach([
                            ['time' => '10:00', 'name' => 'Светлана И.', 'service' => 'Окрашивание'],
                            ['time' => '12:30', 'name' => 'Ольга Л.', 'service' => 'Стрижка'],
                            ['time' => '15:00', 'name' => 'Наталья К.', 'service' => 'Маникюр']
                        ] as $event)
                            <div class="p-4 lg:p-6">
                                <div class="flex items-center space-x-3 lg:space-x-4">
                                    <div
                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white text-sm lg:text-base">{{ $event['time'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event['name'] }}
                                            • {{ $event['service'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Быстрая статистика -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 lg:p-6">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white mb-4">Недельная
                        статистика</h3>
                    <div class="space-y-3 lg:space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400 text-sm lg:text-base">Новые клиенты</span>
                            <span
                                class="font-semibold text-green-600 dark:text-green-400 text-sm lg:text-base">+3</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400 text-sm lg:text-base">Завершено записей</span>
                            <span class="font-semibold text-gray-900 dark:text-white text-sm lg:text-base">18</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400 text-sm lg:text-base">Средний чек</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white text-sm lg:text-base">1 580 ₽</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400 text-sm lg:text-base">Занятость</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400 text-sm lg:text-base">72%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
