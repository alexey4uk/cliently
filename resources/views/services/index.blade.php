@extends('layouts.user')

@section('title', 'Услуги - Cliently')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Заголовок и управление -->
        <div class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Услуги
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Список оказываемых услуг
                    </p>
                </div>

                <!-- Кнопка добавления -->
                <button
                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-2.5 lg:py-3 px-4 lg:px-6 rounded-lg lg:rounded-xl font-medium transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center space-x-2 w-full sm:w-auto text-sm lg:text-base shadow-md hover:shadow-lg">
                    <i class="fas fa-plus"></i>
                    <span>Добавить услугу</span>
                </button>
            </div>
        </div>

        <!-- Поиск и сортировка -->
        <div class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Поиск -->
                <div class="flex-1 relative">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            placeholder="Поиск услуг..."
                            class="pl-10 pr-4 py-3 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm lg:text-base"
                        >
                    </div>
                </div>

                <!-- Сортировка -->
                <div class="sm:w-48">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-sort text-gray-400"></i>
                        </div>
                        <select
                            class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white text-sm lg:text-base appearance-none"
                        >
                            <option>По названию (А-Я)</option>
                            <option>По названию (Я-А)</option>
                            <option>По цене (возрастание)</option>
                            <option>По цене (убывание)</option>
                            <option>По популярности</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список услуг -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Список услуг -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @php
                    // Моки данных для услуг
                    $services = [
                        [
                            'name' => 'Стрижка женская',
                            'description' => 'Классическая стрижка с укладкой',
                            'price' => '1500 ₽',
                            'duration' => '60 мин',
                            'icon' => 'cut',
                            'color' => 'from-blue-500 to-purple-600'
                        ],
                        [
                            'name' => 'Окрашивание волос',
                            'description' => 'Стойкое окрашивание',
                            'price' => '3200 ₽',
                            'duration' => '120 мин',
                            'icon' => 'paint-brush',
                            'color' => 'from-purple-500 to-pink-600'
                        ],
                        [
                            'name' => 'Маникюр классический',
                            'description' => 'Аппаратный маникюр',
                            'price' => '1800 ₽',
                            'duration' => '90 мин',
                            'icon' => 'hand-sparkles',
                            'color' => 'from-pink-500 to-rose-600'
                        ],
                        [
                            'name' => 'Наращивание ресниц',
                            'description' => 'Классическое наращивание',
                            'price' => '2500 ₽',
                            'duration' => '120 мин',
                            'icon' => 'eye',
                            'color' => 'from-indigo-500 to-blue-600'
                        ],
                        [
                            'name' => 'Кератиновое выпрямление',
                            'description' => 'Восстановление волос',
                            'price' => '4200 ₽',
                            'duration' => '180 мин',
                            'icon' => 'wind',
                            'color' => 'from-teal-500 to-green-600'
                        ],
                        [
                            'name' => 'Педикюр',
                            'description' => 'Аппаратный педикюр',
                            'price' => '2200 ₽',
                            'duration' => '90 мин',
                            'icon' => 'shoe-prints',
                            'color' => 'from-orange-500 to-red-600'
                        ],
                        [
                            'name' => 'Чистка лица',
                            'description' => 'Профессиональная чистка',
                            'price' => '2800 ₽',
                            'duration' => '90 мин',
                            'icon' => 'spa',
                            'color' => 'from-green-500 to-teal-600'
                        ],
                        [
                            'name' => 'Мужская стрижка',
                            'description' => 'Стрижка и укладка',
                            'price' => '1000 ₽',
                            'duration' => '45 мин',
                            'icon' => 'user',
                            'color' => 'from-gray-500 to-blue-600'
                        ]
                    ];
                @endphp

                @foreach($services as $service)
                    <!-- Карточка услуги -->
                    <div class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 lg:space-x-4">
                                <!-- Иконка услуги -->
                                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-r {{ $service['color'] }} rounded-lg lg:rounded-xl flex items-center justify-center text-white">
                                    <i class="fas fa-{{ $service['icon'] }}"></i>
                                </div>

                                <!-- Информация -->
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-base lg:text-lg">
                                        {{ $service['name'] }}
                                    </h4>
                                    <p class="text-sm lg:text-base text-gray-600 dark:text-gray-400">
                                        {{ $service['price'] }} • {{ $service['duration'] }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $service['description'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Кнопки действий (десктоп) -->
                            <div class="hidden lg:flex items-center space-x-2">
                                <!-- Кнопка редактирования -->
                                <button
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Изменить</span>
                                </button>

                                <!-- Кнопка быстрой записи -->
                                <button
                                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-2">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span>Записать</span>
                                </button>

                                <!-- Кнопка статистики -->
                                <button
                                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-2">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Статистика</span>
                                </button>
                            </div>

                            <!-- Кнопки действий (планшет) -->
                            <div class="hidden sm:flex lg:hidden items-center space-x-2">
                                <!-- Кнопка редактирования -->
                                <button
                                    class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-1">
                                    <i class="fas fa-edit"></i>
                                    <span class="hidden xs:inline">Изменить</span>
                                </button>

                                <!-- Кнопка быстрой записи -->
                                <button
                                    class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-1">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span class="hidden xs:inline">Записать</span>
                                </button>

                                <!-- Выпадающее меню -->
                                <div class="relative">
                                    <button
                                        onclick="toggleServiceDropdown('menu-{{ $loop->index }}')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                                        title="Еще">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <!-- Выпадающее меню -->
                                    <div id="menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                        <div class="py-1">
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-chart-bar text-gray-400"></i>
                                                <span>Статистика</span>
                                            </button>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-eye text-gray-400"></i>
                                                <span>Просмотр</span>
                                            </button>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-copy text-gray-400"></i>
                                                <span>Дублировать</span>
                                            </button>
                                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2">
                                                <i class="fas fa-trash"></i>
                                                <span>Удалить</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Мобильное выпадающее меню -->
                            <div class="sm:hidden relative">
                                <button
                                    onclick="toggleServiceDropdown('mobile-menu-{{ $loop->index }}')"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                                    title="Действия">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <!-- Выпадающее меню для мобильных -->
                                <div id="mobile-menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                    <div class="py-1">
                                        <button
                                            class="w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-edit text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">Изменить услугу</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Редактировать данные</div>
                                            </div>
                                        </button>

                                        <button
                                            class="w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-calendar-plus text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">Записать клиента</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Быстрая запись на эту услугу</div>
                                            </div>
                                        </button>

                                        <button
                                            class="w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">Статистика</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Анализ популярности</div>
                                            </div>
                                        </button>

                                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                                        <button
                                            class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                            <i class="fas fa-eye text-gray-400"></i>
                                            <span>Просмотр деталей</span>
                                        </button>

                                        <button
                                            class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                            <i class="fas fa-copy text-gray-400"></i>
                                            <span>Дублировать услугу</span>
                                        </button>

                                        <button
                                            class="w-full px-4 py-2.5 text-left text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2">
                                            <i class="fas fa-trash"></i>
                                            <span>Удалить услугу</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            <div class="px-4 lg:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Показано <span class="font-medium text-gray-900 dark:text-white">1-8</span> из <span class="font-medium text-gray-900 dark:text-white">12</span> услуг
                    </div>

                    <div class="flex items-center space-x-1">
                        <button
                            class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>

                        <button
                            class="w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 font-medium">
                            1
                        </button>

                        <button
                            class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300">
                            2
                        </button>

                        <span class="px-2 text-gray-400">...</span>

                        <button
                            class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300">
                            3
                        </button>

                        <button
                            class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Пустое состояние (закомментировано) -->
        {{--
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 lg:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-scissors text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Услуг пока нет
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Добавьте первую услугу, чтобы начать принимать записи
                </p>
                <button
                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02] inline-flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Добавить услугу</span>
                </button>
            </div>
        </div>
        --}}

        <!-- Плавающая кнопка для мобильных -->
        <div class="fixed bottom-6 right-6 md:hidden">
            <button
                class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300">
                <i class="fas fa-plus text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Скрипт для выпадающих меню услуг -->
    <script>
        function toggleServiceDropdown(menuId) {
            const menu = document.getElementById(menuId);
            menu.classList.toggle('hidden');

            // Закрываем другие открытые меню
            document.querySelectorAll('.absolute.bg-white, .absolute.bg-gray-800').forEach(otherMenu => {
                if (otherMenu.id !== menuId && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                }
            });
        }

        // Закрытие меню при клике вне его
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('.absolute.bg-white, .absolute.bg-gray-800').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Закрытие меню при нажатии Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.absolute.bg-white, .absolute.bg-gray-800').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
@endsection

<style>
    /* Анимация для выпадающего меню */
    .absolute.bg-white, .absolute.bg-gray-800 {
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
