@extends('layouts.user')

@section('title', 'Панель управления - Cliently')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <!-- Приветствие и дата -->
        <div class="mb-4 sm:mb-6 lg:mb-8">
            <div class="flex flex-col">
                <div class="mb-3 sm:mb-4">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                        Привет, {{ auth()->user()->first_name }}! 👋
                    </h1>
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 mt-1">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, j F') }} • {{ now()->format('H:i') }}
                    </p>
                </div>

                <!-- Мобильная статистика - 2 в ряд -->
                <div class="sm:hidden grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-xs font-medium">Клиенты</p>
                                <p class="text-xl font-bold mt-1">24</p>
                            </div>
                            <div class="text-lg opacity-80">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <p class="text-blue-100 text-xs mt-2">+3 за неделю</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-xs font-medium">Сегодня</p>
                                <p class="text-xl font-bold mt-1">5</p>
                            </div>
                            <div class="text-lg opacity-80">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <p class="text-green-100 text-xs mt-2">Следующая: 14:00</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-xs font-medium">Доход</p>
                                <p class="text-xl font-bold mt-1">28.5k</p>
                            </div>
                            <div class="text-lg opacity-80">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-arrow-up text-xs mr-1 text-green-300"></i>
                            <span class="text-purple-100 text-xs">+12%</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-xs font-medium">Рейтинг</p>
                                <div class="flex items-center mt-1">
                                    <span class="text-xl font-bold">4.8</span>
                                    <div class="ml-2">
                                        <div class="flex text-yellow-300">
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star-half-alt text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-lg opacity-80">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-orange-100 text-xs mt-2">8 новых отзывов</p>
                    </div>
                </div>

                <!-- Десктопная статистика - 4 в ряд -->
                <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Ваши клиенты</p>
                                <p class="text-2xl lg:text-3xl font-bold mt-2">24</p>
                                <div class="flex items-center mt-3">
                                    <div class="w-16 bg-blue-400/50 rounded-full h-1.5 mr-3">
                                        <div class="bg-white h-1.5 rounded-full" style="width: 80%"></div>
                                    </div>
                                    <span class="text-blue-100 text-xs">+3 за неделю</span>
                                </div>
                            </div>
                            <div class="text-xl lg:text-2xl opacity-80">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Сегодня</p>
                                <p class="text-2xl lg:text-3xl font-bold mt-2">5</p>
                                <div class="flex items-center gap-2 mt-3">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-green-100 text-xs">Следующая:</p>
                                        <p class="text-green-100 text-sm font-semibold">14:00</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xl lg:text-2xl opacity-80">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Доход за месяц</p>
                                <p class="text-2xl lg:text-3xl font-bold mt-2">28.5k</p>
                                <div class="flex items-center mt-3">
                                    <div class="flex items-center text-green-300">
                                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                                        <span class="text-xs font-semibold">+12%</span>
                                    </div>
                                    <span class="text-purple-100 text-xs ml-2">к прошлому месяцу</span>
                                </div>
                            </div>
                            <div class="text-xl lg:text-2xl opacity-80">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-white shadow-lg">
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
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="mb-4 sm:mb-6 lg:mb-8">
            <h2 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 dark:text-white mb-3">
                Быстрые действия
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <button class="bg-white dark:bg-gray-800 rounded-xl p-4 flex flex-col items-center text-center hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 text-sm sm:text-base"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">Новый клиент</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">Добавить в базу</p>
                </button>

                <button class="bg-white dark:bg-gray-800 rounded-xl p-4 flex flex-col items-center text-center hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-calendar-plus text-green-600 dark:text-green-400 text-sm sm:text-base"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">Новая запись</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">Запланировать</p>
                </button>

                <button class="bg-white dark:bg-gray-800 rounded-xl p-4 flex flex-col items-center text-center hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-credit-card text-purple-600 dark:text-purple-400 text-sm sm:text-base"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">Оплата</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">Принять платеж</p>
                </button>

                <button class="bg-white dark:bg-gray-800 rounded-xl p-4 flex flex-col items-center text-center hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-700 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-bell text-orange-600 dark:text-orange-400 text-sm sm:text-base"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">Напомнить</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">Клиентам</p>
                </button>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Левая колонка -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6 lg:space-y-8">
                <!-- БЫСТРЫЕ ЗАМЕТКИ - ТО DO СТИЛЬ -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                            Быстрые заметки
                        </h3>
                        @php
                            // Моки данных для заметок
                            $notes = [
                                [
                                    'id' => 1,
                                    'title' => 'Заказать краску для Анны К.',
                                    'time' => 'Сегодня до 18:00',
                                    'priority' => 'high',
                                    'completed' => false,
                                    'created_at' => now()
                                ],
                                [
                                    'id' => 2,
                                    'title' => 'Позвонить поставщику волос',
                                    'time' => 'Завтра утром',
                                    'priority' => 'medium',
                                    'completed' => false,
                                    'created_at' => now()->subDay()
                                ],
                                [
                                    'id' => 3,
                                    'title' => 'Купить новые ножницы',
                                    'time' => 'На этой неделе',
                                    'priority' => 'low',
                                    'completed' => true,
                                    'created_at' => now()->subDays(2)
                                ],
                                [
                                    'id' => 4,
                                    'title' => 'Подготовить материалы для семинара',
                                    'time' => 'К пятнице',
                                    'priority' => 'high',
                                    'completed' => false,
                                    'created_at' => now()->subHours(3)
                                ],
                                [
                                    'id' => 5,
                                    'title' => 'Обновить прайс-лист услуг',
                                    'time' => 'В течение недели',
                                    'priority' => 'medium',
                                    'completed' => false,
                                    'created_at' => now()->subDays(1)
                                ]
                            ];
                        @endphp
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 sm:px-3 py-1 rounded-full">
                            {{ count($notes) }} заметок
                        </span>
                    </div>

                    <!-- Форма добавления новой заметки -->
                    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-200 dark:border-gray-700">
                        <form action="#" method="POST" class="flex gap-2">
                            @csrf
                            <div class="flex-1 relative">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <input type="text"
                                       name="title"
                                       required
                                       placeholder="Новая заметка..."
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm"
                                >
                            </div>
                            <button type="submit"
                                    class="px-4 sm:px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm whitespace-nowrap">
                                Добавить
                            </button>
                        </form>
                    </div>

                    <!-- Список заметок -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($notes as $note)
                            <div class="p-4 sm:p-5 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-200 {{ $note['completed'] ? 'bg-green-50/50 dark:bg-green-900/10' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3 flex-1">
                                        <!-- Чекбокс выполнения -->
                                        <form action="#" method="POST" class="pt-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="w-5 h-5 flex items-center justify-center rounded border {{ $note['completed'] ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-blue-500' }} transition-colors duration-200">
                                                @if($note['completed'])
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- Текст заметки -->
                                        <div class="flex-1 min-w-0">
                                            <p class="{{ $note['completed'] ? 'line-through text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white' }} text-sm sm:text-base">
                                                {{ $note['title'] }}
                                            </p>

                                            <!-- Дополнительная информация -->
                                            <div class="flex items-center gap-3 mt-2">
                                                @if($note['time'])
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                                        <i class="fas fa-clock mr-1.5 text-gray-400"></i>
                                                        {{ $note['time'] }}
                                                    </span>
                                                @endif

                                                @if($note['priority'])
                                                    <span class="text-xs px-2 py-0.5 rounded-full {{
                                                        $note['priority'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                                        ($note['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300')
                                                    }}">
                                                        {{ $note['priority'] === 'high' ? 'Важно' : ($note['priority'] === 'medium' ? 'Средне' : 'Низкий') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Дата создания -->
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                {{ \Carbon\Carbon::parse($note['created_at'])->format('d.m.Y H:i') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Действия -->
                                    <div class="flex items-center gap-2 ml-3">
                                        <button class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>

                                        <form action="#" method="POST" onsubmit="return confirm('Удалить заметку?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Состояние пустого списка -->
                            <div class="p-8 sm:p-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    Нет заметок
                                </h4>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    Добавьте первую заметку, чтобы не забыть важные дела
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Фильтры заметок -->
                    @if(count($notes) > 0)
                        <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <button class="text-xs px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-lg font-medium">
                                        Все
                                    </button>
                                    <button class="text-xs px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-700">
                                        Активные
                                    </button>
                                    <button class="text-xs px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-700">
                                        Выполненные
                                    </button>
                                </div>

                                <button class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                    Очистить выполненные
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Сегодняшние записи -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-calendar-day mr-2 text-green-500"></i>
                            Сегодняшние записи
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 sm:px-3 py-1 rounded-full">5 записей</span>
                    </div>

                    <!-- Моки данных для записей -->
                    @php
                        $appointments = [
                            [
                                'time' => '14:00',
                                'name' => 'Анна Ковалева',
                                'service' => 'Стрижка и укладка',
                                'status' => 'confirmed',
                                'initials' => 'АК'
                            ],
                            [
                                'time' => '16:30',
                                'name' => 'Мария Смирнова',
                                'service' => 'Маникюр',
                                'status' => 'confirmed',
                                'initials' => 'МС'
                            ],
                            [
                                'time' => '18:00',
                                'name' => 'Ирина Петрова',
                                'service' => 'Консультация',
                                'status' => 'pending',
                                'initials' => 'ИП'
                            ],
                            [
                                'time' => '19:30',
                                'name' => 'Елена Волкова',
                                'service' => 'Стрижка',
                                'status' => 'confirmed',
                                'initials' => 'ЕВ'
                            ]
                        ];
                    @endphp

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($appointments as $appointment)
                            <div class="block p-4 sm:p-5 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col items-center">
                                            <span class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ $appointment['time'] }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 hidden lg:block">час</span>
                                        </div>
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center text-white font-semibold text-sm">
                                            {{ $appointment['initials'] }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate text-sm sm:text-base">{{ $appointment['name'] }}</p>
                                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">{{ $appointment['service'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right hidden sm:block">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $appointment['status'] === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                            {{ $appointment['status'] === 'confirmed' ? '✓ Подтверждена' : '⏳ Ожидает' }}
                                        </span>
                                    </div>
                                    <div class="sm:hidden">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $appointment['status'] === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                            {{ $appointment['status'] === 'confirmed' ? '✓' : '⏳' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                        <button class="w-full py-2.5 sm:py-3 text-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium rounded-lg border border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200 text-sm">
                            <i class="fas fa-plus mr-2"></i>Посмотреть все записи
                        </button>
                    </div>
                </div>
            </div>

            <!-- Правая колонка -->
            <div class="space-y-4 sm:space-y-6 lg:space-y-8">
                <!-- Статистика и тариф -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5 lg:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                        Статистика
                    </h3>

                    <!-- Моки статистики -->
                    @php
                        $statistics = [
                            ['label' => 'Новые клиенты', 'value' => '+3', 'color' => 'green'],
                            ['label' => 'Завершено записей', 'value' => '18', 'color' => 'gray'],
                            ['label' => 'Средний чек', 'value' => '1 580 ₽', 'color' => 'gray'],
                            ['label' => 'Занятость', 'value' => '72%', 'color' => 'blue']
                        ];
                    @endphp

                    <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                        @foreach($statistics as $stat)
                            <div class="flex justify-between items-center pb-2 sm:pb-3 {{ !$loop->last ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</span>
                                <span class="font-semibold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400 text-sm">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Ваш тариф</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Бесплатный • 24/30 клиентов</p>
                            </div>
                            <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold">80%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 sm:h-2 mb-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-1.5 sm:h-2 rounded-full transition-all duration-500" style="width: 80%"></div>
                        </div>
                        <button class="w-full py-2 sm:py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-lg font-medium transition-all duration-200 text-xs sm:text-sm text-center mt-3">
                            Обновить тариф
                        </button>
                    </div>
                </div>

                <!-- Завтра -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>
                            Завтра
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 sm:px-3 py-1 rounded-full">3 записи</span>
                    </div>

                    <!-- Моки записей на завтра -->
                    @php
                        $tomorrowAppointments = [
                            ['time' => '10:00', 'name' => 'Светлана И.', 'service' => 'Окрашивание'],
                            ['time' => '12:30', 'name' => 'Ольга Л.', 'service' => 'Стрижка'],
                            ['time' => '15:00', 'name' => 'Наталья К.', 'service' => 'Маникюр']
                        ];
                    @endphp

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($tomorrowAppointments as $event)
                            <div class="p-4 sm:p-5 lg:p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">{{ $event['time'] }}</p>
                                        </div>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">{{ $event['name'] }} • {{ $event['service'] }}</p>
                                    </div>
                                    <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Мобильная кнопка добавления -->
        <div class="fixed bottom-6 right-6 sm:hidden">
            <button class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300">
                <i class="fas fa-plus text-xl"></i>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // JavaScript функции для работы с заметками
        document.addEventListener('DOMContentLoaded', function() {
            // Обработка чекбоксов заметок
            document.querySelectorAll('form[method="PATCH"] button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const noteItem = form.closest('.p-4, .p-5, .p-6');

                    // Переключаем состояние заметки
                    if (noteItem.classList.contains('bg-green-50/50') || noteItem.classList.contains('dark:bg-green-900/10')) {
                        // Отмечаем как невыполненную
                        noteItem.classList.remove('bg-green-50/50', 'dark:bg-green-900/10');
                        const text = noteItem.querySelector('p.text-sm, p.text-base');
                        text.classList.remove('line-through', 'text-gray-500', 'dark:text-gray-400');
                        text.classList.add('text-gray-900', 'dark:text-white');
                        this.classList.remove('bg-green-500', 'border-green-500');
                        this.classList.add('border-gray-300', 'hover:border-blue-500');
                        this.innerHTML = '';
                    } else {
                        // Отмечаем как выполненную
                        noteItem.classList.add('bg-green-50/50', 'dark:bg-green-900/10');
                        const text = noteItem.querySelector('p.text-sm, p.text-base');
                        text.classList.add('line-through', 'text-gray-500', 'dark:text-gray-400');
                        text.classList.remove('text-gray-900', 'dark:text-white');
                        this.classList.add('bg-green-500', 'border-green-500');
                        this.classList.remove('border-gray-300', 'hover:border-blue-500');
                        this.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                    }

                    // Здесь можно добавить AJAX запрос к серверу
                    console.log('Note toggled');
                });
            });

            // Обработка удаления заметок
            document.querySelectorAll('form[method="DELETE"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('Удалить заметку?')) {
                        const noteItem = this.closest('.p-4, .p-5, .p-6');
                        noteItem.style.opacity = '0.5';

                        // Здесь можно добавить AJAX запрос к серверу
                        setTimeout(() => {
                            noteItem.remove();
                            updateNotesCount();
                        }, 300);
                    }
                });
            });

            // Обработка добавления новой заметки
            const addNoteForm = document.querySelector('form[action="#"]');
            if (addNoteForm) {
                addNoteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = this.querySelector('input[name="title"]');
                    const title = input.value.trim();

                    if (title) {
                        addNewNote(title);
                        input.value = '';
                    }
                });
            }

            // Функция добавления новой заметки
            function addNewNote(title) {
                const notesList = document.querySelector('.divide-y');
                const emptyState = document.querySelector('.p-8, .p-12');

                // Удаляем состояние "нет заметок" если оно есть
                if (emptyState) {
                    emptyState.remove();
                }

                // Создаем новую заметку
                const noteId = Date.now();
                const newNote = document.createElement('div');
                newNote.className = 'p-4 sm:p-5 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-200';
                newNote.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3 flex-1">
                    <form action="#" method="POST" class="pt-1">
                        @csrf
                @method('PATCH')
                <button type="submit"
                        class="w-5 h-5 flex items-center justify-center rounded border border-gray-300 hover:border-blue-500 transition-colors duration-200">
                </button>
            </form>

            <div class="flex-1 min-w-0">
                <p class="text-gray-900 dark:text-white text-sm sm:text-base">
${title}
                        </p>

                        <div class="flex items-center gap-3 mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-clock mr-1.5 text-gray-400"></i>
                                Сейчас
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            ${new Date().toLocaleDateString('ru-RU')} ${new Date().toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'})}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-3">
                    <button class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                        <i class="fas fa-edit text-sm"></i>
                    </button>

                    <form action="#" method="POST" onsubmit="return confirm('Удалить заметку?')">
                        @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </form>
        </div>
    </div>
`;

                // Добавляем в начало списка
                notesList.insertBefore(newNote, notesList.firstChild);

                // Обновляем счетчик
                updateNotesCount();

                // Добавляем обработчики событий для новой заметки
                addEventListenersToNote(newNote);

                console.log('New note added:', title);
            }

            // Функция обновления счетчика заметок
            function updateNotesCount() {
                const notesCount = document.querySelectorAll('.divide-y > div').length;
                const counter = document.querySelector('.rounded-full span');
                if (counter) {
                    counter.textContent = `${notesCount} заметок`;
                }
            }

            // Функция добавления обработчиков событий к заметке
            function addEventListenersToNote(noteElement) {
                // Чекбокс
                const checkbox = noteElement.querySelector('form[method="PATCH"] button');
                if (checkbox) {
                    checkbox.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = this.closest('form');
                        const noteItem = form.closest('.p-4, .p-5, .p-6');

                        if (noteItem.classList.contains('bg-green-50/50') || noteItem.classList.contains('dark:bg-green-900/10')) {
                            noteItem.classList.remove('bg-green-50/50', 'dark:bg-green-900/10');
                            const text = noteItem.querySelector('p.text-sm, p.text-base');
                            text.classList.remove('line-through', 'text-gray-500', 'dark:text-gray-400');
                            text.classList.add('text-gray-900', 'dark:text-white');
                            this.classList.remove('bg-green-500', 'border-green-500');
                            this.classList.add('border-gray-300', 'hover:border-blue-500');
                            this.innerHTML = '';
                        } else {
                            noteItem.classList.add('bg-green-50/50', 'dark:bg-green-900/10');
                            const text = noteItem.querySelector('p.text-sm, p.text-base');
                            text.classList.add('line-through', 'text-gray-500', 'dark:text-gray-400');
                            text.classList.remove('text-gray-900', 'dark:text-white');
                            this.classList.add('bg-green-500', 'border-green-500');
                            this.classList.remove('border-gray-300', 'hover:border-blue-500');
                            this.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                        }
                    });
                }

                // Кнопка удаления
                const deleteForm = noteElement.querySelector('form[method="DELETE"]');
                if (deleteForm) {
                    deleteForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        if (confirm('Удалить заметку?')) {
                            const noteItem = this.closest('.p-4, .p-5, .p-6');
                            noteItem.style.opacity = '0.5';

                            setTimeout(() => {
                                noteItem.remove();
                                updateNotesCount();

                                // Если заметок не осталось, показываем состояние "нет заметок"
                                const notesList = document.querySelector('.divide-y');
                                if (notesList.children.length === 0) {
                                    showEmptyState();
                                }
                            }, 300);
                        }
                    });
                }
            }

            // Функция показа состояния "нет заметок"
            function showEmptyState() {
                const notesList = document.querySelector('.divide-y');
                const emptyState = document.createElement('div');
                emptyState.className = 'p-8 sm:p-12 text-center';
                emptyState.innerHTML = `
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Нет заметок
            </h4>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Добавьте первую заметку, чтобы не забыть важные дела
            </p>
        `;
                notesList.appendChild(emptyState);
            }

            // Добавляем обработчики ко всем существующим заметкам
            document.querySelectorAll('.divide-y > div').forEach(note => {
                addEventListenersToNote(note);
            });

            // Обработка фильтров
            document.querySelectorAll('.bg-gray-100, .bg-blue-100').forEach(button => {
                button.addEventListener('click', function() {
                    // Убираем активный класс у всех кнопок
                    document.querySelectorAll('.bg-gray-100, .bg-blue-100').forEach(btn => {
                        btn.classList.remove('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-800', 'dark:text-blue-300');
                        btn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                    });

                    // Добавляем активный класс текущей кнопке
                    this.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                    this.classList.add('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-800', 'dark:text-blue-300');

                    // Здесь можно добавить логику фильтрации
                    console.log('Filter changed');
                });
            });
        });
    </script>
@endpush
