@extends('layouts.user')

@section('title', 'Добавление клиента - Cliently')
@section('page-title', 'Новый клиент')
@section('page-description', 'Добавление нового клиента в базу')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => 'Добавление клиента', 'url' => null],
    ]" />
@endpush

@section('content')

    <!-- Заголовок страницы с приглушенным фоном -->
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-100 via-slate-200 to-slate-100 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800 shadow-md mb-6">
        <div class="absolute inset-0 bg-black/5"></div>
        <div class="relative p-6 md:p-8">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div
                        class="h-14 w-14 md:h-16 md:w-16 rounded-2xl bg-white/40 dark:bg-slate-600/30 backdrop-blur-sm flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-2xl md:text-3xl text-slate-700 dark:text-slate-200"></i>
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 mb-1">
                        Новый клиент
                    </h1>
                    <p class="text-slate-600 dark:text-slate-300 text-sm md:text-base">
                        Заполните информацию о новом клиенте для быстрого создания записи
                    </p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-6">
            <!-- Карточка основной информации -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <!-- Заголовок карточки -->
                <div
                    class="px-5 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800/30">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <span>Основная информация</span>
                    </h2>
                </div>

                <!-- Поля формы -->
                <div class="p-5 md:p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Имя -->
                        <div class="space-y-2">
                            <label for="first_name"
                                class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-user text-slate-400 dark:text-slate-500"></i>
                                Имя <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="first_name" name="first_name" required
                                    value="{{ old('first_name') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl {{ $errors->has('first_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-0 focus:ring-transparent ring-0 ring-transparent focus:border-indigo-500 focus:dark:border-indigo-400 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="Введите имя клиента" autofocus>
                                @if ($errors->has('first_name'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                    </div>
                                @endif
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Фамилия -->
                        <div class="space-y-2">
                            <label for="last_name"
                                class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-user-tag text-slate-400 dark:text-slate-500"></i>
                                Фамилия
                            </label>
                            <div class="relative">
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl {{ $errors->has('last_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-0 focus:ring-transparent ring-0 ring-transparent focus:border-indigo-500 focus:dark:border-indigo-400 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="Введите фамилию">
                                @if ($errors->has('last_name'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                    </div>
                                @endif
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Карточка контактной информации -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div
                    class="px-5 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800/30">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20">
                            <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-400 text-lg"></i>
                        </div>
                        <span>Контактная информация</span>
                    </h2>
                </div>

                <div class="p-5 md:p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Телефон -->
                        <div class="space-y-2">
                            <label for="phone"
                                class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-phone text-slate-400 dark:text-slate-500"></i>
                                Телефон <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl {{ $errors->has('phone') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-0 focus:ring-transparent ring-0 ring-transparent focus:border-indigo-500 focus:dark:border-indigo-400 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="+375XXXXXXXXX">
                                @if ($errors->has('phone'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                    </div>
                                @endif
                            </div>
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Формат: +375XXXXXXXXX
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email"
                                class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500"></i>
                                Email
                            </label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl {{ $errors->has('email') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-0 focus:ring-transparent ring-0 ring-transparent focus:border-indigo-500 focus:dark:border-indigo-400 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="client@example.com">
                                @if ($errors->has('email'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                    </div>
                                @endif
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Карточка дополнительной информации -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div
                    class="px-5 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800/30">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20">
                            <i class="fa-solid fa-clipboard-list text-amber-600 dark:text-amber-400 text-lg"></i>
                        </div>
                        <span>Дополнительная информация</span>
                    </h2>
                </div>

                <div class="p-5 md:p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Примечание -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="note"
                                class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-note-sticky text-slate-400 dark:text-slate-500"></i>
                                Примечание
                            </label>
                            <textarea id="note" name="note" rows="3"
                                class="w-full px-4 py-2.5 text-sm rounded-xl {{ $errors->has('note') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-0 focus:ring-transparent ring-0 ring-transparent focus:border-indigo-500 focus:dark:border-indigo-400 transition-all duration-200 placeholder:text-slate-400 resize-none"
                                placeholder="Добавьте примечание о клиенте (необязательно)"></textarea>
                            @error('note')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div
            class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('clients.index') }}"
                class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Отмена
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-white bg-slate-700 dark:bg-slate-600 border border-slate-600 dark:border-slate-500 rounded-xl shadow-sm hover:bg-slate-800 dark:hover:bg-slate-700 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-slate-500/30 flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Создать клиента
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
