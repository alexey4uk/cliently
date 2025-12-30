@extends('layouts.user')

@section('title', 'Редактирование клиента - Cliently')
@section('page-title', 'Редактирование клиента')
@section('page-description', 'Изменение данных клиента')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => 'Редактирование', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-4 md:space-y-6">
    @csrf
    @method('PATCH')

                    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Основная информация
                </h2>
            </div>
                            </div>
        <div class="p-4 md:p-6 space-y-4 md:space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                            <div>
                    <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Имя <span class="text-rose-500">*</span>
                                </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" id="first_name" name="first_name" required value="{{ old('first_name', $client->first_name) }}"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               autofocus>
                    </div>
                        @error('first_name')
                    <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                            </div>

                            <div>
                    <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Фамилия
                                </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $client->last_name) }}"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                    </div>
                        @error('last_name')
                    <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Контактная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Контактная информация
                </h2>
            </div>
                        </div>
        <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                    Телефон <span class="text-rose-500">*</span>
                            </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-phone text-slate-400 text-sm"></i>
                    </div>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone', $client->phone) }}"
                           class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="+375 (XX) XXX-XX-XX">
                </div>
                    @error('phone')
                <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                <p class="mt-1.5 sm:mt-2 text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">
                    Формат: +375 (XX) XXX-XX-XX
                </p>
                </div>

                <div>
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                    Email
                    </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}"
                           class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="client@example.com">
                </div>
                    @error('email')
                <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

    <!-- Кнопки действий -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('clients.index') }}"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Отмена</span>
        </a>
                <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
            <i class="fa-solid fa-check text-xs"></i>
            <span>Сохранить изменения</span>
                </button>
    </div>
</form>

@endsection

@push('scripts')
    <script>
        // ==================== ОБРАБОТКА ТЕЛЕФОНА ====================
        const phoneInput = document.getElementById('phone');

        if (phoneInput) {
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

            // Валидные коды операторов Беларуси
            const validOperatorCodes = ['29', '33', '44', '25'];

            // Обработка ввода: только цифры, ограничение количества и проверка кода оператора
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value;

                if (!value.startsWith('+375')) {
                    value = '+375';
                }

                const digits = value.substring(4).replace(/\D/g, '');

                // Проверяем код оператора при вводе первых 2 цифр
                if (digits.length >= 2) {
                    const operatorCode = digits.substring(0, 2);
                    if (!validOperatorCodes.includes(operatorCode)) {
                        const firstDigit = digits.substring(0, 1);
                        const canBeValid = validOperatorCodes.some(code => code.startsWith(firstDigit));

                        if (!canBeValid) {
                            e.target.value = '+375';
                            e.target.setSelectionRange(5, 5);
                            return;
                        } else {
                            const limitedDigits = firstDigit;
                            e.target.value = '+375' + limitedDigits;
                            e.target.setSelectionRange(5 + limitedDigits.length, 5 + limitedDigits.length);
                            return;
                        }
                    }
                } else if (digits.length === 1) {
                    const firstDigit = digits;
                    const canBeValid = validOperatorCodes.some(code => code.startsWith(firstDigit));
                    if (!canBeValid) {
                        e.target.value = '+375';
                        e.target.setSelectionRange(5, 5);
                        return;
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
