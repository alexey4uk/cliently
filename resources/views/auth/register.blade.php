<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Регистрация - {{ config('app.name', 'Cliently') }}</title>

    <!-- Favicons links -->
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="CLIENTLY" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .register-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .dark .register-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #7e22ce 100%);
        }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
<div class="min-h-screen flex relative">
    <!-- Левая часть - форма -->
    <div class="flex-1 flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-16 xl:px-20"> <!-- Уменьшил отступы -->
        <!-- Кнопка переключения темы -->
        <div class="absolute top-4 right-4 z-50">
            <button id="theme-toggle-desktop" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 focus:outline-none bg-white dark:bg-gray-800 shadow-md">
                <svg id="theme-light-icon-desktop" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-dark-icon-desktop" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <div class="mx-auto w-full max-w-md"> <!-- Уменьшил максимальную ширину -->
            <!-- Заголовок -->
            <div class="text-center lg:text-left mb-6"> <!-- Уменьшил отступ -->
                <!-- Логотип -->
                <div class="flex items-center justify-center lg:justify-start space-x-3 mb-4"> <!-- Уменьшил отступ -->
                    <div class="bg-blue-600 text-white p-2 rounded-lg"> <!-- Уменьшил размер -->
                        <i class="fas fa-users text-xl"></i> <!-- Уменьшил иконку -->
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">CLIENTLY</h1> <!-- Уменьшил размер -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">CRM для мастеров</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"> <!-- Уменьшил размер и добавил отступ -->
                    Создать аккаунт
                </h2>
                <p class="text-base text-gray-600 dark:text-gray-400"> <!-- Уменьшил размер -->
                    Присоединяйтесь к сообществу профессионалов
                </p>
            </div>

            <!-- Форма -->
            <div class="space-y-4"> <!-- Уменьшил отступы -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm"> <!-- Уменьшил отступы -->
                    @csrf

                    <!-- Скрытое поле для нормализованного номера -->
                    <input type="hidden" name="phone_normalized" value="{{ old('phone_normalized') }}" id="phone_normalized">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3"> <!-- Уменьшил gap -->
                        <!-- Имя -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> <!-- Уменьшил отступ -->
                                Имя *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    autocomplete="given-name"
                                    required
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('first_name') border-red-500 ring-2 ring-red-500/20 @enderror"
                                    placeholder="Иван"
                                    value="{{ old('first_name') }}"
                                >
                            </div>
                            @error('first_name')
                            <div class="mt-1 flex items-center space-x-1"> <!-- Уменьшил отступ -->
                                <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Фамилия -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> <!-- Уменьшил отступ -->
                                Фамилия *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    autocomplete="family-name"
                                    required
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('last_name') border-red-500 ring-2 ring-red-500/20 @enderror"
                                    placeholder="Иванов"
                                    value="{{ old('last_name') }}"
                                >
                            </div>
                            @error('last_name')
                            <div class="mt-1 flex items-center space-x-1"> <!-- Уменьшил отступ -->
                                <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> <!-- Уменьшил отступ -->
                            Email адрес *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('email') border-red-500 ring-2 ring-red-500/20 @enderror"
                                placeholder="your@email.com"
                                value="{{ old('email') }}"
                            >
                        </div>
                        @error('email')
                        <div class="mt-1 flex items-center space-x-1"> <!-- Уменьшил отступ -->
                            <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!-- Телефон -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> <!-- Уменьшил отступ -->
                            Телефон *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400 text-sm"></i>
                            </div>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                required
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('phone') border-red-500 ring-2 ring-red-500/20 @enderror"
                                placeholder="+375 (29) 123-45-67"
                                value="{{ old('phone') }}"
                            >
                        </div>
                        @error('phone')
                        <div class="mt-1 flex items-center space-x-1"> <!-- Уменьшил отступ -->
                            <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                        @enderror
                        @error('phone_normalized')
                        <div class="mt-1 flex items-center space-x-1"> <!-- Уменьшил отступ -->
                            <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3"> <!-- Уменьшил gap -->
                        <!-- Пароль -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> <!-- Уменьшил отступ -->
                                Пароль *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('password') border-red-500 ring-2 ring-red-500/20 @enderror"
                                    placeholder="Минимум 8 символов"
                                >
                            </div>
                            @error('password')
                            <div class="mt-1 flex items-center space-x-1"> <!-- Уменьшил отступ -->
                                <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Подтверждение пароля -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> <!-- Уменьшил отступ -->
                                Подтверждение *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300"
                                    placeholder="Повторите пароль"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Соглашение -->
                    <div class="flex items-start space-x-2"> <!-- Уменьшил gap -->
                        <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            required
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500/20 border-gray-300 dark:border-gray-600 rounded transition-colors duration-300 mt-0.5">
                        <label for="terms" class="block text-xs text-gray-700 dark:text-gray-300 leading-relaxed"> <!-- Уменьшил размер -->
                            Я соглашаюсь с
                            <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-300">
                                условиями
                            </a>
                            и
                            <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-300">
                                политикой конфиденциальности
                            </a>
                        </label>
                    </div>
                    @error('terms')
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                        <p class="text-sm text-red-600 dark:text-red-400">Необходимо принять условия использования</p>
                    </div>
                    @enderror

                    <!-- Общие ошибки -->
                    @error('register')
                    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3"> <!-- Уменьшил отступы -->
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror

                    <!-- Кнопка регистрации -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 transition-all duration-300 transform hover:scale-[1.02]"> <!-- Уменьшил размер -->
                            <i class="fas fa-user-plus text-sm"></i>
                            <span>Создать аккаунт</span>
                        </button>
                    </div>

                    <!-- Разделитель -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs"> <!-- Уменьшил размер -->
                                Уже есть аккаунт?
                            </span>
                        </div>
                    </div>

                    <!-- Ссылка на вход -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="group w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-gray-300 dark:border-gray-600 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 bg-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/20 transition-all duration-300 transform hover:scale-[1.02]"> <!-- Уменьшил размер -->
                            <i class="fas fa-sign-in-alt text-sm"></i>
                            <span>Войти в аккаунт</span>
                        </a>
                    </div>
                </form>

                <!-- Дополнительная информация -->
                <div class="text-center pt-2"> <!-- Уменьшил отступ -->
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Ваши данные защищены и не передаются третьим лицам
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Правая часть - баннер -->
    <div class="hidden lg:flex flex-1 register-bg relative">
        <div class="absolute inset-0 bg-black/10 dark:bg-black/20"></div>
        <div class="relative flex flex-col justify-center items-center px-8 text-white text-center"> <!-- Уменьшил отступы -->
            <!-- Иконка -->
            <div class="mb-6"> <!-- Уменьшил отступ -->
                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm"> <!-- Уменьшил размер -->
                    <i class="fas fa-rocket text-3xl text-white"></i> <!-- Уменьшил иконку -->
                </div>
            </div>

            <!-- Заголовок -->
            <h3 class="text-3xl font-bold mb-4"> <!-- Уменьшил размер и отступ -->
                Начните сейчас!
            </h3>

            <!-- Описание -->
            <p class="text-lg opacity-90 mb-6 max-w-md"> <!-- Уменьшил размер и отступ -->
                Присоединяйтесь к тысячам мастеров, которые уже работают эффективнее с Cliently
            </p>

            <!-- Преимущества -->
            <div class="space-y-3 text-left max-w-sm"> <!-- Уменьшил gap -->
                <div class="flex items-center space-x-2"> <!-- Уменьшил gap -->
                    <i class="fas fa-check-circle text-white/80"></i> <!-- Уменьшил иконку -->
                    <span class="text-white/90 text-sm">Бесплатный старт на 14 дней</span> <!-- Уменьшил размер -->
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-white/80"></i>
                    <span class="text-white/90 text-sm">Простая настройка за 5 минут</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-white/80"></i>
                    <span class="text-white/90 text-sm">Поддержка 24/7</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle text-white/80"></i>
                    <span class="text-white/90 text-sm">Без скрытых платежей</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Скрипт для маски телефона и улучшения UX -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Улучшение UX формы
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i><span> Регистрация...</span>';
            submitBtn.disabled = true;
        });

        // Валидация в реальном времени
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.classList.add('border-red-500', 'ring-2', 'ring-red-500/20');
                } else {
                    this.classList.remove('border-red-500', 'ring-2', 'ring-red-500/20');
                }
            });
        });

        // Функция для форматирования имени/фамилии с большой буквы
        function capitalizeName(name) {
            return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
        }

        // Конфигурация телефона
        const PHONE_CONFIG = {
            countryCode: '375',
            validOperatorCodes: ['29', '33', '44', '25'],
            validFirstDigits: ['2', '3', '4'],
            template: '+375 (XX) XXX-XX-XX'
        };

        const phoneInput = document.getElementById('phone');
        const phoneNormalizedInput = document.getElementById('phone_normalized');
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');

        // Общая функция для обработки имен
        function setupNameInput(input) {
            input.addEventListener('input', function(e) {
                const cursorPosition = e.target.selectionStart;
                const originalValue = e.target.value;

                if (originalValue.length > 0) {
                    e.target.value = capitalizeName(originalValue);
                    const newCursorPosition = Math.min(cursorPosition, e.target.value.length);
                    e.target.setSelectionRange(newCursorPosition, newCursorPosition);
                }
            });
        }

        // Настройка полей имени и фамилии
        setupNameInput(firstNameInput);
        setupNameInput(lastNameInput);

        // Функция форматирования телефона
        function formatPhoneNumber(digits) {
            let formattedValue = `+${PHONE_CONFIG.countryCode} `;

            if (digits.length > 0) formattedValue += `(${digits.substring(0, 2)}`;
            if (digits.length > 2) formattedValue += `) ${digits.substring(2, 5)}`;
            if (digits.length > 5) formattedValue += `-${digits.substring(5, 7)}`;
            if (digits.length > 7) formattedValue += `-${digits.substring(7, 9)}`;

            return formattedValue;
        }

        // Функция проверки оператора
        function isValidOperatorCode(digits) {
            if (digits.length === 0) return true;

            const firstDigit = digits.substring(0, 1);
            if (!PHONE_CONFIG.validFirstDigits.includes(firstDigit)) return false;

            if (digits.length >= 2) {
                const operatorCode = digits.substring(0, 2);
                return PHONE_CONFIG.validOperatorCodes.includes(operatorCode);
            }

            return true;
        }

        // Обработка телефона
        phoneInput.addEventListener('input', function(e) {
            let digits = e.target.value.replace(/\D/g, '');

            // Убираем код страны если есть
            if (digits.startsWith(PHONE_CONFIG.countryCode)) {
                digits = digits.substring(PHONE_CONFIG.countryCode.length);
            }

            // Проверяем валидность оператора
            if (!isValidOperatorCode(digits)) {
                digits = digits.substring(0, digits.length - 1);
            }

            // Форматируем и устанавливаем значения
            e.target.value = formatPhoneNumber(digits);
            phoneNormalizedInput.value = `+${PHONE_CONFIG.countryCode}${digits}`;
        });

        // Валидация при потере фокуса
        phoneInput.addEventListener('blur', function(e) {
            const digits = e.target.value.replace(/\D/g, '').replace(PHONE_CONFIG.countryCode, '');

            if (digits.length >= 2) {
                const operatorCode = digits.substring(0, 2);
                if (!PHONE_CONFIG.validOperatorCodes.includes(operatorCode)) {
                    showOperatorError();
                    resetPhoneField();
                }
            } else if (digits.length === 1) {
                const firstDigit = digits.substring(0, 1);
                if (!PHONE_CONFIG.validFirstDigits.includes(firstDigit)) {
                    showOperatorError();
                    resetPhoneField();
                }
            }
        });

        function showOperatorError() {
            alert(`Допустимые коды операторов: ${PHONE_CONFIG.validOperatorCodes.join(', ')}`);
            phoneInput.focus();
        }

        function resetPhoneField() {
            phoneInput.value = `+${PHONE_CONFIG.countryCode} (`;
            phoneNormalizedInput.value = '';
        }

        // Автозаполнение при фокусе для телефона
        phoneInput.addEventListener('focus', function(e) {
            if (!e.target.value || e.target.value === `+${PHONE_CONFIG.countryCode} (`) {
                e.target.value = `+${PHONE_CONFIG.countryCode} (`;
            }
        });
    });
</script>
</body>
</html>
