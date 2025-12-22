@extends('layouts.user')

@section('title', 'Добавление мастера - Cliently')
@section('page-title', 'Добавление мастера')
@section('page-description', 'Добавьте нового мастера для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => route('settings.masters')],
        ['title' => 'Добавление', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('settings.masters.store') }}" class="space-y-6">
    @csrf

    <div class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                        Основная информация
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="first_name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Имя*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               autofocus>
                        @error('first_name')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Фамилия</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('last_name')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="specialization" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Специализация*</span>
                    </label>
                    <input type="text" id="specialization" name="specialization" required value="{{ old('specialization') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                    @error('specialization')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Описание</span>
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                        Контактная информация
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="phone" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Телефон*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        <p class="mt-2.5 text-xs text-slate-500 dark:text-slate-400">
                            Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                        </p>
                        @error('phone')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p id="phoneError" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                    </div>

                    <div>
                        <label for="email" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Почта</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('email')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Локации и услуги -->
        @if($locations->count() > 0 || $services->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-link text-indigo-600 dark:text-indigo-400"></i>
                        Связи
                    </h3>
                </div>
                
                @if($locations->count() > 0)
                <div>
                    <label class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Локации</span>
                    </label>
                    <div class="space-y-2">
                        @foreach($locations as $location)
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <input type="checkbox" name="location_ids[]" value="{{ $location->id }}"
                                       class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                       {{ in_array($location->id, old('location_ids', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $location->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($services->count() > 0)
                <div>
                    <label class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Услуги</span>
                    </label>
                    <div class="space-y-2">
                        @foreach($services as $service)
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                       class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                       {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Время работы -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                        Время работы
                    </h3>
                </div>
                
                <div>
                    <label class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
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
                                <label for="workingHoursFrom" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">С</label>
                                <input type="time" name="working_hours[from]" id="workingHoursFrom" required
                                       value="{{ old('working_hours.from', '09:00') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('working_hours.from')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="workingHoursTo" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">До</label>
                                <input type="time" name="working_hours[to]" id="workingHoursTo" required
                                       value="{{ old('working_hours.to', '18:00') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                @error('working_hours.to')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Выходные дни -->
                        <div>
                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-2 font-medium">Выходные дни</label>
                            @php
                                $days = [
                                    'monday' => 'Понедельник',
                                    'tuesday' => 'Вторник',
                                    'wednesday' => 'Среда',
                                    'thursday' => 'Четверг',
                                    'friday' => 'Пятница',
                                    'saturday' => 'Суббота',
                                    'sunday' => 'Воскресенье'
                                ];
                                $oldDaysOff = old('working_hours.days_off', []);
                            @endphp
                            
                            <!-- Кнопка для раскрытия блока -->
                            <button type="button" id="daysOffToggle" class="w-full md:w-auto flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <i class="fa-solid fa-plus text-xs text-indigo-600 dark:text-indigo-400" id="daysOffIcon"></i>
                                <span>Добавить выходные дни</span>
                            </button>
                            
                            <!-- Раскрывающийся блок с чекбоксами -->
                            <div id="daysOffDropdown" class="hidden mt-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-md border border-slate-200 dark:border-slate-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($days as $dayKey => $dayName)
                                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-colors days-off-checkbox-label" data-day="{{ $dayKey }}">
                                            <input type="checkbox" class="days-off-checkbox rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                                   data-day="{{ $dayKey }}"
                                                   {{ in_array($dayKey, $oldDaysOff) ? 'checked' : '' }}>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $dayName }}</span>
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
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('settings.masters') }}" 
           class="px-4 py-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            Отмена
        </a>
        <button type="submit"
            class="px-4 py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Сохранить
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка телефона
        setupPhoneInput();
        // Обработка круглосуточного режима
        setupWorkingHours();
        // Обработка выходных дней
        setupDaysOff();
    });

    // Обработка телефона
    function setupPhoneInput() {
        const phoneInput = document.getElementById('phone');
        if (!phoneInput) return;

        const validOperatorCodes = ['29', '33', '44', '25'];
        const PHONE_OPERATOR_ERROR = 'Неверный код оператора. Допустимые: 29, 33, 44, 25';
        const PHONE_INCOMPLETE_ERROR = 'Введите полный номер телефона (9 цифр после +375)';
        const PHONE_REQUIRED_DIGITS = 9;

        function extractPhoneDigits(value) {
            return value.substring(4).replace(/\D/g, '');
        }

        function resetPhoneToPrefix(input) {
            input.value = '+375';
            setCursorPosition(input, 5);
            showPhoneError(PHONE_OPERATOR_ERROR);
        }

        function setCursorPosition(input, position) {
            requestAnimationFrame(() => {
                const safePosition = Math.max(0, Math.min(position, input.value.length));
                try {
                    input.setSelectionRange(safePosition, safePosition);
                } catch (e) {
                    console.warn('Не удалось установить позицию курсора:', e);
                }
            });
        }

        function canBeValidOperatorCode(firstDigit) {
            return validOperatorCodes.some(code => code.startsWith(firstDigit));
        }

        function validateOperatorCode(digits, input) {
            if (digits.length < 2) {
                if (digits.length === 1) {
                    const firstDigit = digits;
                    if (!canBeValidOperatorCode(firstDigit)) {
                        resetPhoneToPrefix(input);
                        return false;
                    }
                }
                return true;
            }

            const operatorCode = digits.substring(0, 2);
            if (!validOperatorCodes.includes(operatorCode)) {
                const firstDigit = digits.substring(0, 1);
                if (!canBeValidOperatorCode(firstDigit)) {
                    resetPhoneToPrefix(input);
                    return false;
                } else {
                    input.value = '+375' + firstDigit;
                    showPhoneError(PHONE_OPERATOR_ERROR);
                    setCursorPosition(input, 5 + firstDigit.length);
                    return false;
                }
            }

            return true;
        }

        function showPhoneError(message) {
            const errorElement = document.getElementById('phoneError');
            if (errorElement && phoneInput) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                phoneInput.classList.add('border-rose-500');
            }
        }

        function hidePhoneError() {
            const errorElement = document.getElementById('phoneError');
            if (errorElement && phoneInput) {
                errorElement.classList.add('hidden');
                phoneInput.classList.remove('border-rose-500');
            }
        }

        phoneInput.addEventListener('focus', function(e) {
            if (!e.target.value || !e.target.value.startsWith('+375')) {
                e.target.value = '+375';
                setCursorPosition(e.target, 4);
            }
        });

        phoneInput.addEventListener('keydown', function(e) {
            const selectionStart = e.target.selectionStart;
            const selectionEnd = e.target.selectionEnd;
            
            if (selectionStart < 5 || selectionEnd < 5) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    e.preventDefault();
                    setCursorPosition(e.target, 5);
                    return false;
                }
            }
        });

        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            if (!value.startsWith('+375')) {
                value = '+375';
            }
            
            const digits = extractPhoneDigits(value);
            
            if (!validateOperatorCode(digits, e.target)) {
                return;
            }
            
            const limitedDigits = digits.substring(0, PHONE_REQUIRED_DIGITS);
            e.target.value = '+375' + limitedDigits;
            
            if (limitedDigits.length === PHONE_REQUIRED_DIGITS) {
                hidePhoneError();
            }
            
            const cursorPosition = Math.max(5, e.target.value.length);
            setCursorPosition(e.target, cursorPosition);
        });

        phoneInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            const digits = extractPhoneDigits(value);
            
            if (value.startsWith('+375') && digits.length < PHONE_REQUIRED_DIGITS) {
                showPhoneError(PHONE_INCOMPLETE_ERROR);
            } else if (digits.length === PHONE_REQUIRED_DIGITS) {
                hidePhoneError();
            }
        });
    }

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

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedDays.add(checkbox.dataset.day);
            }
        });
        updateTags();
        updateHiddenInputs();

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

        function updateTags() {
            tagsContainer.innerHTML = '';
            if (selectedDays.size === 0) {
                tagsContainer.classList.add('hidden');
                return;
            }
            tagsContainer.classList.remove('hidden');
            
            selectedDays.forEach(day => {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-md text-xs font-medium';
                tag.innerHTML = `
                    <span>${daysMap[day]}</span>
                    <button type="button" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors" data-day="${day}">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                `;
                tagsContainer.appendChild(tag);
                
                const removeButton = tag.querySelector('button');
                removeButton.addEventListener('click', function() {
                    const dayToRemove = this.dataset.day;
                    selectedDays.delete(dayToRemove);
                    const checkbox = document.querySelector(`.days-off-checkbox[data-day="${dayToRemove}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                    updateTags();
                    updateHiddenInputs();
                });
            });
        }

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

