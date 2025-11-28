@extends('layouts.user')

@section('title', 'Профиль пользователя - Cliently')

@section('content')
    <div class="max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <!-- Заголовок -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Профиль пользователя</h1>
            <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Управление вашими личными данными</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
            <!-- Левая колонка - аватар и информация -->
{{--            <div class="lg:col-span-1">--}}
{{--                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">--}}
{{--                    <!-- Аватар -->--}}
{{--                    <div class="text-center mb-4 sm:mb-6">--}}
{{--                        <div class="relative inline-block">--}}
{{--                            @if(auth()->user()->avatar)--}}
{{--                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Аватар" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full mx-auto border-4 border-white dark:border-gray-800 shadow-lg">--}}
{{--                            @else--}}
{{--                                <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full mx-auto border-4 border-white dark:border-gray-800 shadow-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">--}}
{{--                                <span class="text-2xl sm:text-4xl font-bold text-white">--}}
{{--                                    {{ Str::substr(auth()->user()->first_name, 0, 1) }}{{ Str::substr(auth()->user()->last_name, 0, 1) }}--}}
{{--                                </span>--}}
{{--                                </div>--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mt-3 sm:mt-4">--}}
{{--                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}--}}
{{--                        </h2>--}}
{{--                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 break-words">{{ auth()->user()->email }}</p>--}}
{{--                    </div>--}}

{{--                    <!-- Статистика -->--}}
{{--                    <div class="space-y-3 sm:space-y-4">--}}
{{--                        <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <i class="fas fa-users text-blue-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>--}}
{{--                                <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Клиентов</span>--}}
{{--                            </div>--}}
{{--                            <span class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">24</span>--}}
{{--                        </div>--}}

{{--                        <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <i class="fas fa-calendar-check text-green-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>--}}
{{--                                <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Записей в этом месяце</span>--}}
{{--                            </div>--}}
{{--                            <span class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">18</span>--}}
{{--                        </div>--}}

{{--                        <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <i class="fas fa-star text-yellow-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>--}}
{{--                                <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Рейтинг</span>--}}
{{--                            </div>--}}
{{--                            <span class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">4.8</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- Правая колонка - форма редактирования -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <!-- Заголовок формы -->
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">Редактирование профиля</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Обновите вашу личную информацию</p>
                    </div>

                    <!-- Форма -->
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
                        @csrf
                        @method('PATCH')

                        <!-- Уведомления -->
                        @if(session('success'))
                            <div class="mb-4 sm:mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 sm:p-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle text-green-500 text-base sm:text-lg"></i>
                                    <p class="text-green-700 dark:text-green-300 font-medium text-sm sm:text-base">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 sm:mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 sm:p-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-exclamation-circle text-red-500 text-base sm:text-lg"></i>
                                    <p class="text-red-700 dark:text-red-300 font-medium text-sm sm:text-base">Пожалуйста, исправьте ошибки в форме</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-4 sm:gap-6">
                            <!-- Имя -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                                    Имя *
                                </label>
                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    value="{{ old('first_name', auth()->user()->first_name) }}"
                                    required
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('first_name') border-red-500 @enderror"
                                    placeholder="Введите ваше имя"
                                >
                                @error('first_name')
                                <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Фамилия -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                                    Фамилия *
                                </label>
                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    value="{{ old('last_name', auth()->user()->last_name) }}"
                                    required
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('last_name') border-red-500 @enderror"
                                    placeholder="Введите вашу фамилию"
                                >
                                @error('last_name')
                                <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                                    Email адрес *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    required
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('email') border-red-500 @enderror"
                                    placeholder="your@email.com"
                                >
                                @error('email')
                                <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Телефон -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                                    Телефон *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400 text-xs sm:text-sm"></i>
                                    </div>
                                    <input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        autocomplete="tel"
                                        required
                                        class="block w-full pl-9 sm:pl-10 pr-3 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 @error('phone') border-red-500 ring-2 ring-red-500/20 @enderror"
                                        placeholder="+375 (29) 123-45-67"
                                        value="{{ old('phone', auth()->user()->phone) }}"
                                    >
                                </div>
                                @error('phone')
                                <div class="mt-1 flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                    <p class="text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                </div>
                                @enderror
                                @error('phone_normalized')
                                <div class="mt-1 flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                    <p class="text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                </div>
                                @enderror
                            </div>

                            <!-- Аватар -->
                            <div class="md:col-span-2">
                                <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Аватар
                                </label>

                                <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                                    <!-- Предпросмотр аватара -->
                                        <div class="flex-shrink-0 relative">
                                            <div id="avatar-preview" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg">
                                                @if(auth()->user()->avatar)
                                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Текущий аватар"
                                                         class="w-full h-full object-cover" id="current-avatar">
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white font-bold text-lg sm:text-xl" id="avatar-initials">
                            {{ Str::substr(auth()->user()->first_name, 0, 1) }}{{ Str::substr(auth()->user()->last_name, 0, 1) }}
                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div id="avatar-loading" class="absolute inset-0 bg-gray-500 bg-opacity-50 rounded-full flex items-center justify-center hidden">
                                                <i class="fas fa-spinner fa-spin text-white text-xl"></i>
                                            </div>
                                        </div>


                                    <!-- Поле загрузки -->
                                    <div class="flex-1">
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <!-- Контейнер для кнопки выбора файла -->
                                            <div class="flex-1">
                                                <input
                                                    type="file"
                                                    id="avatar"
                                                    name="avatar"
                                                    accept="image/*"
                                                    class="hidden"
                                                >
                                                <label for="avatar" class="cursor-pointer bg-blue-600 text-white py-2.5 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center space-x-2">
                                                    <i class="fas fa-upload"></i>
                                                    <span>Выбрать файл</span>
                                                </label>
                                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" id="file-name">
                                                    PNG, JPG, GIF до 5MB
                                                </p>
                                            </div>

                                            <!-- Кнопка удаления -->
                                            @if(auth()->user()->avatar)
                                                <button type="button" id="remove-avatar-btn" class="bg-red-600 text-white py-2.5 px-4 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200 flex items-center justify-center space-x-2 sm:self-start"> <!-- sm:self-start для выравнивания по верху на ПК -->
                                                    <i class="fas fa-trash"></i>
                                                    <span>Удалить</span>
                                                </button>
                                            @endif
                                        </div>

                                        @error('avatar')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="flex flex-col gap-3 mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center text-sm sm:text-base">
                                <i class="fas fa-save mr-2"></i>
                                Сохранить изменения
                            </button>
                            <a href="{{ route('dashboard') }}" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center text-sm sm:text-base">
                                <i class="fas fa-times mr-2"></i>
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Дополнительные настройки -->
                <div class="mt-4 sm:mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">Безопасность</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Управление безопасностью аккаунта</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-3 gap-3 sm:gap-0">
                            <div class="mb-2 sm:mb-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Смена пароля</h4>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Обновите ваш пароль для безопасности</p>
                            </div>
                            <a href="" class="w-full sm:w-auto bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200 text-center">
                                Сменить пароль
                            </a>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-3 border-t border-gray-200 dark:border-gray-700 gap-3 sm:gap-0">
                            <div class="mb-2 sm:mb-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Двухфакторная аутентификация</h4>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Дополнительная защита вашего аккаунта</p>
                            </div>
                            <button class="w-full sm:w-auto bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                                Включить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatar-preview');
            const avatarLoading = document.getElementById('avatar-loading');
            const fileName = document.getElementById('file-name');
            const removeAvatarBtn = document.getElementById('remove-avatar-btn');

            // Безопасное получение инициалов
            function getAvatarInitials() {
                const initialsElement = document.getElementById('avatar-initials');
                if (initialsElement) {
                    return initialsElement.textContent;
                }

                const userFirstName = '{{ auth()->user()->first_name }}';
                const userLastName = '{{ auth()->user()->last_name }}';

                if (userFirstName && userLastName) {
                    return (userFirstName.charAt(0) + userLastName.charAt(0)).toUpperCase();
                }

                return 'US';
            }

            // Обработка удаления аватара через AJAX
            if (removeAvatarBtn) {
                removeAvatarBtn.addEventListener('click', async function() {
                    if (!confirm('Вы уверены, что хотите удалить аватар?')) {
                        return;
                    }

                    // Показываем индикатор загрузки
                    if (avatarLoading) {
                        avatarLoading.classList.remove('hidden');
                    }

                    // Блокируем кнопку на время запроса
                    this.disabled = true;

                    try {
                        console.log('Sending avatar deletion request...');

                        const response = await fetch('{{ route("profile.avatar.delete") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });

                        console.log('Response status:', response.status);

                        const result = await response.json();
                        console.log('Response data:', result);

                        if (response.ok && result.success) {
                            console.log('Avatar deleted successfully');

                            // Показываем уведомление
                            showNotification('Аватар успешно удален', 'success');

                            // Обновляем страницу через 1.5 секунды (после показа уведомления)
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);

                        } else {
                            console.error('Server returned error:', result);
                            throw new Error(result.message || `HTTP error! status: ${response.status}`);
                        }
                    } catch (error) {
                        console.error('Error deleting avatar:', error);
                        showNotification('Ошибка при удалении аватара: ' + error.message, 'error');
                    } finally {
                        // Скрываем индикатор загрузки
                        if (avatarLoading) {
                            avatarLoading.classList.add('hidden');
                        }
                        // Разблокируем кнопку
                        this.disabled = false;
                    }
                });
            }

            // Функция создания кнопки удаления для новых загруженных файлов
            function createRemoveButton() {
                const button = document.createElement('button');
                button.type = 'button';
                button.id = 'remove-avatar-btn-new';
                button.className = 'bg-red-600 text-white py-2.5 px-4 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200 flex items-center justify-center sm:justify-start space-x-2';
                button.innerHTML = '<i class="fas fa-trash"></i><span>Удалить</span>';

                button.addEventListener('click', async function() {
                    if (!confirm('Вы уверены, что хотите удалить аватар?')) {
                        return;
                    }

                    if (avatarLoading) {
                        avatarLoading.classList.remove('hidden');
                    }

                    this.disabled = true;

                    try {
                        const response = await fetch('{{ route("profile.avatar.delete") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            // Показываем уведомление
                            showNotification('Аватар успешно удален', 'success');

                            // Обновляем страницу через 1.5 секунды
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);

                        } else {
                            throw new Error(result.message || 'Ошибка при удалении');
                        }
                    } catch (error) {
                        console.error('Error deleting avatar:', error);
                        showNotification('Ошибка при удалении аватара: ' + error.message, 'error');
                    } finally {
                        if (avatarLoading) {
                            avatarLoading.classList.add('hidden');
                        }
                        this.disabled = false;
                    }
                });

                const container = avatarInput.closest('.flex-1').querySelector('.flex');
                container.appendChild(button);
            }

            // Обработка выбора файла
            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];

                    if (file) {
                        // Проверка размера файла (5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Файл слишком большой. Максимальный размер: 5MB');
                            this.value = '';
                            return;
                        }

                        // Проверка типа файла
                        if (!file.type.match('image.*')) {
                            alert('Пожалуйста, выберите изображение');
                            this.value = '';
                            return;
                        }

                        // Показать имя файла
                        fileName.textContent = file.name;

                        // Показать превью
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.innerHTML = '';
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('w-full', 'h-full', 'object-cover');
                            avatarPreview.appendChild(img);

                            // Показываем кнопку удаления
                            const existingBtn = document.getElementById('remove-avatar-btn') || document.getElementById('remove-avatar-btn-new');
                            if (!existingBtn) {
                                createRemoveButton();
                            } else {
                                existingBtn.style.display = 'flex';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Функция показа уведомлений
            function showNotification(message, type = 'success') {
                // Создаем элемент уведомления
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-transform duration-300 ${
                    type === 'success'
                        ? 'bg-green-500 text-white'
                        : 'bg-red-500 text-white'
                }`;
                notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span class="text-sm">${message}</span>
            </div>
        `;

                // Добавляем в DOM
                document.body.appendChild(notification);

                // Для успешных уведомлений удаляем через 1.5 секунды (перед обновлением страницы)
                // Для ошибок - через 5 секунд
                const timeout = type === 'success' ? 1500 : 5000;

                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, timeout);
            }
        });
    </script>
@endsection
