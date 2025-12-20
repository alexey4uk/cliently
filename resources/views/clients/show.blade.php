@extends('layouts.user')

@section('title', 'Анна Ковалева - Cliently')
@section('page-title', 'Анна Ковалева')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => 'Анна Ковалева']
    ]" />
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Заголовок и действия -->
        <div class="mb-6 lg:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <!-- Аватар клиента -->
                    <div class="relative">
                        <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                            АК
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white dark:bg-gray-800 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                    </div>

                    <!-- Информация о клиенте -->
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                <i class="fas fa-circle text-xs mr-2"></i>
                                Активный клиент
                            </span>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">
                                <i class="fas fa-star text-yellow-500 mr-1"></i>
                                4.9 • 12 записей
                            </span>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">
                                <i class="fas fa-user-clock mr-1"></i>
                                Клиент с марта 2024
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('clients.edit', 1) }}" class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300 text-sm flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Редактировать
                    </a>
                    <button class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02] text-sm flex items-center gap-2 shadow-md hover:shadow-lg">
                        <i class="fas fa-calendar-plus"></i>
                        Новая запись
                    </button>
                    <div class="relative">
                        <button class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300 text-sm flex items-center gap-2">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Основное содержимое -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Левая колонка -->
            <div class="lg:col-span-2 space-y-6 lg:space-y-8">
                <!-- Быстрые заметки -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Быстрая заметка
                        </h3>
                        <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                            Все заметки
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-sticky-note text-blue-600 dark:text-blue-400 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        Предпочтения по стрижке
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        2 дня назад
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Предпочитает асимметричную стрижку. Не любит слишком короткие волосы сзади.
                                    Аллергия на некоторые аромомасла.
                                </p>
                            </div>
                        </div>

                        <textarea
                            placeholder="Добавить новую заметку..."
                            rows="3"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm resize-none"
                        ></textarea>

                        <div class="flex justify-end">
                            <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200 text-sm">
                                Сохранить заметку
                            </button>
                        </div>
                    </div>
                </div>

                <!-- История записей -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            История записей
                        </h3>
                        <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                            Посмотреть все
                        </button>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach([
                            ['date' => 'Сегодня', 'time' => '14:00', 'service' => 'Стрижка и укладка', 'duration' => '1.5 ч', 'master' => 'Вы', 'price' => '2 500 ₽', 'status' => 'completed'],
                            ['date' => '15 дек', 'time' => '12:30', 'service' => 'Окрашивание + уход', 'duration' => '3 ч', 'master' => 'Вы', 'price' => '5 800 ₽', 'status' => 'completed'],
                            ['date' => '8 дек', 'time' => '16:00', 'service' => 'Стрижка', 'duration' => '1 ч', 'master' => 'Вы', 'price' => '1 800 ₽', 'status' => 'completed'],
                            ['date' => '1 дек', 'time' => '11:00', 'service' => 'Кератиновое выпрямление', 'duration' => '2.5 ч', 'master' => 'Вы', 'price' => '4 200 ₽', 'status' => 'completed']
                        ] as $appointment)
                            <div class="p-5 lg:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $appointment['date'] }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $appointment['time'] }}
                                            </span>
                                        </div>
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <i class="fas fa-scissors text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $appointment['service'] }}
                                            </p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $appointment['duration'] }}
                                                </span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $appointment['master'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white text-lg">
                                            {{ $appointment['price'] }}
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mt-1">
                                            <i class="fas fa-check mr-1"></i>
                                            Завершено
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 lg:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Всего потрачено:
                                    <span class="font-semibold text-gray-900 dark:text-white ml-1">14 300 ₽</span>
                                </p>
                            </div>
                            <button class="px-4 py-2.5 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-blue-600 dark:text-blue-400 hover:border-blue-400 dark:hover:border-blue-500 transition-colors duration-200 text-sm font-medium">
                                <i class="fas fa-history mr-2"></i>
                                Полная история
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка -->
            <div class="space-y-6 lg:space-y-8">
                <!-- Контактная информация -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Контактная информация
                    </h3>

                    <div class="space-y-4">
                        <!-- Телефон -->
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Телефон</p>
                                <p class="font-medium text-gray-900 dark:text-white">+7 (999) 123-45-67</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-xs font-medium hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                        <i class="fas fa-comment-alt mr-1"></i>
                                        SMS
                                    </button>
                                    <button class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                        <i class="fas fa-phone-alt mr-1"></i>
                                        Позвонить
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                <p class="font-medium text-gray-900 dark:text-white">anna@example.com</p>
                                <button class="mt-2 px-3 py-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg text-xs font-medium hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                                    <i class="fas fa-paper-plane mr-1"></i>
                                    Написать
                                </button>
                            </div>
                        </div>

                        <!-- Дата рождения -->
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-birthday-cake text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Дата рождения</p>
                                <p class="font-medium text-gray-900 dark:text-white">15 мая 1992</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="fas fa-bell mr-1"></i>
                                    Напомнить о дне рождения
                                </p>
                            </div>
                        </div>

                        <!-- Источник -->
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-plus text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Источник</p>
                                <p class="font-medium text-gray-900 dark:text-white">Рекомендация клиента</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Добавлена: 15 марта 2024
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Статистика клиента -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Статистика клиента
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Всего визитов</span>
                            <span class="font-semibold text-gray-900 dark:text-white">12</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Средний чек</span>
                            <span class="font-semibold text-gray-900 dark:text-white">2 860 ₽</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Частота визитов</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">Каждые 2 недели</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Любимая услуга</span>
                            <span class="font-semibold text-gray-900 dark:text-white">Стрижка и укладка</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Последний визит</span>
                            <span class="font-semibold text-gray-900 dark:text-white">Сегодня</span>
                        </div>
                    </div>

                    <!-- Прогресс лояльности -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Программа лояльности</span>
                            <span class="text-sm text-blue-600 dark:text-blue-400">5/10 визитов</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500" style="width: 50%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            5 визитов до получения скидки 15%
                        </p>
                    </div>
                </div>

                <!-- Предстоящие записи -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Ближайшие записи
                    </h3>

                    <div class="space-y-4">
                        @foreach([
                            ['date' => '28 дек', 'time' => '14:00', 'service' => 'Стрижка', 'status' => 'confirmed'],
                            ['date' => '11 янв', 'time' => '16:30', 'service' => 'Окрашивание', 'status' => 'pending']
                        ] as $upcoming)
                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $upcoming['date'] }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $upcoming['time'] }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $upcoming['status'] === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ $upcoming['status'] === 'confirmed' ? '✓ Подтверждена' : '⏳ Ожидает' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $upcoming['service'] }}
                                </p>
                                <div class="flex gap-2 mt-3">
                                    <button class="flex-1 py-1.5 text-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded text-xs font-medium transition-colors">
                                        Изменить
                                    </button>
                                    <button class="flex-1 py-1.5 text-center text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded text-xs font-medium transition-colors">
                                        Отменить
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <button class="w-full py-3 text-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium rounded-lg border border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200 text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            Запланировать запись
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Мобильное меню действий (только для мобильных) -->
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 md:hidden shadow-lg">
            <div class="flex items-center justify-around">
                <button class="flex flex-col items-center text-blue-600 dark:text-blue-400">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-1">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <span class="text-xs font-medium">Позвонить</span>
                </button>

                <button class="flex flex-col items-center text-green-600 dark:text-green-400">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-1">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <span class="text-xs font-medium">SMS</span>
                </button>

                <button class="flex flex-col items-center text-purple-600 dark:text-purple-400">
                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-1">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <span class="text-xs font-medium">Запись</span>
                </button>

                <button class="flex flex-col items-center text-orange-600 dark:text-orange-400">
                    <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mb-1">
                        <i class="fas fa-edit"></i>
                    </div>
                    <span class="text-xs font-medium">Изменить</span>
                </button>
            </div>
        </div>
    </div>
@endsection
