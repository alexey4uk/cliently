{{-- Глобальное модальное окно телефона (vanilla JS). Событие: window.dispatchEvent(new CustomEvent('phone-modal-open', { detail: { phone, phoneDisplay, client } })) --}}
<div id="phone-modal-backdrop" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden" role="dialog" aria-modal="true" aria-labelledby="phone-modal-title">
    <div id="phone-modal-panel" class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800">
            <h3 id="phone-modal-title" class="text-base font-semibold text-slate-900 dark:text-white">Контактная информация</h3>
            <button type="button" id="phone-modal-close" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" aria-label="Закрыть">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="px-4 py-4">
            <div class="mb-4">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Клиент</p>
                <p id="phone-modal-client" class="text-base font-semibold text-slate-900 dark:text-white"></p>
            </div>
            <div class="mb-4">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Телефон</p>
                <p id="phone-modal-display" class="text-xl font-semibold text-slate-900 dark:text-white"></p>
            </div>
            <div class="space-y-2">
                <a id="phone-modal-call" href="#" class="md:hidden w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-phone text-sm"></i>
                    <span>Позвонить</span>
                </a>
                <button type="button" id="phone-modal-copy" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fa-regular fa-copy text-sm"></i>
                    <span>Копировать номер</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    var backdrop = document.getElementById('phone-modal-backdrop');
    if (!backdrop) return;
    var clientEl = document.getElementById('phone-modal-client');
    var displayEl = document.getElementById('phone-modal-display');
    var callEl = document.getElementById('phone-modal-call');
    var copyBtn = document.getElementById('phone-modal-copy');
    var closeBtn = document.getElementById('phone-modal-close');
    var currentPhone = '';
    function showModal(detail) {
        currentPhone = detail.phone || '';
        if (clientEl) clientEl.textContent = detail.client || '';
        if (displayEl) displayEl.textContent = detail.phoneDisplay || detail.phone || '';
        if (callEl) callEl.href = 'tel:' + (detail.phone || '').replace(/[^\d+]/g, '');
        backdrop.classList.remove('hidden');
    }
    function hideModal() {
        backdrop.classList.add('hidden');
    }
    window.addEventListener('phone-modal-open', function(e) {
        showModal(e.detail || {});
    });
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-phone-modal-trigger]');
        if (!btn) return;
        e.preventDefault();
        showModal({
            phone: btn.dataset.phone || '',
            phoneDisplay: btn.dataset.phoneDisplay || btn.dataset.phone || '',
            client: btn.dataset.clientName || ''
        });
    });
    backdrop.addEventListener('click', function() { hideModal(); });
    if (closeBtn) closeBtn.addEventListener('click', hideModal);
    if (copyBtn) copyBtn.addEventListener('click', function() {
        if (currentPhone) {
            navigator.clipboard.writeText(currentPhone);
            hideModal();
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && backdrop && !backdrop.classList.contains('hidden')) hideModal();
    });
})();
</script>
