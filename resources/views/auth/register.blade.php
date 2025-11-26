<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Регистрация</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
<div class="min-h-full flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Заголовок -->
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Создать аккаунт
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Заполните форму для регистрации
            </p>
        </div>

        <!-- Форма -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <!-- Скрытое поле для нормализованного номера -->
            <input type="hidden" name="phone_normalized" id="phone_normalized">

            <div class="space-y-4">
                <!-- Имя -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Имя *
                    </label>
                    <input
                        id="first_name"
                        name="first_name"
                        type="text"
                        autocomplete="given-name"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('first_name') border-red-500 @enderror"
                        placeholder="Иван"
                        value="{{ old('first_name') }}"
                    >
                    @error('first_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Фамилия -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Фамилия *
                    </label>
                    <input
                        id="last_name"
                        name="last_name"
                        type="text"
                        autocomplete="family-name"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('last_name') border-red-500 @enderror"
                        placeholder="Иванов"
                        value="{{ old('last_name') }}"
                    >
                    @error('last_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Остальные поля остаются без изменений -->
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email адрес *
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('email') border-red-500 @enderror"
                        placeholder="example@mail.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Телефон -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Телефон *
                    </label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        autocomplete="tel"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('phone') border-red-500 @enderror"
                        placeholder="+375 (29) 123-45-67"
                        value="{{ old('phone') }}"
                    >
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('phone_normalized')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Пароль -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Пароль *
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('password') border-red-500 @enderror"
                        placeholder="Минимум 8 символов"
                    >
                    @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Подтверждение пароля -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Подтверждение пароля *
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300"
                        placeholder="Повторите пароль"
                    >
                </div>
            </div>

            <!-- Соглашение -->
            <div class="flex items-center">
                <input
                    id="terms"
                    name="terms"
                    type="checkbox"
                    required
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded transition-colors duration-300"
                >
                <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                    Я соглашаюсь с
                    <a href="#" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                        условиями использования
                    </a>
                </label>
            </div>
            @error('terms')
            <p class="text-sm text-red-600 dark:text-red-400">Необходимо принять условия использования</p>
            @enderror

            <!-- Кнопка регистрации -->
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-colors duration-300">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </span>
                    Зарегистрироваться
                </button>
            </div>

            <!-- Ссылка на вход -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Уже есть аккаунт?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                        Войти
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Скрипт для маски телефона и форматирования имен -->
<script>
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

    // Маска для телефона в формате Беларуси
    document.addEventListener('DOMContentLoaded', function() {
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

        //updateTheme();
    });

    window.matchMedia('(prefers-color-scheme: dark)');
</script>
</body>
</html>
