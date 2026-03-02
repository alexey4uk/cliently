<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <x-logo size="footer" />
                    <span class="text-lg font-bold text-white uppercase font-display">CLIENTLY</span>
                </div>
                <p class="text-sm text-gray-400 mb-4">
                    Онлайн-запись и CRM для салонов и мастеров. Запись через сайт и Telegram-бот, напоминания, аналитика.
                </p>
                <div class="flex gap-4">
                    <a href="https://t.me/cliently_by" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="Мы в Telegram">Telegram</a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Сервис</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#features" class="hover:text-white transition-colors">Возможности</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition-colors">Как это работает</a></li>
                    <li><a href="#pricing" class="hover:text-white transition-colors">Тарифы</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Поддержка</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="mailto:support@cliently.by" class="hover:text-white transition-colors">Помощь</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Контакты</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-start gap-2">
                        <x-icon name="envelope" variant="outline" size="sm" class="text-gray-400 shrink-0 mt-0.5" />
                        <a href="mailto:hello@cliently.by" class="hover:text-white transition-colors break-all">hello@cliently.by</a>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="envelope" variant="outline" size="sm" class="text-gray-400 shrink-0 mt-0.5" />
                        <a href="mailto:support@cliently.by" class="hover:text-white transition-colors break-all">support@cliently.by</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 py-6 flex flex-wrap justify-center items-center gap-8">
            <a href="https://express-pay.by" target="_blank" rel="noopener noreferrer" class="opacity-70 hover:opacity-100 transition-opacity" title="Express Pay — оплата картой и ЕРИП">
                <img src="https://express-pay.by/brandbook/logo_w.png" alt="Express Pay" class="h-8 w-auto" loading="lazy">
            </a>
            <a href="https://raschet.by" target="_blank" rel="noopener noreferrer" class="opacity-70 hover:opacity-100 transition-opacity" title="ЕРИП — Единое расчётное информационное пространство">
                <img src="https://raschet.by/assets/img/logo.svg" alt="ЕРИП" class="h-8 w-auto" loading="lazy">
            </a>
            <a href="https://e-pos.by" target="_blank" rel="noopener noreferrer" class="opacity-70 hover:opacity-100 transition-opacity" title="E-POS — оплата счетов в ЕРИП через QR-коды">
                <img src="https://e-pos.by/img/8.png" alt="E-POS" class="h-8 w-auto" loading="lazy">
            </a>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-400 text-center md:text-left">CLIENTLY.BY © {{ date('Y') }}</p>
            <div class="flex flex-wrap items-center justify-center md:justify-end gap-6 text-sm">
                <a href="{{ route('public.offer') }}" class="text-gray-400 hover:text-white transition-colors">Оферта</a>
                <a href="{{ route('privacy.policy') }}" class="text-gray-400 hover:text-white transition-colors">Конфиденциальность</a>
            </div>
        </div>
    </div>
</footer>
