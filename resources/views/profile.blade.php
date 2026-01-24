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

<div class="max-w-3xl mx-auto">
    <div x-data="profileData" class="space-y-6">
    
    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6">Основная информация</h2>
        <form 
            method="POST" 
            action="{{ route('profile.update') }}" 
            enctype="multipart/form-data" 
            @submit.prevent="submitProfileForm"
            class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Скрытое поле для удаления аватара -->
                <input type="hidden" name="remove_avatar" id="remove_avatar" :value="removeAvatar ? '1' : '0'">

            <!-- Аватар -->
            <div class="space-y-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Аватар профиля
                </label>
                <div class="flex items-start gap-4">
                    <!-- Превью аватара -->
                    <div class="relative flex-shrink-0 group">
                        <label for="avatar" class="cursor-pointer block" :class="isUploading ? 'pointer-events-none' : ''">
                            <div 
                                id="avatarPreview"
                                class="relative h-20 w-20 sm:h-24 sm:w-24 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xl font-semibold text-slate-600 dark:text-slate-400 overflow-hidden border border-slate-300 dark:border-slate-600 transition-all hover:border-indigo-500">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" alt="Avatar Preview" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!avatarPreview && !hasAvatar">
                                    <span x-text="userInitials"></span>
                                </template>
                                <template x-if="!avatarPreview && hasAvatar">
                                    <img src="{{ $user->getAvatarUrl() ?? '' }}" alt="{{ $user->name }}" class="w-full h-full object-cover" referrerpolicy="no-referrer" />
                                </template>
                                
                                <!-- Индикатор загрузки -->
                                <div x-show="isUploading" 
                                     x-transition
                                     class="absolute inset-0 bg-black/60 flex items-center justify-center rounded-lg z-20">
                                    <i class="fa-solid fa-spinner fa-spin text-white text-lg"></i>
                                </div>
                                
                                <!-- Подсказка при наведении -->
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg z-10 pointer-events-none">
                                    <p class="text-white text-xs font-medium">Нажмите для загрузки</p>
                                </div>
                            </div>
                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="hidden" @change="handleAvatarChange($event)" />
                        </label>
                        
                        <!-- Кнопка удаления -->
                        <template x-if="hasAvatar || avatarPreview">
                            <button 
                                type="button"
                                @click.stop="showRemoveConfirm = true"
                                :disabled="isUploading"
                                class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-rose-500 hover:bg-rose-600 flex items-center justify-center text-white text-xs shadow-md border border-white dark:border-slate-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed z-30">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </template>
                        
                        <!-- Сообщение об ошибке -->
                        <div x-show="avatarError" 
                             x-transition
                             class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 whitespace-nowrap z-30">
                            <div class="bg-rose-500 text-white text-xs px-2 py-1 rounded shadow-lg">
                                <span x-text="avatarError"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Информация -->
                    <div class="flex-1 pt-1">
                        <div class="space-y-2">
                            <ul class="space-y-1.5">
                                <li class="flex items-start gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>Форматы: <span class="font-medium text-slate-600 dark:text-slate-300">JPG, PNG, GIF, WEBP</span></span>
                                </li>
                                <li class="flex items-start gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>Максимальный размер: <span class="font-medium text-slate-600 dark:text-slate-300">5 МБ</span></span>
                                </li>
                                <li class="flex items-start gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>Рекомендуемый размер: <span class="font-medium text-slate-600 dark:text-slate-300">400×400px</span></span>
                                </li>
                            </ul>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-info-circle text-indigo-500 mr-1"></i>
                                Нажмите на аватар для загрузки нового изображения
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Имя -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Имя <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required 
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                        placeholder="Введите ваше имя" />
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2.5 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                        placeholder="your@email.com" />
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Телефон -->
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Телефон
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full px-4 py-2.5 border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                    placeholder="+375 (XX) XXX-XX-XX" />
                @error('phone')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Формат: +375 (XX) XXX-XX-XX
                </p>
            </div>

            <!-- Кнопка сохранения -->
            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!isSubmitting">
                        <span>Сохранить изменения</span>
                    </template>
                    <template x-if="isSubmitting">
                        <span>Сохранение...</span>
                    </template>
                </button>
            </div>
            </form>
        </div>

    <!-- Безопасность -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6">Безопасность</h2>
        <form 
            method="POST" 
            action="{{ route('profile.password.update') }}" 
            @submit.prevent="submitPasswordForm"
            class="space-y-6">
                @csrf
                @method('PATCH')

            <!-- Текущий пароль -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Текущий пароль <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password"
                        x-ref="currentPassword"
                        class="w-full px-4 py-2.5 pr-10 border {{ $errors->has('current_password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                        placeholder="Введите текущий пароль" />
                    <button 
                        type="button"
                        @click="togglePasswordVisibility('current_password')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-eye text-sm" x-show="!passwordVisible.current_password"></i>
                        <i class="fa-solid fa-eye-slash text-sm" x-show="passwordVisible.current_password"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Новый пароль -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Новый пароль <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        x-ref="password"
                        @input="checkPasswordStrength"
                        class="w-full px-4 py-2.5 pr-10 border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                        placeholder="Введите новый пароль" />
                    <button 
                        type="button"
                        @click="togglePasswordVisibility('password')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-eye text-sm" x-show="!passwordVisible.password"></i>
                        <i class="fa-solid fa-eye-slash text-sm" x-show="passwordVisible.password"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                
                <!-- Индикатор сложности пароля -->
                <div x-show="passwordStrength.show" class="mt-3">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div 
                                class="h-full transition-all duration-300"
                                :class="passwordStrength.color"
                                :style="'width: ' + passwordStrength.width + '%'">
                            </div>
                        </div>
                        <span class="text-xs font-medium" :class="passwordStrength.textColor" x-text="passwordStrength.text"></span>
                    </div>
                </div>
            </div>

            <!-- Подтверждение нового пароля -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Подтверждение нового пароля <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        x-ref="passwordConfirmation"
                        class="w-full px-4 py-2.5 pr-10 border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                        placeholder="Повторите новый пароль" />
                    <button 
                        type="button"
                        @click="togglePasswordVisibility('password_confirmation')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-eye text-sm" x-show="!passwordVisible.password_confirmation"></i>
                        <i class="fa-solid fa-eye-slash text-sm" x-show="passwordVisible.password_confirmation"></i>
                    </button>
                </div>
            </div>

            <!-- Кнопка сохранения -->
            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button 
                    type="submit"
                    :disabled="isSubmittingPassword"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!isSubmittingPassword">
                        <span>Изменить пароль</span>
                    </template>
                    <template x-if="isSubmittingPassword">
                        <span>Изменение...</span>
                    </template>
                </button>
            </div>
            </form>
        </div>

    <!-- Модальное окно подтверждения удаления аватара -->
    <div 
        x-show="showRemoveConfirm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showRemoveConfirm = false"
        style="display: none;">
        <div 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 p-6 max-w-sm w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 dark:bg-rose-900/30 mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Удалить аватар?
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Вы уверены, что хотите удалить аватар? Это действие нельзя отменить.
                </p>
                <div class="flex gap-3">
                    <button 
                        @click="showRemoveConfirm = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Отмена
                    </button>
                    <button 
                        @click="confirmRemoveAvatar"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileData', () => ({
            avatarPreview: null,
            showRemoveConfirm: false,
            removeAvatar: false,
            isSubmitting: false,
            isSubmittingPassword: false,
            isUploading: false,
            avatarError: null,
            hasAvatar: {{ $user->avatar ? 'true' : 'false' }},
            userInitials: '{{ strtoupper(mb_substr($user->name, 0, 2)) }}',
            profileUpdateUrl: '{{ route('profile.update') }}',
            passwordVisible: {
                current_password: false,
                password: false,
                password_confirmation: false
            },
            passwordStrength: {
                show: false,
                width: 0,
                text: '',
                color: 'bg-slate-300',
                textColor: 'text-slate-500'
            },

            handleAvatarChange(event) {
                const file = event.target.files[0];
                
                // Сбрасываем предыдущую ошибку
                this.avatarError = null;
                
                if (!file) {
                    return;
                }

                // Валидация типа файла
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    this.avatarError = 'Недопустимый формат файла';
                    event.target.value = '';
                    setTimeout(() => {
                        this.avatarError = null;
                    }, 3000);
                    return;
                }

                // Валидация размера файла
                const maxSize = 5 * 1024 * 1024; // 5 МБ
                if (file.size > maxSize) {
                    this.avatarError = 'Размер файла превышает 5 МБ';
                    event.target.value = '';
                    setTimeout(() => {
                        this.avatarError = null;
                    }, 3000);
                    return;
                }

                // Показываем индикатор загрузки
                this.isUploading = true;

                // Читаем файл
                const reader = new FileReader();
                const self = this;

                reader.onload = function(e) {
                    self.avatarPreview = e.target.result;
                    self.hasAvatar = true;
                    self.isUploading = false;
                };

                reader.onerror = function() {
                    self.avatarError = 'Ошибка при чтении файла';
                    self.isUploading = false;
                    event.target.value = '';
                    setTimeout(() => {
                        self.avatarError = null;
                    }, 3000);
                };

                reader.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percentLoaded = Math.round((e.loaded / e.total) * 100);
                        // Можно добавить прогресс-бар в будущем
                    }
                };

                reader.readAsDataURL(file);
            },

            confirmRemoveAvatar() {
                this.removeAvatar = true;
                this.avatarPreview = null;
                this.hasAvatar = false;
                this.showRemoveConfirm = false;
                
                // Автоматически отправляем форму
                const form = document.querySelector('form[action="' + this.profileUpdateUrl + '"]');
                if (form) {
                    const hiddenInput = document.getElementById('remove_avatar');
                    if (hiddenInput) {
                        hiddenInput.value = '1';
                    }
                    form.submit();
                }
            },

            submitProfileForm(event) {
                this.isSubmitting = true;
                event.target.submit();
            },

            submitPasswordForm(event) {
                this.isSubmittingPassword = true;
                event.target.submit();
            },

            togglePasswordVisibility(field) {
                this.passwordVisible[field] = !this.passwordVisible[field];
                const input = this.$refs[field];
                if (input) {
                    input.type = this.passwordVisible[field] ? 'text' : 'password';
                }
            },

            checkPasswordStrength() {
                const password = this.$refs.password.value;
                if (!password) {
                    this.passwordStrength.show = false;
                    return;
                }

                this.passwordStrength.show = true;
                let strength = 0;
                
                if (password.length >= 8) strength += 20;
                if (password.length >= 12) strength += 10;
                if (/[A-Z]/.test(password)) strength += 20;
                if (/[a-z]/.test(password)) strength += 20;
                if (/[0-9]/.test(password)) strength += 15;
                if (/[^A-Za-z0-9]/.test(password)) strength += 15;

                this.passwordStrength.width = Math.min(strength, 100);

                if (strength < 40) {
                    this.passwordStrength.color = 'bg-red-500';
                    this.passwordStrength.text = 'Слабый';
                    this.passwordStrength.textColor = 'text-red-600 dark:text-red-400';
                } else if (strength < 70) {
                    this.passwordStrength.color = 'bg-yellow-500';
                    this.passwordStrength.text = 'Средний';
                    this.passwordStrength.textColor = 'text-yellow-600 dark:text-yellow-400';
                } else {
                    this.passwordStrength.color = 'bg-green-500';
                    this.passwordStrength.text = 'Надежный';
                    this.passwordStrength.textColor = 'text-green-600 dark:text-green-400';
                }
            },

            showToast(type, message) {
                // Используем существующую систему уведомлений
                console.log(type, message);
            }
        }));
    });

    // ==================== ОБРАБОТКА ТЕЛЕФОНА ====================
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        if (!phoneInput) return;

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
    });
</script>
@endpush
