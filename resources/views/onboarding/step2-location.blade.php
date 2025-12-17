@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Добавление локации</h1>
        </div>
        
        <!-- Индикатор прогресса -->
        <div class="w-full md:w-auto">
            <div class="flex items-center w-full md:w-auto md:gap-1.5">
                @for($i = 1; $i <= 4; $i++)
                    <div class="flex items-center {{ $i < 4 ? 'flex-1 md:flex-none' : 'flex-shrink-0' }}">
                        <div class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold transition-colors flex-shrink-0 {{ $i <= 2 ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $i }}
                        </div>
                        @if($i < 4)
                            <div class="flex-1 md:w-6 md:flex-none h-0.5 mx-1 md:mx-0 {{ $i < 2 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Информационная карточка -->
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 dark:border-indigo-900 dark:bg-indigo-900/30 p-3 md:p-4 mb-6">
        <div class="flex items-start gap-2 md:gap-3">
            <div class="hidden md:flex flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center">
                    <i class="fa-solid fa-store text-white text-sm"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-xs md:text-sm font-semibold text-indigo-900 dark:text-indigo-100 mb-1 md:mb-2">
                    Что такое локация?
                </h3>
                <p class="text-xs md:text-sm text-indigo-800 dark:text-indigo-300 mb-2 md:mb-3">
                    Место, где вы оказываете услуги (салон, студия, офис).
                </p>
                <div class="space-y-1.5 md:space-y-2">
                    <div class="flex items-start gap-1.5 md:gap-2">
                        <i class="fa-solid fa-check-circle text-indigo-600 dark:text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            <span class="font-medium">Обязательно:</span> добавьте хотя бы одну локацию
                        </p>
                    </div>
                    <div class="flex items-start gap-1.5 md:gap-2">
                        <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            Больше локаций можно добавить позже
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.location.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="name" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-map-marker-alt text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Название локации*</span>
                </label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-3 py-2.5 text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Основной салон"
                       autofocus>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 hidden" id="nameTooltip">
                    Введите название вашей локации (салона, студии и т.д.)
                </p>
                @error('name')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Адрес*</span>
                </label>
                <input type="text" id="address" name="address" required value="{{ old('address') }}"
                       class="w-full px-3 py-2.5 text-sm rounded-md border {{ $errors->has('address') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="ул. Пушкинская, д. 10">
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 hidden" id="addressTooltip">
                    Введите полный адрес локации для клиентов
                </p>
                @error('address')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Описание</span>
                </label>
                <div class="relative">
                    <textarea id="description" name="description" rows="3" maxlength="500"
                          class="w-full px-3 py-2.5 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Описание локации...">{{ old('description') }}</textarea>
                    <div class="absolute bottom-2 right-2 flex items-center gap-1">
                        <span id="descriptionCount" class="text-xs text-slate-400 dark:text-slate-500">0</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">/</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">500</span>
                    </div>
                </div>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 hidden" id="descriptionTooltip">
                    Необязательное поле. Добавьте описание локации для клиентов.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Телефон*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-3 py-2.5 text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="+375 (29) 123-45-67">
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 hidden" id="phoneTooltip">
                        Введите номер телефона в формате +375XXXXXXXXX. Коды операторов: 29, 33, 44, 25.
                    </p>
                    @error('phone')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p id="phoneError" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                </div>

                <div>
                    <label for="email" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Почта</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2.5 text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="salon@example.com">
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 hidden" id="emailTooltip">
                        Необязательное поле. Email для связи с локацией.
                    </p>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Время работы -->
            <div>
                <label class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Время работы*</span>
                </label>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                    Укажите время работы для всех дней недели. Разные часы для разных дней можно настроить позже.
                </p>
                <div class="space-y-3">
                    <!-- Чекбокс круглосуточно -->
                    <div class="flex items-center gap-2 p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                        <label class="flex items-center gap-2 cursor-pointer flex-1">
                            <input type="checkbox" id="workingHours24" name="working_hours[24_hours]" value="1"
                                   class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                                   {{ old('working_hours.24_hours') ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">Круглосуточно</span>
                        </label>
                    </div>

                    <!-- Поля времени работы -->
                    <div id="workingHoursFields" class="flex items-start gap-4 p-4 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">С</label>
                            <select name="working_hours[from]" id="workingHoursFrom" required
                                    class="w-full px-3 py-2.5 text-sm rounded-md border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                <option value="">--:--</option>
                                @for($i = 6; $i <= 23; $i++)
                                    <option value="{{ sprintf('%02d:00', $i) }}" {{ old('working_hours.from') == sprintf('%02d:00', $i) ? 'selected' : ($i == 9 ? 'selected' : '') }}>
                                        {{ sprintf('%02d:00', $i) }}
                                    </option>
                                @endfor
                            </select>
                            @error('working_hours.from')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">До</label>
                            <select name="working_hours[to]" id="workingHoursTo" required
                                    class="w-full px-3 py-2.5 text-sm rounded-md border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                <option value="">--:--</option>
                                @for($i = 8; $i <= 23; $i++)
                                    <option value="{{ sprintf('%02d:00', $i) }}" {{ old('working_hours.to') == sprintf('%02d:00', $i) ? 'selected' : ($i == 18 ? 'selected' : '') }}>
                                        {{ sprintf('%02d:00', $i) }}
                                    </option>
                                @endfor
                                <option value="00:00" {{ old('working_hours.to') == '00:00' ? 'selected' : '' }}>00:00</option>
                            </select>
                            @error('working_hours.to')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @error('working_hours')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Сохранить и продолжить <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Подсказки при фокусе
            setupTooltips();
            // Счетчик символов для описания
            setupDescriptionCounter();
            // Обработка телефона
            setupPhoneInput();
            // Обработка круглосуточного режима
            setupWorkingHours();
        });

        // Подсказки при фокусе
        function setupTooltips() {
            const fields = [
                { id: 'name', tooltipId: 'nameTooltip' },
                { id: 'address', tooltipId: 'addressTooltip' },
                { id: 'phone', tooltipId: 'phoneTooltip' },
                { id: 'email', tooltipId: 'emailTooltip' },
                { id: 'description', tooltipId: 'descriptionTooltip' }
            ];

            fields.forEach(({ id, tooltipId }) => {
                const field = document.getElementById(id);
                const tooltip = document.getElementById(tooltipId);
                
                if (field && tooltip) {
                    field.addEventListener('focus', () => {
                        tooltip.classList.remove('hidden');
                    });
                    
                    field.addEventListener('blur', () => {
                        tooltip.classList.add('hidden');
                    });
                }
            });
        }

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
        function setupPhoneInput() {
            const phoneInput = document.getElementById('phone');
            if (!phoneInput) return;

            // Валидные коды операторов Беларуси
            const validOperatorCodes = ['29', '33', '44', '25'];

            // Автоподстановка +375 при фокусе
            phoneInput.addEventListener('focus', function(e) {
                if (!e.target.value || !e.target.value.startsWith('+375')) {
                    e.target.value = '+375';
                    setTimeout(() => {
                        e.target.setSelectionRange(4, 4);
                    }, 0);
                }
            });

            // Защита от удаления +375
            phoneInput.addEventListener('keydown', function(e) {
                const selectionStart = e.target.selectionStart;
                const selectionEnd = e.target.selectionEnd;
                
                if (selectionStart < 5 || selectionEnd < 5) {
                    if (e.key === 'Backspace' || e.key === 'Delete') {
                        e.preventDefault();
                        e.target.setSelectionRange(5, 5);
                        return false;
                    }
                }
            });

            // Показать ошибку
            function showPhoneError(message) {
                const errorElement = document.getElementById('phoneError');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                    phoneInput.classList.add('border-rose-500');
                }
            }

            // Скрыть ошибку
            function hidePhoneError() {
                const errorElement = document.getElementById('phoneError');
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    phoneInput.classList.remove('border-rose-500');
                }
            }

            // Обработка ввода
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value;
                
                if (!value.startsWith('+375')) {
                    value = '+375';
                }
                
                const digits = value.substring(4).replace(/\D/g, '');
                
                // Проверка кода оператора
                if (digits.length >= 2) {
                    const operatorCode = digits.substring(0, 2);
                    if (!validOperatorCodes.includes(operatorCode)) {
                        const firstDigit = digits.substring(0, 1);
                        const canBeValid = validOperatorCodes.some(code => code.startsWith(firstDigit));
                        
                        if (!canBeValid) {
                            e.target.value = '+375';
                            showPhoneError('Неверный код оператора. Допустимые: 29, 33, 44, 25');
                            e.target.setSelectionRange(5, 5);
                            return;
                        } else {
                            const limitedDigits = firstDigit;
                            e.target.value = '+375' + limitedDigits;
                            showPhoneError('Неверный код оператора. Допустимые: 29, 33, 44, 25');
                            e.target.setSelectionRange(5 + limitedDigits.length, 5 + limitedDigits.length);
                            return;
                        }
                    } else {
                        hidePhoneError();
                    }
                } else {
                    if (digits.length === 1) {
                        const firstDigit = digits;
                        const canBeValid = validOperatorCodes.some(code => code.startsWith(firstDigit));
                        if (!canBeValid) {
                            e.target.value = '+375';
                            showPhoneError('Неверный код оператора. Допустимые: 29, 33, 44, 25');
                            e.target.setSelectionRange(5, 5);
                            return;
                        } else {
                            hidePhoneError();
                        }
                    } else {
                        hidePhoneError();
                    }
                }
                
                // Ограничиваем до 9 цифр
                const limitedDigits = digits.substring(0, 9);
                e.target.value = '+375' + limitedDigits;
                
                const cursorPosition = Math.max(5, e.target.value.length);
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            });
        }

        // Обработка круглосуточного режима работы
        function setupWorkingHours() {
            const checkbox24 = document.getElementById('workingHours24');
            const fieldsContainer = document.getElementById('workingHoursFields');
            const fromSelect = document.getElementById('workingHoursFrom');
            const toSelect = document.getElementById('workingHoursTo');

            if (!checkbox24 || !fieldsContainer || !fromSelect || !toSelect) return;

            function toggleFields() {
                if (checkbox24.checked) {
                    fieldsContainer.classList.add('opacity-50', 'pointer-events-none');
                    fromSelect.disabled = true;
                    toSelect.disabled = true;
                    fromSelect.removeAttribute('required');
                    toSelect.removeAttribute('required');
                    fromSelect.value = '00:00';
                    toSelect.value = '00:00';
                } else {
                    fieldsContainer.classList.remove('opacity-50', 'pointer-events-none');
                    fromSelect.disabled = false;
                    toSelect.disabled = false;
                    fromSelect.setAttribute('required', 'required');
                    toSelect.setAttribute('required', 'required');
                }
            }

            checkbox24.addEventListener('change', toggleFields);
            
            // Инициализация при загрузке
            toggleFields();
        }
    </script>
@endpush
