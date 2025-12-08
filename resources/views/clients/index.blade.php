@extends('layouts.user')

@section('title', 'Клиенты - Cliently')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Заголовок и управление -->
        <div class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Клиенты
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Ваша клиентская база
                    </p>
                </div>

                <!-- Кнопка добавления -->
                <button
                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-2.5 lg:py-3 px-4 lg:px-6 rounded-lg lg:rounded-xl font-medium transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center space-x-2 w-full sm:w-auto text-sm lg:text-base shadow-md hover:shadow-lg">
                    <i class="fas fa-user-plus"></i>
                    <span>Добавить клиента</span>
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
                            placeholder="Поиск по имени или телефону..."
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
                            <option>По имени (А-Я)</option>
                            <option>По имени (Я-А)</option>
                            <option>По дате добавления</option>
                            <option>По последней записи</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список клиентов -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Список клиентов -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @php
                    // Моки данных для клиентов
                    $clients = [
                        [
                            'name' => 'Анна Ковалева',
                            'phone' => '+7 (999) 123-45-67',
                            'initials' => 'АК',
                            'color' => 'from-blue-500 to-purple-600'
                        ],
                        [
                            'name' => 'Мария Смирнова',
                            'phone' => '+7 (999) 234-56-78',
                            'initials' => 'МС',
                            'color' => 'from-green-500 to-blue-600'
                        ],
                        [
                            'name' => 'Ирина Петрова',
                            'phone' => '+7 (999) 345-67-89',
                            'initials' => 'ИП',
                            'color' => 'from-purple-500 to-pink-600'
                        ],
                        [
                            'name' => 'Елена Волкова',
                            'phone' => '+7 (999) 456-78-90',
                            'initials' => 'ЕВ',
                            'color' => 'from-orange-500 to-red-600'
                        ],
                        [
                            'name' => 'Светлана Иванова',
                            'phone' => '+7 (999) 567-89-01',
                            'initials' => 'СИ',
                            'color' => 'from-teal-500 to-green-600'
                        ],
                        [
                            'name' => 'Ольга Лебедева',
                            'phone' => '+7 (999) 678-90-12',
                            'initials' => 'ОЛ',
                            'color' => 'from-indigo-500 to-blue-600'
                        ],
                        [
                            'name' => 'Наталья Козлова',
                            'phone' => '+7 (999) 789-01-23',
                            'initials' => 'НК',
                            'color' => 'from-pink-500 to-rose-600'
                        ],
                        [
                            'name' => 'Александра Новикова',
                            'phone' => '+7 (999) 890-12-34',
                            'initials' => 'АН',
                            'color' => 'from-yellow-500 to-orange-600'
                        ]
                    ];
                @endphp

                @foreach($clients as $client)
                    <!-- Карточка клиента -->
                    <div class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 lg:space-x-4">
                                <!-- Аватар -->
                                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-r {{ $client['color'] }} rounded-lg lg:rounded-xl flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $client['initials'] }}
                                </div>

                                <!-- Информация -->
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-base lg:text-lg">
                                        {{ $client['name'] }}
                                    </h4>
                                    <p class="text-sm lg:text-base text-gray-600 dark:text-gray-400">
                                        {{ $client['phone'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Кнопки действий (десктоп) -->
                            <div class="hidden lg:flex items-center space-x-2">
                                <!-- Кнопка быстрой записи -->
                                <button
                                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-2">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span>Записать</span>
                                </button>

                                <!-- Кнопка просмотра -->
                                <button
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-2">
                                    <i class="fas fa-eye"></i>
                                    <span>Просмотр</span>
                                </button>

                                <!-- Кнопка редактирования -->
                                <button
                                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Изменить</span>
                                </button>
                            </div>

                            <!-- Кнопки действий (планшет) -->
                            <div class="hidden sm:flex lg:hidden items-center space-x-2">
                                <!-- Кнопка быстрой записи -->
                                <button
                                    class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-1">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span class="hidden xs:inline">Записать</span>
                                </button>

                                <!-- Кнопка просмотра -->
                                <button
                                    class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm flex items-center space-x-1">
                                    <i class="fas fa-eye"></i>
                                    <span class="hidden xs:inline">Просмотр</span>
                                </button>

                                <!-- Выпадающее меню -->
                                <div class="relative">
                                    <button
                                        onclick="toggleDropdown('menu-{{ $loop->index }}')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                                        title="Еще">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <!-- Выпадающее меню -->
                                    <div id="menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                        <div class="py-1">
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-edit text-gray-400"></i>
                                                <span>Изменить</span>
                                            </button>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-trash text-gray-400"></i>
                                                <span>Удалить</span>
                                            </button>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-phone text-gray-400"></i>
                                                <span>Позвонить</span>
                                            </button>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-comment-alt text-gray-400"></i>
                                                <span>Написать</span>
                                            </button>
                                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                            <button
                                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                                <i class="fas fa-file-export text-gray-400"></i>
                                                <span>Экспорт данных</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Мобильное выпадающее меню -->
                            <div class="sm:hidden relative">
                                <button
                                    onclick="toggleDropdown('mobile-menu-{{ $loop->index }}')"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                                    title="Действия">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <!-- Выпадающее меню для мобильных -->
                                <div id="mobile-menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                    <div class="py-1">
                                        <button
                                            class="w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-calendar-plus text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">Записать клиента</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Быстрая запись на услугу</div>
                                            </div>
                                        </button>

                                        <button
                                            class="w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-eye text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">Просмотр профиля</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Полная информация о клиенте</div>
                                            </div>
                                        </button>

                                        <button
                                            class="w-full px-4 py-3 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-edit text-gray-600 dark:text-gray-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">Изменить данные</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Редактировать информацию</div>
                                            </div>
                                        </button>

                                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                                        <button
                                            class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                            <i class="fas fa-phone text-gray-400"></i>
                                            <span>Позвонить</span>
                                        </button>

                                        <button
                                            class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                            <i class="fas fa-comment-alt text-gray-400"></i>
                                            <span>Написать сообщение</span>
                                        </button>

                                        <button
                                            class="w-full px-4 py-2.5 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2">
                                            <i class="fas fa-trash text-gray-400"></i>
                                            <span>Удалить клиента</span>
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
                        Показано <span class="font-medium text-gray-900 dark:text-white">1-8</span> из <span class="font-medium text-gray-900 dark:text-white">24</span> клиентов
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

        <!-- Плавающая кнопка для мобильных -->
        <div class="fixed bottom-6 right-6 md:hidden">
            <button
                class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300">
                <i class="fas fa-user-plus text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Скрипт для выпадающих меню -->
    <script>
        function toggleDropdown(menuId) {
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
