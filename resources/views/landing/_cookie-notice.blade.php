<div id="cookie-notice" class="cookie-notice fixed left-3 right-3 bottom-[max(1rem,env(safe-area-inset-bottom))] sm:bottom-6 sm:right-6 sm:left-auto z-40 w-[calc(100%-1.5rem)] sm:w-full sm:max-w-sm hidden opacity-0 translate-y-2 transition-all duration-300 ease-out rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg" role="status" aria-live="polite">
    <div class="p-4 sm:p-5">
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-3 sm:mb-4">
            Используем файлы cookie для работы сайта.
        </p>
        <div class="flex flex-row items-center gap-3 pt-3 border-t border-gray-100 dark:border-gray-700/80">
            <a href="{{ route('privacy.policy') }}#cookies" class="cookie-notice-link flex-1 min-h-[44px] sm:min-h-0 flex items-center justify-center px-4 py-2.5 sm:py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 touch-manipulation no-underline select-none">
                Подробнее
            </a>
            <button type="button" id="cookie-notice-accept" class="cookie-notice-accept flex-1 min-h-[44px] sm:min-h-0 shrink-0 px-4 py-2.5 sm:py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 touch-manipulation select-none">
                Принять
            </button>
        </div>
    </div>
</div>
<script>
(function() {
    var key = 'cliently_cookie_notice_accepted';
    var bar = document.getElementById('cookie-notice');
    var btn = document.getElementById('cookie-notice-accept');
    if (!bar || !btn) return;
    if (localStorage.getItem(key)) {
        bar.classList.add('hidden');
        return;
    }
    function show() {
        bar.classList.remove('hidden');
        requestAnimationFrame(function() {
            bar.classList.remove('opacity-0', 'translate-y-2');
            bar.classList.add('opacity-100', 'translate-y-0');
        });
    }
    window.setTimeout(show, 1500);
    btn.addEventListener('click', function() {
        try { localStorage.setItem(key, '1'); } catch (e) {}
        bar.classList.add('opacity-0', 'translate-y-2');
        window.setTimeout(function() { bar.classList.add('hidden'); }, 300);
    });
})();
</script>
