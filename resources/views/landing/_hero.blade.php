<section class="landing-hero pt-24 pb-16 sm:pt-32 sm:pb-24 md:pt-40 md:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <div class="min-w-0">
                <p class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 mb-5 sm:mb-8">
                    Начните бесплатно, без карты
                </p>
                <h1 class="landing-heading text-2xl sm:text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-7 leading-tight tracking-tight">
                    Записи и клиенты <span class="text-indigo-600 dark:text-indigo-400">в одном месте</span>
                </h1>
                <p class="text-sm sm:text-base sm:text-lg text-gray-600 dark:text-gray-400 mb-7 sm:mb-10 leading-relaxed max-w-xl">
                    Онлайн-запись через сайт и <strong class="text-gray-800 dark:text-gray-200">Telegram-бот</strong>. Напоминания клиентам, база, аналитика — для салонов и мастеров.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-8 sm:mb-10">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center min-h-[44px] w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-500/25">
                            Перейти в панель
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center min-h-[44px] w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-500/25">
                            Начать бесплатно
                        </a>
                    @endauth
                    <a href="#features" class="inline-flex items-center justify-center min-h-[44px] w-full sm:w-auto px-6 py-3.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Узнать больше
                    </a>
                </div>
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-y-2 sm:gap-x-6 text-sm text-gray-600 dark:text-gray-400">
                    <span class="flex items-center gap-2">
                        <x-icon name="check-circle" variant="outline" size="sm" class="text-green-500 flex-shrink-0" />
                        Без кредитной карты
                    </span>
                    <span class="flex items-center gap-2">
                        <x-icon name="check-circle" variant="outline" size="sm" class="text-green-500 flex-shrink-0" />
                        Настройка за 5 минут
                    </span>
                    <span class="flex items-center gap-2">
                        <x-icon name="check-circle" variant="outline" size="sm" class="text-green-500 flex-shrink-0" />
                        Запись в Telegram
                    </span>
                </div>
            </div>
            <div class="hidden lg:flex lg:items-start lg:gap-4">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-none p-5 flex-1">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Мои клиенты</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Сегодня, {{ \Carbon\Carbon::now()->locale('ru')->isoFormat('D MMMM') }}</p>
                        </div>
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Анна К.</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Стрижка · 14:00</div>
                            </div>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Мария С.</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Маникюр · 16:30</div>
                            </div>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Ирина П.</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Консультация · 18:00</div>
                            </div>
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-none p-4 w-52 shrink-0">
                    <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-sky-100 dark:bg-sky-900/40">
                            <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Запись в Telegram</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Клиенты записываются и получают напоминания прямо в мессенджере.</p>
                </div>
            </div>
        </div>
    </div>
</section>
