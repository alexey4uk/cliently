<section id="how-it-works" class="py-16 sm:py-20 md:py-24 bg-gray-50 dark:bg-gray-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="landing-heading landing-section-title text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Как начать работать с Cliently</h2>
            <p class="landing-section-lead text-base sm:text-lg text-gray-600 dark:text-gray-400 mx-auto">
                Три шага — и вы принимаете записи через сайт и Telegram
            </p>
        </div>

        <div class="grid sm:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div class="text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-lg sm:text-xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Регистрация</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Создайте аккаунт за пару минут. Без сложных настроек и проверок.
                </p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-lg sm:text-xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Настройка</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Добавьте бизнес, услуги, локации и мастеров. При желании подключите Telegram-бота.
                </p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-lg sm:text-xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Работайте</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Принимайте записи через сайт и Telegram, управляйте клиентами — всё готово.
                </p>
            </div>
        </div>

        <div class="text-center mt-10 sm:mt-12">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">Перейти в панель</a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">Начать бесплатно</a>
            @endauth
        </div>
    </div>
</section>
