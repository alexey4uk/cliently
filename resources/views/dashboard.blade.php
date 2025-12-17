@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-baseline justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Дашборд</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">Сегодня, 15 декабря</p>
        </div>
    </div>

    <!-- Панель статистики (компактная, сверху) -->
    <section class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base md:text-sm font-semibold text-slate-900 dark:text-white">Статистика</h2>
            <div class="flex items-center gap-2">
                <button
                    class="px-3 py-1.5 md:px-2 md:py-1 text-sm md:text-xs font-medium text-slate-900 dark:text-slate-300 bg-slate-200 dark:bg-slate-800 rounded-md hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors">
                    Неделя
                </button>
                <button
                    class="px-3 py-1.5 md:px-2 md:py-1 text-sm md:text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    Месяц
                </button>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm md:text-xs font-medium text-slate-500 dark:text-slate-400">Всего</span>
                    <div
                        class="flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-500/20">
                        <i class="fa-solid fa-calendar-check text-sm md:text-xs text-blue-600 dark:text-blue-300"></i>
                    </div>
                </div>
                <div class="text-3xl md:text-2xl font-bold text-slate-900 dark:text-white">8</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm md:text-xs font-medium text-slate-500 dark:text-slate-400">Ожидают</span>
                    <div
                        class="flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-500/20">
                        <i class="fa-solid fa-clock text-sm md:text-xs text-amber-600 dark:text-amber-300"></i>
                    </div>
                </div>
                <div class="text-3xl md:text-2xl font-bold text-amber-600 dark:text-amber-300">3</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm md:text-xs font-medium text-slate-500 dark:text-slate-400">Отменено</span>
                    <div
                        class="flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-500/20">
                        <i class="fa-solid fa-xmark text-sm md:text-xs text-rose-600 dark:text-rose-300"></i>
                    </div>
                </div>
                <div class="text-3xl md:text-2xl font-bold text-rose-600 dark:text-rose-300">1</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm md:text-xs font-medium text-slate-500 dark:text-slate-400">Выручка</span>
                    <div
                        class="flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-500/20">
                        <i class="fa-solid fa-ruble-sign text-sm md:text-xs text-emerald-600 dark:text-emerald-300"></i>
                    </div>
                </div>
                <div class="text-3xl md:text-2xl font-bold text-emerald-600 dark:text-emerald-300">12 500 ₽</div>
            </div>
        </div>
    </section>

    <!-- Блок 1: Требуют подтверждения -->
    <section class="mb-6">
        <div class="flex items-center justify-between gap-2 mb-3">
            <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white">Требуют подтверждения</h2>
            <span
                class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 md:px-2 md:py-0.5 text-sm md:text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                3 записи
            </span>
        </div>

        <!-- Мобильная версия (карточки) -->
        <div class="md:hidden space-y-4 md:space-y-3">
            <!-- Запись 1 -->
            <article
                class="rounded-lg border border-slate-200 bg-white p-4 md:p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 relative">
                    <div class="flex-1 min-w-0">
                        <p class="text-base md:text-sm font-medium text-slate-900 dark:text-white">10:30 • Стрижка
                            мужская</p>
                        <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">15 дек • Иван Петров
                        </p>
                    </div>

                    <button
                        class="call-btn inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 active:bg-indigo-100/80 transition-colors dark:bg-indigo-500/20 dark:text-indigo-200 dark:hover:bg-indigo-500/30"
                        data-phone="+79991112233" data-phone-display="+7 (999) 111-22-33" data-client="Иван Петров"
                        aria-label="Позвонить">
                        <i class="fa-solid fa-phone text-sm md:text-xs"></i>
                    </button>

                    <button type="button"
                        class="menu-trigger flex-shrink-0 inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-sm md:text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Дополнительные действия">
                        ⋯
                    </button>

                    <div
                        class="menu-panel z-20 hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                        <button
                            class="confirm-btn flex w-full items-center gap-2 px-3 py-2 text-left text-emerald-700 hover:bg-emerald-50 dark:text-emerald-200 dark:hover:bg-emerald-500/30"
                            onclick="return confirm('Подтвердить запись?')">
                            <span class="w-4 text-emerald-600 dark:text-emerald-300"><i
                                    class="fa-solid fa-check"></i></span>
                            <span>Подтвердить</span>
                        </button>
                        <button
                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-regular fa-eye"></i></span>
                            <span>Просмотр</span>
                        </button>
                        <button
                            class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                            onclick="return confirm('Отменить запись?')">
                            <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                            <span>Отменить</span>
                        </button>
                    </div>
                </div>
            </article>

            <!-- Запись 2 -->
            <article
                class="rounded-lg border border-slate-200 bg-white p-4 md:p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 relative">
                    <div class="flex-1 min-w-0">
                        <p class="text-base md:text-sm font-medium text-slate-900 dark:text-white">12:00 • Стрижка
                            женская</p>
                        <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">15 дек • Анна
                            Смирнова</p>
                    </div>

                    <button
                        class="call-btn inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 active:bg-indigo-100/80 transition-colors dark:bg-indigo-500/20 dark:text-indigo-200 dark:hover:bg-indigo-500/30"
                        data-phone="+79992223344" data-phone-display="+7 (999) 222-33-44" data-client="Анна Смирнова"
                        aria-label="Позвонить">
                        <i class="fa-solid fa-phone text-sm md:text-xs"></i>
                    </button>

                    <button type="button"
                        class="menu-trigger flex-shrink-0 inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-sm md:text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Дополнительные действия">
                        ⋯
                    </button>

                    <div
                        class="menu-panel z-20 hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                        <button
                            class="confirm-btn flex w-full items-center gap-2 px-3 py-2 text-left text-emerald-700 hover:bg-emerald-50 dark:text-emerald-200 dark:hover:bg-emerald-500/30"
                            onclick="return confirm('Подтвердить запись?')">
                            <span class="w-4 text-emerald-600 dark:text-emerald-300"><i
                                    class="fa-solid fa-check"></i></span>
                            <span>Подтвердить</span>
                        </button>
                        <button
                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-regular fa-eye"></i></span>
                            <span>Просмотр</span>
                        </button>
                        <button
                            class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                            onclick="return confirm('Отменить запись?')">
                            <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                            <span>Отменить</span>
                        </button>
                    </div>
                </div>
            </article>

            <!-- Запись 3 -->
            <article
                class="rounded-lg border border-slate-200 bg-white p-4 md:p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3 relative">
                    <div class="flex-1 min-w-0">
                        <p class="text-base md:text-sm font-medium text-slate-900 dark:text-white">14:15 •
                            Оформление бороды</p>
                        <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">15 дек • Сергей</p>
                    </div>

                    <button
                        class="call-btn inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 active:bg-indigo-100/80 transition-colors dark:bg-indigo-500/20 dark:text-indigo-200 dark:hover:bg-indigo-500/30"
                        data-phone="+79993334455" data-phone-display="+7 (999) 333-44-55" data-client="Сергей"
                        aria-label="Позвонить">
                        <i class="fa-solid fa-phone text-sm md:text-xs"></i>
                    </button>

                    <button type="button"
                        class="menu-trigger flex-shrink-0 inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-sm md:text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Дополнительные действия">
                        ⋯
                    </button>

                    <div
                        class="menu-panel z-20 hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                        <button
                            class="confirm-btn flex w-full items-center gap-2 px-3 py-2 text-left text-emerald-700 hover:bg-emerald-50 dark:text-emerald-200 dark:hover:bg-emerald-500/30"
                            onclick="return confirm('Подтвердить запись?')">
                            <span class="w-4 text-emerald-600 dark:text-emerald-300"><i
                                    class="fa-solid fa-check"></i></span>
                            <span>Подтвердить</span>
                        </button>
                        <button
                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                    class="fa-regular fa-eye"></i></span>
                            <span>Просмотр</span>
                        </button>
                        <button
                            class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                            onclick="return confirm('Отменить запись?')">
                            <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                            <span>Отменить</span>
                        </button>
                    </div>
                </div>
            </article>
        </div>

        <!-- Десктопная версия (таблица) -->
        <div class="hidden md:block rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            style="overflow: visible;">
            <div class="overflow-x-auto" style="overflow-y: visible;">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th
                                class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Дата и время
                            </th>
                            <th
                                class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Услуга
                            </th>
                            <th
                                class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Клиент
                            </th>
                            <th
                                class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <!-- Запись 1 -->
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-100 dark:border-slate-800">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar text-[10px] text-slate-400 dark:text-slate-500"></i>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">15 дек</span>
                                    <span
                                        class="inline-flex justify-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                        10:30
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-900 dark:text-white">Стрижка мужская</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-600 dark:text-slate-300">Иван Петров</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2 relative">
                                    <button
                                        class="confirm-btn inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700 hover:bg-emerald-100 transition-colors dark:bg-emerald-500/20 dark:text-emerald-200 dark:hover:bg-emerald-500/30"
                                        onclick="return confirm('Подтвердить запись?')">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        <span class="hidden lg:inline">Подтвердить</span>
                                    </button>
                                    <button type="button"
                                        class="menu-trigger inline-flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                                        aria-label="Дополнительные действия">
                                        ⋯
                                    </button>
                                    <div
                                        class="menu-panel z-[100] hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                        <button
                                            class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                            data-phone="+79991112233" data-phone-display="+7 (999) 111-22-33"
                                            data-client="Иван Петров">
                                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                    class="fa-solid fa-phone"></i></span>
                                            <span>Позвонить</span>
                                        </button>
                                        <button
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                    class="fa-regular fa-eye"></i></span>
                                            <span>Просмотр</span>
                                        </button>
                                        <button
                                            class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                            onclick="return confirm('Отменить запись?')">
                                            <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                            <span>Отменить</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Запись 2 -->
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-100 dark:border-slate-800">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar text-[10px] text-slate-400 dark:text-slate-500"></i>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">15 дек</span>
                                    <span
                                        class="inline-flex justify-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                        12:00
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-900 dark:text-white">Стрижка женская</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-600 dark:text-slate-300">Анна Смирнова</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2 relative">
                                    <button
                                        class="confirm-btn inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700 hover:bg-emerald-100 transition-colors dark:bg-emerald-500/20 dark:text-emerald-200 dark:hover:bg-emerald-500/30"
                                        onclick="return confirm('Подтвердить запись?')">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        <span class="hidden lg:inline">Подтвердить</span>
                                    </button>
                                    <button type="button"
                                        class="menu-trigger inline-flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                                        aria-label="Дополнительные действия">
                                        ⋯
                                    </button>
                                    <div
                                        class="menu-panel z-[100] hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                        <button
                                            class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                            data-phone="+79992223344" data-phone-display="+7 (999) 222-33-44"
                                            data-client="Анна Смирнова">
                                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                    class="fa-solid fa-phone"></i></span>
                                            <span>Позвонить</span>
                                        </button>
                                        <button
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                    class="fa-regular fa-eye"></i></span>
                                            <span>Просмотр</span>
                                        </button>
                                        <button
                                            class="cancel-btn flex w-full items-center gap-2 px-3 py-1.5 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                            onclick="return confirm('Отменить запись?')">
                                            <span class="w-3 text-[11px]"><i class="fa-solid fa-xmark"></i></span>
                                            <span>Отменить</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Запись 3 -->
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-100 dark:border-slate-800">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar text-[10px] text-slate-400 dark:text-slate-500"></i>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">15 дек</span>
                                    <span
                                        class="inline-flex justify-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                        14:15
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-900 dark:text-white">Оформление бороды</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-600 dark:text-slate-300">Сергей</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2 relative">
                                    <button
                                        class="confirm-btn inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700 hover:bg-emerald-100 transition-colors dark:bg-emerald-500/20 dark:text-emerald-200 dark:hover:bg-emerald-500/30"
                                        onclick="return confirm('Подтвердить запись?')">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        <span class="hidden lg:inline">Подтвердить</span>
                                    </button>
                                    <button type="button"
                                        class="menu-trigger inline-flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                                        aria-label="Дополнительные действия">
                                        ⋯
                                    </button>
                                    <div
                                        class="menu-panel z-[100] hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                        <button
                                            class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                            data-phone="+79993334455" data-phone-display="+7 (999) 333-44-55"
                                            data-client="Сергей">
                                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                    class="fa-solid fa-phone"></i></span>
                                            <span>Позвонить</span>
                                        </button>
                                        <button
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                            <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                    class="fa-regular fa-eye"></i></span>
                                            <span>Просмотр</span>
                                        </button>
                                        <button
                                            class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                            onclick="return confirm('Отменить запись?')">
                                            <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                            <span>Отменить</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Блок 2: Сегодня -->
    <section class="mb-6">
        <!-- Список на сегодня -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white">Сегодня</h2>
                <button
                    class="text-sm md:text-xs font-medium text-[#6366F1] hover:text-[#4F46E5] dark:text-indigo-300 dark:hover:text-indigo-200 transition-colors">
                    Все записи
                </button>
            </div>

            <!-- Мобильная версия (карточки) -->
            <div class="md:hidden space-y-4 md:space-y-3">
                <!-- Запись 1 -->
                <article
                    class="rounded-lg border border-slate-200 bg-white p-4 md:p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3 relative">
                        <div class="flex-1 min-w-0">
                            <p class="text-base md:text-sm font-medium text-slate-900 dark:text-white">09:00 •
                                Стрижка мужская</p>
                            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Алексей • 45 мин
                            </p>
                        </div>

                        <button type="button"
                            class="menu-trigger flex-shrink-0 inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-sm md:text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                            aria-label="Дополнительные действия">
                            ⋯
                        </button>

                        <div
                            class="menu-panel z-20 hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                            <button
                                class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                data-phone="+79991234567" data-phone-display="+7 (999) 123-45-67" data-client="Алексей">
                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                        class="fa-solid fa-phone"></i></span>
                                <span>Позвонить</span>
                            </button>
                            <button
                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                        class="fa-regular fa-eye"></i></span>
                                <span>Просмотр</span>
                            </button>
                            <button
                                class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                onclick="return confirm('Отменить запись?')">
                                <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                <span>Отменить</span>
                            </button>
                        </div>
                    </div>
                </article>

                <!-- Запись 2 -->
                <article
                    class="rounded-lg border border-slate-200 bg-white p-4 md:p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3 relative">
                        <div class="flex-1 min-w-0">
                            <p class="text-base md:text-sm font-medium text-slate-900 dark:text-white">11:00 •
                                Стрижка женская</p>
                            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Мария • 1 ч 30
                                мин</p>
                        </div>

                        <button type="button"
                            class="menu-trigger flex-shrink-0 inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-sm md:text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                            aria-label="Дополнительные действия">
                            ⋯
                        </button>

                        <div
                            class="menu-panel z-20 hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                            <button
                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                        class="fa-regular fa-eye"></i></span>
                                <span>Просмотр</span>
                            </button>
                            <button
                                class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                onclick="return confirm('Отменить запись?')">
                                <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                <span>Отменить</span>
                            </button>
                        </div>
                    </div>
                </article>

                <!-- Запись 3 -->
                <article
                    class="rounded-lg border border-slate-200 bg-white p-4 md:p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3 relative">
                        <div class="flex-1 min-w-0">
                            <p class="text-base md:text-sm font-medium text-slate-900 dark:text-white">16:00 •
                                Окрашивание</p>
                            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ольга • 2 ч</p>
                        </div>

                        <button type="button"
                            class="menu-trigger flex-shrink-0 inline-flex h-8 w-8 md:h-7 md:w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-sm md:text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                            aria-label="Дополнительные действия">
                            ⋯
                        </button>

                        <div
                            class="menu-panel z-20 hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                            <button
                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                        class="fa-regular fa-eye"></i></span>
                                <span>Просмотр</span>
                            </button>
                            <button
                                class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                onclick="return confirm('Отменить запись?')">
                                <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                <span>Отменить</span>
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Десктопная версия (таблица) -->
            <div class="hidden md:block rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                style="overflow: visible;">
                <div class="overflow-x-auto" style="overflow-y: visible;">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                    Время
                                </th>
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                    Услуга
                                </th>
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                    Клиент
                                </th>
                                <th
                                    class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                    Действия
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <!-- Запись 1 -->
                            <tr
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-100 dark:border-slate-800">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex justify-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                            09:00
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">• 45 мин</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-900 dark:text-white">Стрижка мужская</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-600 dark:text-slate-300">Алексей</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2 relative">
                                        <button type="button"
                                            class="menu-trigger inline-flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                                            aria-label="Дополнительные действия">
                                            ⋯
                                        </button>
                                        <div
                                            class="menu-panel z-[100] hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                            <button
                                                class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                                data-phone="+79991234567" data-phone-display="+7 (999) 123-45-67"
                                                data-client="Алексей">
                                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                        class="fa-solid fa-phone"></i></span>
                                                <span>Позвонить</span>
                                            </button>
                                            <button
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                        class="fa-regular fa-eye"></i></span>
                                                <span>Просмотр</span>
                                            </button>
                                            <button
                                                class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                                onclick="return confirm('Отменить запись?')">
                                                <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                                <span>Отменить</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Запись 2 -->
                            <tr
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-100 dark:border-slate-800">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex justify-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                            11:00
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">• 1 ч 30 мин</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-900 dark:text-white">Стрижка женская</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-600 dark:text-slate-300">Мария</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2 relative">
                                        <button type="button"
                                            class="menu-trigger inline-flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                                            aria-label="Дополнительные действия">
                                            ⋯
                                        </button>
                                        <div
                                            class="menu-panel z-[100] hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                            <button
                                                class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                                data-phone="+79992345678" data-phone-display="+7 (999) 234-56-78"
                                                data-client="Мария">
                                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                        class="fa-solid fa-phone"></i></span>
                                                <span>Позвонить</span>
                                            </button>
                                            <button
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                        class="fa-regular fa-eye"></i></span>
                                                <span>Просмотр</span>
                                            </button>
                                            <button
                                                class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                                onclick="return confirm('Отменить запись?')">
                                                <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                                <span>Отменить</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Запись 3 -->
                            <tr
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-100 dark:border-slate-800">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex justify-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                            16:00
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">• 2 ч</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-900 dark:text-white">Окрашивание</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-600 dark:text-slate-300">Ольга</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2 relative">
                                        <button type="button"
                                            class="menu-trigger inline-flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                                            aria-label="Дополнительные действия">
                                            ⋯
                                        </button>
                                        <div
                                            class="menu-panel z-[100] hidden w-40 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                            <button
                                                class="call-btn flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800"
                                                data-phone="+79993456789" data-phone-display="+7 (999) 345-67-89"
                                                data-client="Ольга">
                                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                        class="fa-solid fa-phone"></i></span>
                                                <span>Позвонить</span>
                                            </button>
                                            <button
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                                <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                                                        class="fa-regular fa-eye"></i></span>
                                                <span>Просмотр</span>
                                            </button>
                                            <button
                                                class="cancel-btn flex w-full items-center gap-2 px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                                onclick="return confirm('Отменить запись?')">
                                                <span class="w-4"><i class="fa-solid fa-xmark"></i></span>
                                                <span>Отменить</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Модальное окно для номера телефона -->
    <div id="phoneModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div
            class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 p-6 max-w-sm w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Номер телефона</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Клиент</p>
                    <p id="modalClient" class="text-sm font-medium text-slate-900 dark:text-white"></p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Телефон</p>
                    <p id="modalPhone" class="text-xl font-bold text-slate-900 dark:text-white"></p>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <a id="modalCallLink" href="#"
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-phone"></i>
                    <span>Позвонить</span>
                </a>
                <button onclick="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Обработка кнопок "Позвонить"
        document.addEventListener('click', (event) => {
            const callBtn = event.target.closest('.call-btn');
            if (!callBtn) return;

            const phone = callBtn.getAttribute('data-phone');
            const phoneDisplay = callBtn.getAttribute('data-phone-display');
            const client = callBtn.getAttribute('data-client');

            // Проверяем, мобильное устройство или нет
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                // На мобильных - сразу звоним
                window.location.href = `tel:${phone}`;
            } else {
                // На десктопе - показываем модальное окно
                const modal = document.getElementById('phoneModal');
                const modalPhone = document.getElementById('modalPhone');
                const modalClient = document.getElementById('modalClient');
                const modalCallLink = document.getElementById('modalCallLink');

                if (modal && modalPhone && modalClient && modalCallLink) {
                    modalPhone.textContent = phoneDisplay;
                    modalClient.textContent = client || '';
                    modalCallLink.href = `tel:${phone}`;
                    modal.classList.remove('hidden');
                }
            }
        });

        // Закрытие модального окна
        window.closeModal = () => {
            const modal = document.getElementById('phoneModal');
            if (modal) modal.classList.add('hidden');
        };

        // Закрытие по клику на фон
        document.getElementById('phoneModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'phoneModal') window.closeModal();
        });

        // Автоматическое скрывание уведомлений через 5 секунд
        document.querySelectorAll('.toast-notification').forEach((notification) => {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        });
    </script>
@endpush
