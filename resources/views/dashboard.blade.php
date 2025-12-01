@extends('layouts.user')

@section('title', 'Панель управления - Cliently')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Заголовок и дата -->
        <div class="mb-6">
            <p class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</p>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Клиенты -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div class="rounded-xl bg-blue-100 dark:bg-blue-900/30 p-3 mr-4">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего клиентов</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">24</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>+3 за месяц
                        </p>
                    </div>
                </div>
            </div>

            <!-- Записи на сегодня -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div class="rounded-xl bg-green-100 dark:bg-green-900/30 p-3 mr-4">
                        <i class="fas fa-calendar-check text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Записи сегодня</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">5</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Следующая: 14:00</p>
                    </div>
                </div>
            </div>

            <!-- Доход за месяц -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div class="rounded-xl bg-yellow-100 dark:bg-yellow-900/30 p-3 mr-4">
                        <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Доход за месяц</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">28 500 ₽</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>+12% к прошлому месяцу
                        </p>
                    </div>
                </div>
            </div>

            <!-- Отзывы -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
                <div class="flex items-center">
                    <div class="rounded-xl bg-purple-100 dark:bg-purple-900/30 p-3 mr-4">
                        <i class="fas fa-star text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Новых отзывов</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">8</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Средняя оценка: 4.8</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="mb-8">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Быстрые действия</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <a href="#" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-[1.02]">
                    <div class="rounded-xl bg-blue-100 dark:bg-blue-900/30 p-3 mb-3">
                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Новый клиент</span>
                </a>

                <a href="#" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-[1.02]">
                    <div class="rounded-xl bg-green-100 dark:bg-green-900/30 p-3 mb-3">
                        <i class="fas fa-calendar-plus text-green-600 dark:text-green-400 text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Новая запись</span>
                </a>

                <a href="#" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-[1.02]">
                    <div class="rounded-xl bg-yellow-100 dark:bg-yellow-900/30 p-3 mb-3">
                        <i class="fas fa-credit-card text-yellow-600 dark:text-yellow-400 text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Принять оплату</span>
                </a>

                <a href="#" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-[1.02]">
                    <div class="rounded-xl bg-purple-100 dark:bg-purple-900/30 p-3 mb-3">
                        <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400 text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Отчеты</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Ближайшие записи -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Ближайшие записи</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Сегодня</span>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Запись 1 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    АК
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Анна Ковалева</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Стрижка и укладка</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">14:00</div>
                                <div class="text-xs text-green-600 dark:text-green-400 flex items-center">
                                    <i class="fas fa-circle mr-1 text-xs"></i>Подтверждена
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Запись 2 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    МС
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Мария Смирнова</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Маникюр</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">16:30</div>
                                <div class="text-xs text-green-600 dark:text-green-400 flex items-center">
                                    <i class="fas fa-circle mr-1 text-xs"></i>Подтверждена
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Запись 3 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ИП
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Ирина Петрова</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Консультация</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">18:00</div>
                                <div class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center">
                                    <i class="fas fa-circle mr-1 text-xs"></i>Ожидает подтверждения
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Запись 4 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ЕВ
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Елена Волкова</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Стрижка</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">19:30</div>
                                <div class="text-xs text-green-600 dark:text-green-400 flex items-center">
                                    <i class="fas fa-circle mr-1 text-xs"></i>Подтверждена
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    <a href="#" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Все записи
                    </a>
                </div>
            </div>

            <!-- Недавние клиенты -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Недавние клиенты</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">За месяц</span>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Клиент 1 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    СИ
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Светлана Иванова</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">+7 (912) 345-67-89</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">2 визита</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Последний: 2 дня назад</div>
                            </div>
                        </div>
                    </div>

                    <!-- Клиент 2 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ОЛ
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Ольга Лебедева</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">+7 (923) 456-78-90</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">1 визит</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Новый клиент</div>
                            </div>
                        </div>
                    </div>

                    <!-- Клиент 3 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-violet-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    НК
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Наталья Козлова</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">+7 (934) 567-89-01</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">3 визита</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Постоянный клиент</div>
                            </div>
                        </div>
                    </div>

                    <!-- Клиент 4 -->
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-amber-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ТМ
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Татьяна Морозова</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">+7 (945) 678-90-12</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">5 визитов</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">VIP клиент</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    <a href="#" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-users mr-2"></i>
                        Все клиенты
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
