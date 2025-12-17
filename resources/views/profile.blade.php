@extends('layouts.user')

@section('title', 'Профиль пользователя - Cliently')

@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Профиль</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Управление личными данными</p>
        </div>
        <a href="{{ route('dashboard') }}"
            class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors text-sm">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Назад</span>
        </a>
    </div>

    <!-- Аватар и основная информация -->
    <section>
        <div
            class="rounded-lg border border-slate-200 bg-white p-4 md:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div
                class="flex flex-col md:flex-row items-start md:items-center gap-6 pb-6 border-b border-slate-200 dark:border-slate-800 mb-6">
                <!-- Аватар -->
                <div class="relative">
                    <div
                        class="h-24 w-24 md:h-20 md:w-20 rounded-full bg-slate-200 flex items-center justify-center text-3xl md:text-2xl font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-4 border-slate-300 dark:border-slate-700">
                        АМ
                    </div>
                    <label for="avatar"
                        class="absolute bottom-0 right-0 h-8 w-8 md:h-7 md:w-7 rounded-full bg-[#6366F1] flex items-center justify-center text-white cursor-pointer hover:bg-[#4F46E5] transition-colors shadow-sm border-2 border-white dark:border-slate-900">
                        <i class="fa-solid fa-camera text-xs"></i>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" />
                    </label>
                </div>

                <!-- Информация -->
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white mb-1">Алексей Морозов</h2>
                    <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mb-2">alexey@example.com</p>
                    <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-phone mr-1.5"></i>+375 29 123-45-67
                    </p>
                </div>
            </div>

            <form method="POST" action="#" class="space-y-4">
                <!-- @csrf -->
                <!-- @method('PUT') -->

                <!-- Имя -->
                <div>
                    <label for="name"
                        class="block text-sm md:text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Имя
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-400 dark:text-slate-500 text-sm"></i>
                        </div>
                        <input type="text" id="name" name="name" value="Алексей Морозов"
                            class="block w-full pl-10 pr-3 py-2.5 text-base border border-slate-300 dark:border-slate-700 rounded-md bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 transition-colors"
                            placeholder="Ваше имя" />
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email"
                        class="block text-sm md:text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500 text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" value="alexey@example.com"
                            class="block w-full pl-10 pr-3 py-2.5 text-base border border-slate-300 dark:border-slate-700 rounded-md bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 transition-colors"
                            placeholder="your@email.com" />
                    </div>
                </div>

                <!-- Телефон -->
                <div>
                    <label for="phone"
                        class="block text-sm md:text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Телефон
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-phone text-slate-400 dark:text-slate-500 text-sm"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" value="+375291234567"
                            class="block w-full pl-10 pr-3 py-2.5 text-base border border-slate-300 dark:border-slate-700 rounded-md bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 transition-colors"
                            placeholder="+375291234567" />
                    </div>
                </div>

                <!-- Кнопка сохранения -->
                <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-[#6366F1] text-white text-sm font-medium hover:bg-[#4F46E5] active:bg-[#4338CA] transition-colors shadow-sm shadow-[#6366F1]/40">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Смена пароля -->
    <section>
        <div
            class="rounded-lg border border-slate-200 bg-white p-4 md:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-base md:text-sm font-semibold text-slate-900 dark:text-white mb-4">Безопасность</h2>

            <form method="POST" action="#" class="space-y-4">
                <!-- @csrf -->
                <!-- @method('PUT') -->

                <!-- Текущий пароль -->
                <div>
                    <label for="current_password"
                        class="block text-sm md:text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Текущий пароль
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400 dark:text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" id="current_password" name="current_password"
                            class="block w-full pl-10 pr-3 py-2.5 text-base border border-slate-300 dark:border-slate-700 rounded-md bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 transition-colors"
                            placeholder="Введите текущий пароль" />
                    </div>
                </div>

                <!-- Новый пароль -->
                <div>
                    <label for="new_password"
                        class="block text-sm md:text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Новый пароль
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400 dark:text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" id="new_password" name="new_password"
                            class="block w-full pl-10 pr-3 py-2.5 text-base border border-slate-300 dark:border-slate-700 rounded-md bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 transition-colors"
                            placeholder="Введите новый пароль" />
                    </div>
                </div>

                <!-- Подтверждение нового пароля -->
                <div>
                    <label for="new_password_confirmation"
                        class="block text-sm md:text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Подтверждение нового пароля
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400 dark:text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                            class="block w-full pl-10 pr-3 py-2.5 text-base border border-slate-300 dark:border-slate-700 rounded-md bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 transition-colors"
                            placeholder="Повторите новый пароль" />
                    </div>
                </div>

                <!-- Кнопка сохранения -->
                <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-[#6366F1] text-white text-sm font-medium hover:bg-[#4F46E5] active:bg-[#4338CA] transition-colors shadow-sm shadow-[#6366F1]/40">
                        Изменить пароль
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Переключение темы
        const htmlEl = document.documentElement;
        const toggleBtn = document.getElementById('themeToggle');

        if (toggleBtn) {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const savedTheme = localStorage.getItem('theme');
            const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

            if (shouldBeDark) {
                htmlEl.classList.add('dark');
            } else {
                htmlEl.classList.remove('dark');
            }

            toggleBtn.addEventListener('click', () => {
                const isDark = htmlEl.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }

        // Меню профиля
        document.addEventListener('DOMContentLoaded', function () {
            const menuTriggers = document.querySelectorAll('.menu-trigger');

            menuTriggers.forEach(trigger => {
                const menuPanel = trigger.nextElementSibling;
                if (!menuPanel || !menuPanel.classList.contains('menu-panel')) return;

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();

                    // Закрываем все другие меню
                    document.querySelectorAll('.menu-panel').forEach(panel => {
                        if (panel !== menuPanel) {
                            panel.classList.add('hidden');
                        }
                    });

                    // Переключаем текущее меню
                    menuPanel.classList.toggle('hidden');

                    // Позиционируем меню
                    if (!menuPanel.classList.contains('hidden')) {
                        const rect = trigger.getBoundingClientRect();
                        menuPanel.style.position = 'fixed';
                        menuPanel.style.top = (rect.bottom + 4) + 'px';
                        menuPanel.style.right = (window.innerWidth - rect.right) + 'px';
                    }
                });
            });

            // Закрытие меню при клике вне его
            document.addEventListener('click', () => {
                document.querySelectorAll('.menu-panel').forEach(panel => {
                    panel.classList.add('hidden');
                });
            });
        });
    </script>
@endsection