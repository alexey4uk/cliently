<div>
    <div>
        <label for="slug"
            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            <span>Ссылка на запись*</span>
        </label>

        <div class="flex items-center bg-white dark:bg-slate-900 rounded-md border {{ $errorMessage || $errors->has('slug') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all"
            id="slugContainer">
            <!-- Префикс -->
            <span
                class="inline-flex items-center px-2.5 md:px-3 py-2 md:py-2.5 bg-slate-50 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 text-slate-400 dark:text-slate-500 text-sm font-mono select-none">
                /
            </span>

            <!-- Поле ввода -->
            <div class="flex-1 relative">
                <input type="text" wire:model.live.debounce.1000ms="slug" id="slug" name="slug" required
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm border-0 bg-transparent text-slate-900 dark:text-white focus:outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500"
                    placeholder="например, ip-ivanov" autocomplete="off">

                <!-- Индикаторы проверки -->
                @if ($isChecking)
                    <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2">
                        <div
                            class="animate-spin h-3.5 w-3.5 border-2 border-indigo-500 border-t-transparent rounded-full">
                        </div>
                    </div>
                @elseif($isAvailable === true)
                    <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-emerald-500">
                        <i class="fa-solid fa-check text-xs"></i>
                    </div>
                @elseif($isAvailable === false)
                    <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-rose-500">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Предпросмотр адреса -->
        @if ($slug)
            <div class="mt-2 transition-opacity duration-200">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono flex items-center">
                    <i class="fa-solid fa-link text-indigo-600 dark:text-indigo-400 text-xs mr-1.5"></i>
                    <span class="select-none">{{ url('/') }}/book/</span>
                    <span
                        class="font-semibold {{ $isAvailable === true ? 'text-emerald-600 dark:text-emerald-400' : ($isAvailable === false ? 'text-rose-600 dark:text-rose-400' : 'text-indigo-600 dark:text-indigo-400') }}">
                        {{ $slug ?: 'ip-ivanov' }}
                    </span>
                </p>
            </div>
        @endif

        <!-- Сообщения об ошибках -->
        @error('slug')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror

        @if ($errorMessage)
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $errorMessage }}</p>
        @endif

        <!-- Подсказка -->
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
            Уникальная ссылка для записи. Только латинские буквы, цифры и дефисы.
        </p>
    </div>

    {{-- @script
    <script>
        // Обработчик для дебаунса на клиенте
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('slug');
            
            if (input) {
                let timeout = null;
                
                // Автоматическая замена пробелов на дефисы
                input.addEventListener('keydown', function(e) {
                    if (e.key === ' ') {
                        e.preventDefault();
                        const start = this.selectionStart;
                        const end = this.selectionEnd;
                        const value = this.value;
                        
                        this.value = value.substring(0, start) + '-' + value.substring(end);
                        this.selectionStart = this.selectionEnd = start + 1;
                        
                        // Триггерим событие input
                        this.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
                
                // Автоматическое приведение к нижнему регистру
                input.addEventListener('input', function(e) {
                    const cursorPosition = this.selectionStart;
                    this.value = this.value.toLowerCase();
                    this.setSelectionRange(cursorPosition, cursorPosition);
                });
            }
        });
    </script>
    @endscript --}}
</div>
