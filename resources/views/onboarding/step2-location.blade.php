@extends('layouts.user')

@section('title', 'Добавление локации - Cliently')
@section('page-title', 'Добавление локации')
@section('page-description', 'Информация о месте работы')

@section('content')
    <!-- Индикатор прогресса -->
    <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 mb-6">
        <!-- Индикатор прогресса -->
        <div class="w-full md:w-auto">
            <div class="flex items-center w-full md:w-auto md:gap-1.5">
                @php
                    $steps = [
                        1 => 'Бизнес',
                        2 => 'Локация',
                        3 => 'Услуга',
                        4 => 'Мастер',
                    ];
                @endphp
                @for ($i = 1; $i <= 4; $i++)
                    <div class="flex items-center {{ $i < 4 ? 'flex-1 md:flex-none' : 'shrink-0' }}">
                        <div
                            class="flex items-center justify-center w-6 md:w-7 h-6 md:h-7 rounded-full text-xs font-semibold transition-colors shrink-0 {{ $i == 1 ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $i }}
                        </div>
                        @if ($i < 4)
                            <div
                                class="flex-1 md:w-6 md:flex-none h-0.5 mx-1 md:mx-0 {{ $i <= 1 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.location.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-6">
            <!-- Основная информация -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400"></i>
                            Основная информация
                        </h3>
                    </div>

                    <div>
                        <label for="name"
                            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Название локации*</span>
                        </label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                            autofocus>
                        @error('name')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Адрес -->
                    <div>
                        <label
                            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Адрес*</span>
                        </label>
                        <div class="space-y-4">
                            <!-- Город, улица, дом -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="city"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Город*</label>
                                    <input type="text" id="city" name="city" required value="{{ old('city') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('city') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('city')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="street"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Улица*</label>
                                    <input type="text" id="street" name="street" required
                                        value="{{ old('street') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('street') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('street')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="house"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Дом*</label>
                                    <input type="text" id="house" name="house" required
                                        value="{{ old('house') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('house') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('house')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <!-- Корпус, квартира -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="building"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Корпус</label>
                                    <input type="text" id="building" name="building" value="{{ old('building') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('building')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="apartment"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Квартира/Офис</label>
                                    <input type="text" id="apartment" name="apartment" value="{{ old('apartment') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('apartment')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Закрытие блока основной информации -->

            <!-- Контактная информация -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                            Контактная информация
                        </h3>
                    </div>

                    <div>
                        <label for="phone"
                            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Телефон*</span>
                        </label>
                        <livewire:phone-input />
                        <p class="mt-2.5 text-xs text-slate-500 dark:text-slate-400">
                            Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                        </p>
                        @error('phone')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p id="phoneError" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                    </div>
                </div>
            </div> <!-- Закрытие блока контактной информации -->

            <!-- Дополнительная информация -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400"></i>
                            Дополнительная информация
                        </h3>
                    </div>

                    <div>
                        <label for="description"
                            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Описание</span>
                        </label>
                        <div class="relative">
                            <textarea id="description" name="description" rows="3" maxlength="500"
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none">{{ old('description') }}</textarea>
                            <div class="absolute bottom-2 right-2 flex items-center gap-1">
                                <span id="descriptionCount" class="text-xs text-slate-400 dark:text-slate-500">0</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">/</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">500</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Закрытие блока дополнительной информации -->

            <!-- Время работы -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                            Время работы
                        </h3>
                    </div>

                    <div>
                        <label
                            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>График работы*</span>
                        </label>
                        <div class="space-y-3">
                            <!-- Чекбокс круглосуточно -->
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="workingHours24" name="working_hours[24_hours]" value="1"
                                    class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                    {{ old('working_hours.24_hours') ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">Круглосуточно</span>
                            </label>

                            <!-- Поля времени работы -->
                            <div id="workingHoursFields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="workingHoursFrom"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">С</label>
                                    <input type="time" name="working_hours[from]" id="workingHoursFrom" required
                                        value="{{ old('working_hours.from', '09:00') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('working_hours.from')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="workingHoursTo"
                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">До</label>
                                    <input type="time" name="working_hours[to]" id="workingHoursTo" required
                                        value="{{ old('working_hours.to', '18:00') }}"
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    @error('working_hours.to')
                                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Выходные дни -->
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-2 font-medium">Выходные
                                    дни</label>
                                @php
                                    $days = [
                                        'monday' => 'Понедельник',
                                        'tuesday' => 'Вторник',
                                        'wednesday' => 'Среда',
                                        'thursday' => 'Четверг',
                                        'friday' => 'Пятница',
                                        'saturday' => 'Суббота',
                                        'sunday' => 'Воскресенье',
                                    ];
                                    $oldDaysOff = old('working_hours.days_off', []);
                                @endphp

                                <!-- Кнопка для раскрытия блока -->
                                <button type="button" id="daysOffToggle"
                                    class="w-full md:w-auto flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <i class="fa-solid fa-plus text-xs text-indigo-600 dark:text-indigo-400"
                                        id="daysOffIcon"></i>
                                    <span>Добавить выходные дни</span>
                                </button>

                                <!-- Раскрывающийся блок с чекбоксами -->
                                <div id="daysOffDropdown"
                                    class="hidden mt-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-md border border-slate-200 dark:border-slate-700">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach ($days as $dayKey => $dayName)
                                            <label
                                                class="flex items-center gap-2 cursor-pointer p-2 rounded border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-colors days-off-checkbox-label"
                                                data-day="{{ $dayKey }}">
                                                <input type="checkbox"
                                                    class="days-off-checkbox rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                                    data-day="{{ $dayKey }}"
                                                    {{ in_array($dayKey, $oldDaysOff) ? 'checked' : '' }}>
                                                <span
                                                    class="text-sm text-slate-700 dark:text-slate-300">{{ $dayName }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Скрытые input'ы для отправки данных -->
                                <div id="daysOffHiddenInputs"></div>

                                <!-- Теги выбранных дней -->
                                <div id="daysOffTags" class="flex flex-wrap gap-2 mt-3"></div>
                            </div>
                        </div>
                        @error('working_hours')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div> <!-- Закрытие блока времени работы -->

            <!-- Кнопки действий -->
            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                <button type="submit"
                    class="px-3 md:px-4 py-1.5 md:py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Сохранить и продолжить <i class="fa-solid fa-arrow-right ml-1.5 md:ml-2"></i>
                </button>
            </div>
        </div> <!-- Закрытие div.space-y-6 -->
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Счетчик символов для описания
            setupDescriptionCounter();
            // Обработка телефона
            // setupPhoneInput();
            // Обработка круглосуточного режима
            setupWorkingHours();
            // Обработка выходных дней
            setupDaysOff();
        });

        // Счетчик символов для описания
        function setupDescriptionCounter() {
            const descriptionField = document.getElementById('description');
            const counter = document.getElementById('descriptionCount');

            if (descriptionField && counter) {
                function updateCounter() {
                    const length = descriptionField.value.length;
                    counter.textContent = length;

                    if (length > 450) {
                        counter.classList.add('text-amber-600', 'dark:text-amber-400');
                        counter.classList.remove('text-slate-400', 'dark:text-slate-500');
                    } else {
                        counter.classList.remove('text-amber-600', 'dark:text-amber-400');
                        counter.classList.add('text-slate-400', 'dark:text-slate-500');
                    }
                }

                descriptionField.addEventListener('input', updateCounter);
                updateCounter(); // Инициализация
            }
        }

        // Обработка телефона
        // function setupPhoneInput() {
        //     const phoneInput = document.getElementById('phone');
        //     if (!phoneInput) return;

        //     // Валидные коды операторов Беларуси
        //     const validOperatorCodes = ['29', '33', '44', '25'];
        //     const PHONE_OPERATOR_ERROR = 'Неверный код оператора. Допустимые: 29, 33, 44, 25';
        //     const PHONE_INCOMPLETE_ERROR = 'Введите полный номер телефона (9 цифр после +375)';
        //     const PHONE_REQUIRED_DIGITS = 9;

        //     /**
        //      * Извлечение цифр из номера телефона (после +375)
        //      */
        //     function extractPhoneDigits(value) {
        //         return value.substring(4).replace(/\D/g, '');
        //     }

        //     /**
        //      * Сброс телефона к префиксу +375 с установкой курсора и показом ошибки
        //      */
        //     function resetPhoneToPrefix(input) {
        //         input.value = '+375';
        //         setCursorPosition(input, 5);
        //         showPhoneError(PHONE_OPERATOR_ERROR);
        //     }

        //     /**
        //      * Установка позиции курсора с безопасной обработкой ошибок
        //      */
        //     function setCursorPosition(input, position) {
        //         requestAnimationFrame(() => {
        //             const safePosition = Math.max(0, Math.min(position, input.value.length));
        //             try {
        //                 input.setSelectionRange(safePosition, safePosition);
        //             } catch (e) {
        //                 console.warn('Не удалось установить позицию курсора:', e);
        //             }
        //         });
        //     }

        //     /**
        //      * Проверка валидности кода оператора
        //      */
        //     function canBeValidOperatorCode(firstDigit) {
        //         return validOperatorCodes.some(code => code.startsWith(firstDigit));
        //     }

        //     /**
        //      * Валидация и обработка кода оператора
        //      */
        //     function validateOperatorCode(digits, input) {
        //         if (digits.length < 2) {
        //             if (digits.length === 1) {
        //                 const firstDigit = digits;
        //                 if (!canBeValidOperatorCode(firstDigit)) {
        //                     resetPhoneToPrefix(input);
        //                     return false;
        //                 }
        //             }
        //             return true;
        //         }

        //         const operatorCode = digits.substring(0, 2);
        //         if (!validOperatorCodes.includes(operatorCode)) {
        //             const firstDigit = digits.substring(0, 1);
        //             if (!canBeValidOperatorCode(firstDigit)) {
        //                 resetPhoneToPrefix(input);
        //                 return false;
        //             } else {
        //                 input.value = '+375' + firstDigit;
        //                 showPhoneError(PHONE_OPERATOR_ERROR);
        //                 setCursorPosition(input, 5 + firstDigit.length);
        //                 return false;
        //             }
        //         }

        //         return true;
        //     }

        //     /**
        //      * Показать ошибку
        //      */
        //     function showPhoneError(message) {
        //         const errorElement = document.getElementById('phoneError');
        //         if (errorElement && phoneInput) {
        //             errorElement.textContent = message;
        //             errorElement.classList.remove('hidden');
        //             phoneInput.classList.add('border-rose-500');
        //         }
        //     }

        //     /**
        //      * Скрыть ошибку
        //      */
        //     function hidePhoneError() {
        //         const errorElement = document.getElementById('phoneError');
        //         if (errorElement && phoneInput) {
        //             errorElement.classList.add('hidden');
        //             phoneInput.classList.remove('border-rose-500');
        //         }
        //     }

        //     // Автоподстановка +375 при фокусе
        //     phoneInput.addEventListener('focus', function(e) {
        //         if (!e.target.value || !e.target.value.startsWith('+375')) {
        //             e.target.value = '+375';
        //             setCursorPosition(e.target, 4);
        //         }
        //     });

        //     // Защита от удаления +375
        //     phoneInput.addEventListener('keydown', function(e) {
        //         const selectionStart = e.target.selectionStart;
        //         const selectionEnd = e.target.selectionEnd;

        //         if (selectionStart < 5 || selectionEnd < 5) {
        //             if (e.key === 'Backspace' || e.key === 'Delete') {
        //                 e.preventDefault();
        //                 setCursorPosition(e.target, 5);
        //                 return false;
        //             }
        //         }
        //     });

        //     // Обработка ввода
        //     phoneInput.addEventListener('input', function(e) {
        //         let value = e.target.value;

        //         if (!value.startsWith('+375')) {
        //             value = '+375';
        //         }

        //         const digits = extractPhoneDigits(value);

        //         // Валидация кода оператора
        //         if (!validateOperatorCode(digits, e.target)) {
        //             return;
        //         }

        //         // Ограничиваем до 9 цифр
        //         const limitedDigits = digits.substring(0, PHONE_REQUIRED_DIGITS);
        //         e.target.value = '+375' + limitedDigits;

        //         // Если номер полный, скрываем ошибку
        //         if (limitedDigits.length === PHONE_REQUIRED_DIGITS) {
        //             hidePhoneError();
        //         }

        //         // Устанавливаем курсор в конец
        //         const cursorPosition = Math.max(5, e.target.value.length);
        //         setCursorPosition(e.target, cursorPosition);
        //     });

        //     // Проверка при потере фокуса
        //     phoneInput.addEventListener('blur', function(e) {
        //         const value = e.target.value;
        //         const digits = extractPhoneDigits(value);

        //         if (value.startsWith('+375') && digits.length < PHONE_REQUIRED_DIGITS) {
        //             showPhoneError(PHONE_INCOMPLETE_ERROR);
        //         } else if (digits.length === PHONE_REQUIRED_DIGITS) {
        //             hidePhoneError();
        //         }
        //     });
        // }

        // Обработка круглосуточного режима работы
        function setupWorkingHours() {
            const checkbox24 = document.getElementById('workingHours24');
            const fieldsContainer = document.getElementById('workingHoursFields');
            const fromInput = document.getElementById('workingHoursFrom');
            const toInput = document.getElementById('workingHoursTo');

            if (!checkbox24 || !fieldsContainer || !fromInput || !toInput) return;

            function toggleFields() {
                if (checkbox24.checked) {
                    fieldsContainer.classList.add('opacity-50', 'pointer-events-none');
                    fromInput.disabled = true;
                    toInput.disabled = true;
                    fromInput.removeAttribute('required');
                    toInput.removeAttribute('required');
                    fromInput.value = '00:00';
                    toInput.value = '00:00';
                } else {
                    fieldsContainer.classList.remove('opacity-50', 'pointer-events-none');
                    fromInput.disabled = false;
                    toInput.disabled = false;
                    fromInput.setAttribute('required', 'required');
                    toInput.setAttribute('required', 'required');
                }
            }

            checkbox24.addEventListener('change', toggleFields);

            // Инициализация при загрузке
            toggleFields();
        }

        // Обработка выходных дней
        function setupDaysOff() {
            const toggleButton = document.getElementById('daysOffToggle');
            const dropdown = document.getElementById('daysOffDropdown');
            const icon = document.getElementById('daysOffIcon');
            const checkboxes = document.querySelectorAll('.days-off-checkbox');
            const tagsContainer = document.getElementById('daysOffTags');
            const hiddenInputsContainer = document.getElementById('daysOffHiddenInputs');

            if (!toggleButton || !dropdown || !icon || !tagsContainer || !hiddenInputsContainer) return;

            const daysMap = {
                'monday': 'Понедельник',
                'tuesday': 'Вторник',
                'wednesday': 'Среда',
                'thursday': 'Четверг',
                'friday': 'Пятница',
                'saturday': 'Суббота',
                'sunday': 'Воскресенье'
            };

            let isExpanded = false;
            const selectedDays = new Set();

            // Инициализация: собираем уже выбранные дни
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedDays.add(checkbox.dataset.day);
                }
            });
            updateTags();
            updateHiddenInputs();

            // Переключение раскрытия блока
            toggleButton.addEventListener('click', function() {
                isExpanded = !isExpanded;
                if (isExpanded) {
                    dropdown.classList.remove('hidden');
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-chevron-up');
                } else {
                    dropdown.classList.add('hidden');
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-plus');
                }
            });

            // Обработка изменения чекбоксов
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const day = this.dataset.day;
                    if (this.checked) {
                        selectedDays.add(day);
                    } else {
                        selectedDays.delete(day);
                    }
                    updateTags();
                    updateHiddenInputs();
                });
            });

            // Обновление тегов
            function updateTags() {
                tagsContainer.innerHTML = '';
                if (selectedDays.size === 0) {
                    tagsContainer.classList.add('hidden');
                    return;
                }
                tagsContainer.classList.remove('hidden');

                selectedDays.forEach(day => {
                    const tag = document.createElement('div');
                    tag.className =
                        'inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-md text-xs font-medium';
                    tag.innerHTML = `
                        <span>${daysMap[day]}</span>
                        <button type="button" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors" data-day="${day}">
                            <i class="fa-solid fa-times text-xs"></i>
                        </button>
                    `;
                    tagsContainer.appendChild(tag);

                    // Обработка удаления тега
                    const removeButton = tag.querySelector('button');
                    removeButton.addEventListener('click', function() {
                        const dayToRemove = this.dataset.day;
                        selectedDays.delete(dayToRemove);
                        // Снимаем чекбокс
                        const checkbox = document.querySelector(
                            `.days-off-checkbox[data-day="${dayToRemove}"]`);
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                        updateTags();
                        updateHiddenInputs();
                    });
                });
            }

            // Обновление скрытых input'ов для отправки данных
            function updateHiddenInputs() {
                hiddenInputsContainer.innerHTML = '';
                selectedDays.forEach(day => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'working_hours[days_off][]';
                    input.value = day;
                    hiddenInputsContainer.appendChild(input);
                });
            }
        }
    </script>
@endpush
