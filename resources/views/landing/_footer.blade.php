<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <x-logo size="footer" />
                    <span class="text-lg font-bold text-white uppercase font-display">CLIENTLY</span>
                </div>
                <p class="text-sm text-gray-400 mb-4">
                    Простая CRM для самозанятых и мастеров. Организуйте клиентов и записи без сложностей.
                </p>
                {{-- <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"
                        aria-label="Telegram">Telegram</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"
                        aria-label="Instagram">Instagram</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="VK">VK</a>
                </div> --}}
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
                    <li><a href="#" class="hover:text-white transition-colors">Инструкции</a></li>
                    <li><a href="mailto:hello@cliently.by" class="hover:text-white transition-colors">Контакты</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Контакты</h3>
                <ul class="space-y-2 text-sm">
                    {{-- <li class="flex items-start gap-2">
                        <x-icon name="phone" variant="outline" size="sm" class="text-gray-400 flex-shrink-0 mt-0.5" />
                        <span>+375291234567</span>
                    </li> --}}
                    <li class="flex items-start gap-2">
                        <x-icon name="envelope" variant="outline" size="sm"
                            class="text-gray-400 shrink-0 mt-0.5" />
                        <a href="mailto:hello@cliently.by"
                            class="hover:text-white transition-colors break-all">info@cliently.by</a>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="envelope" variant="outline" size="sm"
                            class="text-gray-400 shrink-0 mt-0.5" />
                        <a href="mailto:support@cliently.by"
                            class="hover:text-white transition-colors break-all">support@cliently.by</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-400 text-center md:text-left">© {{ date('Y') }} CLIENTLY.BY</p>
            <div class="flex gap-6 text-sm">
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Оферта</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Конфиденциальность</a>
            </div>
        </div>
    </div>
</footer>
