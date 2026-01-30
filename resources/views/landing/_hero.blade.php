<section class="pt-24 pb-16 sm:pt-28 sm:pb-20 md:pt-36 md:pb-28 bg-gray-50 dark:bg-gray-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div>
                <p class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 mb-6">
                    Начните бесплатно, без карты
                </p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 leading-tight">
                    Управляйте клиентами <span class="text-indigo-600 dark:text-indigo-400">без сложностей</span>
                </h1>
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 mb-6 sm:mb-8 leading-relaxed">
                    Cliently — простая CRM для мастеров и самозанятых. Записи, напоминания и история клиентов в одном месте.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                            Перейти в панель
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                            Начать бесплатно
                        </a>
                    @endauth
                    <a href="#features" class="inline-flex items-center justify-center px-6 py-3.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Узнать больше
                    </a>
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="flex items-center gap-2">
                        <x-icon name="check-circle" variant="outline" size="sm" class="text-green-500 flex-shrink-0" />
                        Без кредитной карты
                    </span>
                    <span class="flex items-center gap-2">
                        <x-icon name="check-circle" variant="outline" size="sm" class="text-green-500 flex-shrink-0" />
                        Настройка за 5 минут
                    </span>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-5">
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
            </div>
        </div>
    </div>
</section>
