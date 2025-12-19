@if (session()->hasAny(['success', 'info', 'error']))
<!-- Примеры уведомлений (статичные) -->
<section>
    {{-- <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-3">Уведомления</h2> --}}

    <div class="space-y-3">
        @if(session('success'))
            <!-- Уведомление об успехе -->
            <div
                class="toast-notification flex items-start mt-6 mb-6 gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-800 dark:bg-emerald-900/30">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-800">
                        <i class="fa-solid fa-check-circle text-xs text-emerald-600 dark:text-emerald-300"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">Успешно!</p>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">{{ session('success') }}</p>
                </div>
                <button onclick="this.closest('.toast-notification').remove()"
                    class="flex-shrink-0 text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <!-- Информационное уведомление -->
            <div
                class="toast-notification flex items-start mt-6 mb-6 gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 shadow-sm dark:border-blue-800 dark:bg-blue-900/30">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-800">
                        <i class="fa-solid fa-info-circle text-xs text-blue-600 dark:text-blue-300"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Информация</p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-0.5">{{ session('info') }}</p>
                </div>
                <button onclick="this.closest('.toast-notification').remove()"
                    class="flex-shrink-0 text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <!-- Уведомление об ошибке -->
            <div
                class="toast-notification flex items-start gap-3 mt-6 mb-6 rounded-lg border border-rose-200 bg-rose-50 p-4 shadow-sm dark:border-rose-800 dark:bg-rose-900/30">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-800">
                        <i class="fa-solid fa-exclamation-circle text-xs text-rose-600 dark:text-rose-300"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-rose-900 dark:text-rose-100">Ошибка</p>
                    <p class="text-xs text-rose-700 dark:text-rose-300 mt-0.5">{{ session('error') }}</p>
                </div>
                <button onclick="this.closest('.toast-notification').remove()"
                    class="flex-shrink-0 text-rose-400 hover:text-rose-600 dark:hover:text-rose-300 transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        @endif
    </div>
</section>

@push('scripts')
    <script>
        // Автоматическое скрытие уведомлений
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.toast-notification');

            notifications.forEach(function(notification) {
                // Скрываем через 5 секунд
                setTimeout(function() {
                    notification.style.transition = 'opacity 0.3s ease-out';
                    notification.style.opacity = '0';

                    // Удаляем из DOM после анимации
                    setTimeout(function() {
                        notification.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
@endpush
@endif
