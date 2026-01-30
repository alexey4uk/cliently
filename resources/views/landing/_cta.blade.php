<section class="py-16 sm:py-20 md:py-24 bg-indigo-600 dark:bg-indigo-700">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4">Готовы организовать свой бизнес?</h2>
        <p class="text-base sm:text-lg text-indigo-100 mb-8">
            Присоединяйтесь к мастерам, которые уже работают эффективнее с Cliently
        </p>
        @auth
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3.5 bg-white text-indigo-600 hover:bg-indigo-50 font-semibold rounded-lg transition-colors">
                Перейти в панель управления
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3.5 bg-white text-indigo-600 hover:bg-indigo-50 font-semibold rounded-lg transition-colors">
                Начать бесплатно
            </a>
            <p class="mt-4 text-sm text-indigo-200">Никаких платёжных данных не требуется</p>
        @endauth
    </div>
</section>
