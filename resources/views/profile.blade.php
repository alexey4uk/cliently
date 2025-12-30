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

<div x-data="{
    avatarPreview: null,
    showRemoveConfirm: false,
    userInitials: '{{ strtoupper(mb_substr($user->name, 0, 2)) }}',
    profileUpdateUrl: '{{ route('profile.update') }}',
    removeAvatar() {
        if (confirm('Удалить аватар?')) {
            document.getElementById('remove_avatar').value = '1';
            this.avatarPreview = null;
            document.getElementById('avatarPreview').innerHTML = this.userInitials;
            const removeBtn = document.getElementById('removeAvatarBtn');
            if (removeBtn) {
                removeBtn.style.display = 'none';
            }
            const form = document.querySelector('form[action=\'' + this.profileUpdateUrl + '\']');
            if (form) {
                form.submit();
            }
        }
    },
    handleAvatarChange(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Размер файла не должен превышать 5 МБ');
                event.target.value = '';
                return;
            }
            if (!file.type.match('image.*')) {
                alert('Файл должен быть изображением');
                event.target.value = '';
                return;
            }
            const reader = new FileReader();
            const self = this;
            reader.onload = function(e) {
                self.avatarPreview = e.target.result;
                const previewEl = document.getElementById('avatarPreview');
                if (previewEl) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    img.className = 'w-full h-full object-cover';
                    previewEl.innerHTML = '';
                    previewEl.appendChild(img);
                }
                const removeBtn = document.getElementById('removeAvatarBtn');
                if (removeBtn) {
                    removeBtn.style.display = 'flex';
                }
            };
            reader.readAsDataURL(file);
        }
    }
}" class="space-y-4 md:space-y-6">
    
    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Заголовок секции -->
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

        <div class="p-3 md:p-4 lg:p-6">
            <!-- Блок с аватаром и информацией -->
            <div class="flex flex-row items-start gap-3 md:gap-4 lg:gap-6 pb-4 md:pb-6 mb-4 md:mb-6 border-b border-slate-200 dark:border-slate-800">
                <!-- Аватар -->
                <div class="relative flex-shrink-0">
                    <div id="avatarPreview"
                        class="h-16 w-16 sm:h-20 sm:w-20 md:h-24 md:w-24 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-xl sm:text-2xl md:text-3xl font-bold text-white shadow-lg overflow-hidden border-2 border-slate-200 dark:border-slate-700">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <label for="avatar"
                        class="absolute bottom-0 right-0 h-7 w-7 sm:h-8 sm:w-8 rounded-lg bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center text-white cursor-pointer hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-sm border-2 border-white dark:border-slate-900">
                        <i class="fa-solid fa-camera text-[10px] sm:text-xs"></i>
                    </label>
                    @if ($user->avatar)
                        <button type="button" id="removeAvatarBtn"
                            @click="removeAvatar()"
                            class="absolute top-0 right-0 h-6 w-6 sm:h-7 sm:w-7 rounded-lg bg-rose-500 flex items-center justify-center text-white cursor-pointer hover:bg-rose-600 transition-colors shadow-sm border-2 border-white dark:border-slate-900">
                            <i class="fa-solid fa-xmark text-[10px] sm:text-xs"></i>
                        </button>
                    @endif
                </div>

                <!-- Информация -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg md:text-xl font-semibold text-slate-900 dark:text-white mb-1.5 truncate">
                        {{ $user->name }}
                    </h3>
                    <div class="space-y-1">
                        <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1.5 sm:gap-2">
                            <i class="fa-solid fa-envelope text-slate-400 text-[10px] sm:text-xs flex-shrink-0"></i>
                            <span class="truncate break-all">{{ $user->email }}</span>
                        </p>
                    @if ($user->phone)
                            <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1.5 sm:gap-2">
                                <i class="fa-solid fa-phone text-slate-400 text-[10px] sm:text-xs flex-shrink-0"></i>
                                <span class="break-all">{{ $user->phone }}</span>
                        </p>
                    @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3 md:space-y-4 lg:space-y-5">
                @csrf
                @method('PATCH')

                <!-- Скрытое поле для загрузки аватара -->
                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" @change="handleAvatarChange($event)" />

                <!-- Имя -->
                <div>
                    <label for="name"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Имя <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-400 text-sm"></i>
                        </div>
                    <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                            class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Ваше имя" />
                    </div>
                    @error('name')
                        <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                        </div>
                    <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                            class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="your@email.com" />
                    </div>
                    @error('email')
                        <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Телефон -->
                <div>
                    <label for="phone"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Телефон
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-phone text-slate-400 text-sm"></i>
                        </div>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                            placeholder="+375 (XX) XXX-XX-XX" />
                    </div>
                    @error('phone')
                        <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 sm:mt-2 text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">
                        Формат: +375 (XX) XXX-XX-XX
                    </p>
                </div>

                <!-- Скрытое поле для удаления аватара -->
                <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">

                <!-- Кнопка сохранения -->
                <div class="flex justify-end pt-3 sm:pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit" id="submitButton"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Сохранить изменения</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Безопасность -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Заголовок секции -->
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-lock text-amber-600 dark:text-amber-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Безопасность
                </h2>
            </div>
        </div>

        <div class="p-3 md:p-4 lg:p-6">
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-3 md:space-y-4 lg:space-y-5">
                @csrf
                @method('PATCH')

                <!-- Текущий пароль -->
                <div>
                    <label for="current_password"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Текущий пароль <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                        </div>
                    <input type="password" id="current_password" name="current_password"
                            class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('current_password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Введите текущий пароль" />
                    </div>
                    @error('current_password')
                        <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Новый пароль -->
                <div>
                    <label for="password"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Новый пароль <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                        </div>
                    <input type="password" id="password" name="password"
                            class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Введите новый пароль" />
                    </div>
                    @error('password')
                        <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Подтверждение нового пароля -->
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Подтверждение нового пароля <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                        </div>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Повторите новый пароль" />
                    </div>
                </div>

                <!-- Кнопка сохранения -->
                <div class="flex justify-end pt-3 sm:pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                        <i class="fa-solid fa-key text-xs"></i>
                        <span>Изменить пароль</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
