<nav
    class="md:hidden border-t border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95 fixed bottom-0 left-0 right-0 z-50">
    <div class="mx-auto max-w-6xl flex justify-around py-2.5 md:py-2 text-xs md:text-[11px]">
        <button class="flex flex-col items-center gap-1 text-[#6366F1] active:opacity-70 transition-opacity">
            <span
                class="h-7 w-7 md:h-6 md:w-6 rounded-lg bg-[#6366F1]/15 flex items-center justify-center text-[#6366F1]">
                <i class="fa-solid fa-chart-line text-sm md:text-xs"></i>
            </span>
            <span class="font-medium">Дашборд</span>
        </button>
        <button
            class="flex flex-col items-center gap-1 text-slate-500 dark:text-slate-400 active:opacity-70 transition-opacity hover:text-slate-700 dark:hover:text-slate-300">
            <span
                class="h-7 w-7 md:h-6 md:w-6 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400">
                <i class="fa-solid fa-calendar text-sm md:text-xs"></i>
            </span>
            <span>Записи</span>
        </button>
        <button
            class="flex flex-col items-center gap-1 text-slate-500 dark:text-slate-400 active:opacity-70 transition-opacity hover:text-slate-700 dark:hover:text-slate-300">
            <span
                class="h-7 w-7 md:h-6 md:w-6 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400">
                <i class="fa-solid fa-users text-sm md:text-xs"></i>
            </span>
            <span>Клиенты</span>
        </button>
        <!-- Дополнительные пункты меню -->
        <div class="relative">
            <button
                class="menu-trigger flex flex-col items-center gap-1 text-slate-500 dark:text-slate-400 active:opacity-70 transition-opacity hover:text-slate-700 dark:hover:text-slate-300"
                aria-label="Дополнительные разделы">
                <span
                    class="h-7 w-7 md:h-6 md:w-6 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400">
                    <i class="fa-solid fa-ellipsis text-sm md:text-xs"></i>
                </span>
                <span>Еще</span>
            </button>
            <div
                class="menu-panel z-[100] hidden w-48 rounded-md border border-slate-200 bg-white py-1 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900">
                <a href="#"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-scissors"></i></span>
                    <span>Услуги</span>
                </a>
                <a href="#"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-chart-bar"></i></span>
                    <span>Аналитика</span>
                </a>
                <a href="#"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i
                            class="fa-solid fa-file-invoice"></i></span>
                    <span>Отчеты</span>
                </a>
                <a href="#"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="w-4 text-indigo-500 dark:text-indigo-300"><i class="fa-solid fa-bell"></i></span>
                    <span>Уведомления</span>
                </a>
            </div>
        </div>
    </div>
</nav>