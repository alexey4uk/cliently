@extends('layouts.user')

@section('title', 'Добавление мастера - Cliently')
@section('page-title', 'Добавление мастера')
@section('page-description', 'Информация о мастере')

@section('content')
    <!-- Индикатор прогресса -->
    <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 mb-6">
        <div class="w-full md:w-auto">
            <div class="flex items-center w-full md:w-auto md:gap-1.5">
                @for($i = 1; $i <= 4; $i++)
                    <div class="flex items-center {{ $i < 4 ? 'flex-1 md:flex-none' : 'flex-shrink-0' }}">
                        <div class="flex items-center justify-center w-6 md:w-7 h-6 md:h-7 rounded-full text-xs font-semibold transition-colors flex-shrink-0 {{ $i <= 4 ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $i }}
                        </div>
                        @if($i < 4)
                            <div class="flex-1 md:w-6 md:flex-none h-0.5 mx-1 md:mx-0 {{ $i <= 4 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
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
                    <i class="fa-solid fa-user-check text-white text-sm"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base md:text-sm font-semibold text-indigo-900 dark:text-indigo-100 mb-1 md:mb-2">
                    Кто такой мастер?
                </h3>
                <p class="text-base md:text-sm text-indigo-800 dark:text-indigo-300 mb-2 md:mb-3">
                    Специалист, который оказывает услуги
                </p>
                <div class="space-y-1.5 md:space-y-2">
                    <div class="flex items-start gap-1.5 md:gap-2">
                        <i class="fa-solid fa-check-circle text-indigo-600 dark:text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            Добавьте хотя бы одного мастера
                        </p>
                    </div>
                    <div class="flex items-start gap-1.5 md:gap-2">
                        <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            Остальные — позже
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.master.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Имя мастера*</span>
                </label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Анна Иванова"
                       autofocus>
                @error('name')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="specialization" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-briefcase text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Специализация*</span>
                </label>
                <input type="text" id="specialization" name="specialization" required value="{{ old('specialization') }}"
                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Парикмахер, барбер, косметолог">
                @error('specialization')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Описание (необязательно)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Опыт работы, образование, достижения...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Телефон*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="+375 (29) 123-45-67">
                    @error('phone')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p id="phoneError" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                </div>

                <div>
                    <label for="email" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Почта (необязательно)</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="anna@example.com">
                    @error('email')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
            <button type="submit"
                        class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Завершить настройку <i class="fa-solid fa-check ml-1.5 md:ml-2"></i>
                </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Обработка телефона
            setupPhoneInput();
        });

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
    </script>
@endpush
