@extends('layouts.user')

@section('title', 'Профиль пользователя - Cliently')
@section('page-title', 'Профиль')
@section('page-description', 'Управление личными данными')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Профиль', 'url' => null]
    ]" />
@endpush

@section('content')

    <!-- Основная информация -->
    <section class="mb-6">
        <div
            class="rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-sm p-4 md:p-6">
            <div
                class="flex flex-col md:flex-row items-start md:items-center gap-6 pb-6 border-b border-slate-200 dark:border-slate-800 mb-6">
                <!-- Аватар -->
                <div class="relative">
                    <div id="avatarPreview"
                        class="h-24 w-24 md:h-20 md:w-20 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-3xl md:text-2xl font-semibold text-slate-600 dark:text-slate-400 border-4 border-slate-300 dark:border-slate-700 overflow-hidden">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <label for="avatar"
                        class="absolute bottom-0 right-0 h-8 w-8 md:h-7 md:w-7 rounded-full bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center text-white cursor-pointer hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-sm border-2 border-white dark:border-slate-900">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </label>
                    @if ($user->avatar)
                        <button type="button" id="removeAvatar"
                            class="absolute top-0 right-0 h-6 w-6 rounded-full bg-rose-500 flex items-center justify-center text-white cursor-pointer hover:bg-rose-600 transition-colors shadow-sm border-2 border-white dark:border-slate-900">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    @endif
                </div>

                <!-- Информация -->
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white mb-1">{{ $user->name }}
                    </h2>
                    <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mb-2">{{ $user->email }}</p>
                    @if ($user->phone)
                        <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-phone mr-1.5"></i>{{ $user->phone }}
                        </p>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Скрытое поле для загрузки аватара -->
                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" />

                <!-- Имя -->
                <div>
                    <label for="name"
                        class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Имя*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Ваше имя" />
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email"
                        class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Email*</span>
                    </label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="your@email.com" />
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Телефон -->
                <div>
                    <label for="phone"
                        class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Телефон*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors" />
                    @error('phone')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Скрытое поле для удаления аватара -->
                <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">

                <!-- Кнопка сохранения -->
                <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit" id="submitButton"
                        class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Смена пароля -->
    <section>
        <div
            class="rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-sm p-4 md:p-6">
            <h2 class="text-base md:text-sm font-semibold text-slate-900 dark:text-white mb-4">Безопасность</h2>

            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Текущий пароль -->
                <div>
                    <label for="current_password"
                        class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-lock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Текущий пароль*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('current_password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Введите текущий пароль" />
                    @error('current_password')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Новый пароль -->
                <div>
                    <label for="password"
                        class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-lock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Новый пароль*</span>
                    </label>
                    <input type="password" id="password" name="password"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Введите новый пароль" />
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Подтверждение нового пароля -->
                <div>
                    <label for="password_confirmation"
                        class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-lock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Подтверждение нового пароля*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Повторите новый пароль" />
                </div>

                <!-- Кнопка сохранения -->
                <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit"
                        class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Изменить пароль
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // ==================== ОБРАБОТКА АВАТАРА ====================

        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');
        const removeAvatarBtn = document.getElementById('removeAvatar');
        const removeAvatarInput = document.getElementById('remove_avatar');
        const profileForm = document.querySelector('form[action="{{ route('profile.update') }}"]');

        // Предпросмотр аватара при выборе файла
        if (avatarInput) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Проверяем размер файла (5 МБ)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Размер файла не должен превышать 5 МБ');
                        e.target.value = '';
                        return;
                    }

                    // Проверяем тип файла
                    if (!file.type.match('image.*')) {
                        alert('Файл должен быть изображением');
                        e.target.value = '';
                        return;
                    }

                    // Показываем предпросмотр
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.innerHTML =
                            `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                        if (removeAvatarBtn) {
                            removeAvatarBtn.style.display = 'flex';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Удаление аватара
        if (removeAvatarBtn) {
            removeAvatarBtn.addEventListener('click', function() {
                if (confirm('Удалить аватар?')) {
                    removeAvatarInput.value = '1';
                    avatarPreview.innerHTML = `{{ strtoupper(mb_substr($user->name, 0, 2)) }}`;
                    if (avatarInput) {
                        avatarInput.value = '';
                    }
                    this.style.display = 'none';
                    // Отправляем форму для удаления аватара
                    if (profileForm) {
                        profileForm.submit();
                    }
                }
            });
        }

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
