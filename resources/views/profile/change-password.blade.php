@extends('layouts.user')

@section('title', 'Смена пароля - Cliently')

@section('content')
    <div class="max-w-2xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <!-- Заголовок -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Смена пароля</h1>
            <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Обновите ваш пароль для безопасности аккаунта</p>
        </div>

        <!-- Карточка смены пароля -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Заголовок формы -->
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">Обновление пароля</h3>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Введите текущий пароль и новый пароль</p>
            </div>

            <!-- Форма -->
            <form action="{{ route('password.update') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                @method('PUT')

                <!-- Уведомления -->
                @if(session('status'))
                    <div class="mb-4 sm:mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 sm:p-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500 text-base sm:text-lg"></i>
                            <p class="text-green-700 dark:text-green-300 font-medium text-sm sm:text-base">{{ session('status') }}</p>
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

                <div class="space-y-4 sm:space-y-6">
                    <!-- Текущий пароль -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                            Текущий пароль *
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 pr-10 @error('current_password') border-red-500 @enderror"
                                placeholder="Введите текущий пароль"
                            >
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="current_password">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Новый пароль -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                            Новый пароль *
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 pr-10 @error('password') border-red-500 @enderror"
                                placeholder="Введите новый пароль"
                            >
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="password">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <!-- Индикатор сложности пароля -->
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center space-x-2">
                                <div id="password-strength-bar" class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div id="password-strength-progress" class="h-full bg-red-500 transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <span id="password-strength-text" class="text-xs text-gray-500 dark:text-gray-400">Слабый</span>
                            </div>
                            <div id="password-requirements" class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <div class="flex items-center space-x-2 requirement" data-requirement="length">
                                    <i class="fas fa-times text-red-500 text-xs"></i>
                                    <span>Минимум 8 символов</span>
                                </div>
                                <div class="flex items-center space-x-2 requirement" data-requirement="uppercase">
                                    <i class="fas fa-times text-red-500 text-xs"></i>
                                    <span>Заглавные буквы</span>
                                </div>
                                <div class="flex items-center space-x-2 requirement" data-requirement="lowercase">
                                    <i class="fas fa-times text-red-500 text-xs"></i>
                                    <span>Строчные буквы</span>
                                </div>
                                <div class="flex items-center space-x-2 requirement" data-requirement="numbers">
                                    <i class="fas fa-times text-red-500 text-xs"></i>
                                    <span>Цифры</span>
                                </div>
                                <div class="flex items-center space-x-2 requirement" data-requirement="special">
                                    <i class="fas fa-times text-red-500 text-xs"></i>
                                    <span>Специальные символы</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Подтверждение пароля -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">
                            Подтверждение пароля *
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 pr-10 @error('password_confirmation') border-red-500 @enderror"
                                placeholder="Повторите новый пароль"
                            >
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="password_confirmation">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex flex-col sm:flex-row gap-3 mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center text-sm sm:text-base">
                        <i class="fas fa-key mr-2"></i>
                        Обновить пароль
                    </button>
                    <a href="{{ route('profile.edit') }}" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Назад к профилю
                    </a>
                </div>
            </form>
        </div>

        <!-- Советы по безопасности -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">Советы по безопасности</h3>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Рекомендации для создания надежного пароля</p>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-shield-alt text-green-500 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Используйте длинные пароли</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Пароли длиной от 12 символов значительно надежнее</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fas fa-random text-blue-500 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Разнообразие символов</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Комбинируйте буквы, цифры и специальные символы</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fas fa-user-secret text-purple-500 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Уникальность</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Не используйте один пароль для разных сервисов</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fas fa-sync-alt text-orange-500 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Регулярное обновление</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Меняйте пароль каждые 3-6 месяцев</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Переключение видимости пароля
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Проверка сложности пароля
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('password-strength-progress');
            const strengthText = document.getElementById('password-strength-text');
            const requirements = document.querySelectorAll('.requirement');

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    checkPasswordStrength(password);
                });
            }

            function checkPasswordStrength(password) {
                let strength = 0;
                const requirementsMet = {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    numbers: false,
                    special: false
                };

                // Длина пароля
                if (password.length >= 8) {
                    strength += 20;
                    requirementsMet.length = true;
                }

                // Заглавные буквы
                if (/[A-Z]/.test(password)) {
                    strength += 20;
                    requirementsMet.uppercase = true;
                }

                // Строчные буквы
                if (/[a-z]/.test(password)) {
                    strength += 20;
                    requirementsMet.lowercase = true;
                }

                // Цифры
                if (/[0-9]/.test(password)) {
                    strength += 20;
                    requirementsMet.numbers = true;
                }

                // Специальные символы
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength += 20;
                    requirementsMet.special = true;
                }

                // Обновляем индикатор
                strengthBar.style.width = strength + '%';

                // Обновляем текст и цвет
                if (strength < 40) {
                    strengthBar.className = 'h-full bg-red-500 transition-all duration-300';
                    strengthText.textContent = 'Слабый';
                    strengthText.className = 'text-xs text-red-500';
                } else if (strength < 80) {
                    strengthBar.className = 'h-full bg-yellow-500 transition-all duration-300';
                    strengthText.textContent = 'Средний';
                    strengthText.className = 'text-xs text-yellow-500';
                } else {
                    strengthBar.className = 'h-full bg-green-500 transition-all duration-300';
                    strengthText.textContent = 'Надежный';
                    strengthText.className = 'text-xs text-green-500';
                }

                // Обновляем иконки требований
                requirements.forEach(req => {
                    const requirementType = req.getAttribute('data-requirement');
                    const icon = req.querySelector('i');

                    if (requirementsMet[requirementType]) {
                        icon.classList.remove('fa-times', 'text-red-500');
                        icon.classList.add('fa-check', 'text-green-500');
                    } else {
                        icon.classList.remove('fa-check', 'text-green-500');
                        icon.classList.add('fa-times', 'text-red-500');
                    }
                });
            }
        });
    </script>
@endsection
