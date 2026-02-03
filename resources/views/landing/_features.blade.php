<section id="features" class="py-16 sm:py-20 md:py-24 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="landing-heading landing-section-title text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Всё необходимое для вашего бизнеса</h2>
            <p class="landing-section-lead text-base sm:text-lg text-gray-600 dark:text-gray-400 mx-auto">
                Онлайн-запись, Telegram-бот, база клиентов и аналитика — без лишнего
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            {{-- Telegram — запись в мессенджере --}}
            <div class="p-5 sm:p-6 bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-sky-900/20 dark:to-indigo-900/20 border border-sky-200/60 dark:border-sky-700/50 rounded-2xl ring-2 ring-sky-500/10">
                <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Запись в Telegram</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Клиенты записываются и получают напоминания прямо в мессенджере. Без перехода на сайт — всё в привычном чате.
                </p>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center mb-4">
                    <x-icon name="calendar-days" variant="outline" size="lg" class="text-green-600 dark:text-green-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Онлайн-запись</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Своя ссылка для записи: клиент выбирает услугу, мастера и время. Вы получаете уведомления о новых записях.
                </p>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center mb-4">
                    <x-icon name="users" variant="outline" size="lg" class="text-indigo-600 dark:text-indigo-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">База клиентов</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Контакты, заметки и история обращений в одном месте. Удобно искать и вести клиентов.
                </p>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center mb-4">
                    <x-icon name="bell" variant="outline" size="lg" class="text-purple-600 dark:text-purple-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Напоминания</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Система напоминает о предстоящих встречах — меньше пустых окон и довольные клиенты.
                </p>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center mb-4">
                    <x-icon name="chart-bar" variant="outline" size="lg" class="text-amber-600 dark:text-amber-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Аналитика</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Статистика по клиентам, записям и доходам. На платных тарифах — расширенные отчёты.
                </p>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mb-4">
                    <x-icon name="shield-check" variant="outline" size="lg" class="text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Безопасность</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Данные защищены. Резервные копии и надёжный хостинг — вы можете спокойно работать.
                </p>
            </div>
        </div>
    </div>
</section>
