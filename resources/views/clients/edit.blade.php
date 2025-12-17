@extends('layouts.user')

@section('title', 'Редактирование клиента - Cliently')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Хлебные крошки -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-base md:text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                            <i class="fas fa-home mr-2"></i>
                            Главная
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <a href="{{ route('clients.index') }}" class="ml-1 text-base md:text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white md:ml-2">
                                Клиенты
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <a href="{{ route('clients.show', 1) }}" class="ml-1 text-base md:text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white md:ml-2">
                                Анна Ковалева
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="ml-1 text-base md:text-sm text-gray-700 dark:text-gray-300 font-medium md:ml-2">
                                Редактирование
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Заголовок и действия -->
        <div class="mb-6 lg:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Редактирование клиента
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-base md:text-sm">
                        Обновите информацию о клиенте
                    </p>
                </div>

                <!-- Кнопки действий -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('clients.index') }}"
                       class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300 text-base md:text-sm flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        Отмена
                    </a>
                    <button type="submit"
                            form="client-form"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02] text-base md:text-sm flex items-center gap-2 shadow-md hover:shadow-lg">
                        <i class="fas fa-save"></i>
                        Сохранить изменения
                    </button>
                </div>
            </div>
        </div>

        <!-- Форма редактирования -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Основная форма -->
            <div class="lg:col-span-2">
                <form id="client-form" class="space-y-6 lg:space-y-8">
                    <!-- Основная информация -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                            Основная информация
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                            <!-- Имя -->
                            <div>
                                <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Имя *
                                </label>
                                <input
                                    type="text"
                                    name="first_name"
                                    value="Анна"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                    required
                                >
                            </div>

                            <!-- Фамилия -->
                            <div>
                                <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Фамилия *
                                </label>
                                <input
                                    type="text"
                                    name="last_name"
                                    value="Ковалева"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                    required
                                >
                            </div>

                            <!-- Пол -->
                            <div>
                                <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Пол
                                </label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" value="female" checked
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="ml-2 text-base md:text-sm text-gray-700 dark:text-gray-300">Женский</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" value="male"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="ml-2 text-base md:text-sm text-gray-700 dark:text-gray-300">Мужской</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Дата рождения -->
                            <div>
                                <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Дата рождения
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                    <input
                                        type="text"
                                        name="birth_date"
                                        value="15.05.1992"
                                        placeholder="ДД.ММ.ГГГГ"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Контактная информация -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-address-book mr-2 text-green-500"></i>
                            Контактная информация
                        </h3>

                        <div class="space-y-4 lg:space-y-6">
                            <!-- Телефон -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                                <div>
                                    <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Телефон *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input
                                            type="tel"
                                            name="phone"
                                            value="+7 (999) 123-45-67"
                                            class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                            required
                                        >
                                    </div>
                                </div>

                                <!-- Дополнительный телефон -->
                                <div>
                                    <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Доп. телефон
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-mobile-alt text-gray-400"></i>
                                        </div>
                                        <input
                                            type="tel"
                                            name="phone_alt"
                                            value=""
                                            placeholder="+7 (___) ___-__-__"
                                            class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input
                                        type="email"
                                        name="email"
                                        value="anna@example.com"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Социальные сети -->
                            <div>
                                <label class="block text-base md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Социальные сети
                                </label>
                                <div class="space-y-3">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fab fa-instagram text-pink-500"></i>
                                        </div>
                                        <input
                                            type="text"
                                            name="instagram"
                                            value="@annakovaleva"
                                            placeholder="Instagram"
                                            class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                        >
                                    </div>

                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fab fa-vk text-blue-600"></i>
                                        </div>
                                        <input
                                            type="text"
                                            name="vk"
                                            value="id123456789"
                                            placeholder="ВКонтакте"
                                            class="pl-10 w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-base md:text-sm"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Правая колонка -->
            <div class="space-y-6 lg:space-y-8">

                <!-- Быстрые настройки -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Настройки клиента
                    </h3>

                    <div class="space-y-4">
                        <!-- Уведомления -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-base md:text-sm font-medium text-gray-900 dark:text-white">
                                    SMS уведомления
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Напоминания о записях
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Email рассылка -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-base md:text-sm font-medium text-gray-900 dark:text-white">
                                    Email рассылка
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Новости и акции
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <!-- День рождения -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-base md:text-sm font-medium text-gray-900 dark:text-white">
                                    Поздравлять с ДР
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Отправлять поздравления
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Опасная зона -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-900/50 p-5 lg:p-6">
                    <h3 class="text-lg font-semibold text-red-700 dark:text-red-400 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Опасная зона
                    </h3>

                    <div class="space-y-3">
                        <p class="text-base md:text-sm text-gray-600 dark:text-gray-400">
                            Эти действия нельзя отменить. Будьте осторожны.
                        </p>

                        <button type="button"
                                class="w-full px-4 py-2.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg font-medium transition-colors duration-200 text-base md:text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-archive"></i>
                            Архивировать клиента
                        </button>

                        <button type="button"
                                class="w-full px-4 py-2.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg font-medium transition-colors duration-200 text-base md:text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-user-slash"></i>
                            Удалить клиента
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Мобильные кнопки действий -->
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 lg:hidden shadow-lg">
            <div class="flex items-center justify-between">
                <button type="button"
                        onclick="window.history.back()"
                        class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-700 dark:text-gray-300 text-base md:text-sm flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    Отмена
                </button>

                <button type="submit"
                        form="client-form"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 text-base md:text-sm flex items-center gap-2 shadow-md">
                    <i class="fas fa-save"></i>
                    Сохранить
                </button>
            </div>
        </div>
    </div>
@endsection
