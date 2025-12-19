@extends('layouts.user')

@section('title', 'Создание бизнеса - Cliently')
@section('page-title', 'Создание бизнеса')
@section('page-description', 'Основная информация о вашем бизнесе')

@section('content')
    <!-- Индикатор прогресса -->
    <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 mb-6">
        
        <!-- Индикатор прогресса -->
        <div class="w-full md:w-auto">
            <div class="flex items-center w-full md:w-auto md:gap-1.5">
                @php
                    $steps = [
                        1 => 'Бизнес',
                        2 => 'Локация',
                        3 => 'Услуга',
                        4 => 'Мастер'
                    ];
                @endphp
                @for($i = 1; $i <= 4; $i++)
                    <div class="flex items-center {{ $i < 4 ? 'flex-1 md:flex-none' : 'flex-shrink-0' }}">
                        <div class="flex items-center justify-center w-6 md:w-7 h-6 md:h-7 rounded-full text-xs font-semibold transition-colors flex-shrink-0 {{ $i == 1 ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $i }}
                        </div>
                        @if($i < 4)
                            <div class="flex-1 md:w-6 md:flex-none h-0.5 mx-1 md:mx-0 {{ $i <= 1 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.business.store') }}" class="space-y-6">
        @csrf
        
        <div class="space-y-6">
            <!-- Основная информация -->
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400"></i>
                        Основная информация
                    </h3>
                </div>
                
                <div>
                    <label for="name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Название бизнеса*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        autofocus>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Название будет отображаться клиентам
                    </p>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Персональная ссылка*</span>
                    </label>
                    <div class="flex items-center bg-white dark:bg-slate-900 rounded-md overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 transition-all">
                        <!-- Префикс -->
                        <span class="inline-flex items-center px-2.5 md:px-3 py-2 md:py-2.5 bg-slate-50 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 text-slate-400 dark:text-slate-500 text-base md:text-sm font-mono select-none">
                            /
                        </span>
                        <!-- Поле ввода -->
                        <div class="flex-1 relative">
                            <input type="text" id="slug" name="slug" required value="{{ old('slug') }}"
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm border-0 bg-transparent text-slate-900 dark:text-white focus:outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            
                            <!-- Индикаторы проверки -->
                            <div id="slugChecking" class="hidden absolute right-2.5 top-1/2 transform -translate-y-1/2">
                                <div class="animate-spin h-3.5 w-3.5 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                            </div>
                            
                            <div id="slugAvailable" class="hidden absolute right-2.5 top-1/2 transform -translate-y-1/2 text-emerald-500">
                                <i class="fa-solid fa-check text-xs"></i>
                            </div>
                            
                            <div id="slugUnavailable" class="hidden absolute right-2.5 top-1/2 transform -translate-y-1/2 text-rose-500">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Предпросмотр адреса -->
                    <div id="slugPreviewCard" class="hidden mt-2 transition-opacity duration-200">
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono flex items-center">
                            <i class="fa-solid fa-link text-indigo-600 dark:text-indigo-400 text-xs mr-1.5"></i>
                            <span class="select-none">https://cliently.by/</span><span id="slugPreview" class="font-semibold text-indigo-600 dark:text-indigo-400">ip-ivanov</span>
                        </p>
                    </div>
                    @error('slug')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @else
                        <p id="slugError" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                    @enderror
                </div>
            </div>

            <!-- Информация о владельце -->
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                        Информация о владельце
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="first_name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Имя*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('first_name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="last_name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Фамилия*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('last_name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Контактная информация -->
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                        Контактная информация
                    </h3>
                </div>
                
                <div>
                    <label for="phone" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Телефон*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="+375 (29) 123-45-67">
                    <p class="mt-2.5 text-xs text-slate-500 dark:text-slate-400">
                        Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                    </p>
                    @error('phone')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p id="phoneError" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400"></i>
                        Дополнительная информация
                    </h3>
                </div>
                
                <div>
                    <label for="description" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Описание</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">(необязательно)</span>
                    </label>
                    <div class="relative">
                        <textarea id="description" name="description" rows="3" maxlength="500"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                            placeholder="Краткое описание вашего бизнеса...">{{ old('description') }}</textarea>
                        <div class="absolute bottom-2 right-2 flex items-center gap-1">
                            <span id="descriptionCount" class="text-xs text-slate-400 dark:text-slate-500">0</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500">/</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500">500</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
            <button type="submit" id="submitButton"
                class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Продолжить <i class="fa-solid fa-arrow-right ml-1.5 md:ml-2"></i>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // ==================== КОНСТАНТЫ И ПЕРЕМЕННЫЕ ====================
    // Переменные для отслеживания состояния slug (для визуальной индикации)
    let slugIsChecking = false;
    let slugCheckTimeout = null;
    let currentAbortController = null;

    // Регулярное выражение для проверки формата slug
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    const SLUG_MIN_LENGTH = 3;
    const SLUG_CHECK_DEBOUNCE = 500;
    const SLUG_CHECK_TIMEOUT = 10000;

    // Таблица транслитерации кириллицы в латиницу
    const translitMap = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
        'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
        'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
        'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
        'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo',
        'Ж': 'Zh', 'З': 'Z', 'И': 'I', 'Й': 'Y', 'К': 'K', 'Л': 'L', 'М': 'M',
        'Н': 'N', 'О': 'O', 'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U',
        'Ф': 'F', 'Х': 'H', 'Ц': 'Ts', 'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sch',
        'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu', 'Я': 'Ya'
    };

    // Кэширование DOM элементов
    const slugElements = {
        input: null,
        checking: null,
        available: null,
        unavailable: null,
        error: null,
        preview: null,
        previewCard: null,
        container: null
    };


    // ==================== УТИЛИТЫ ====================
    
    /**
     * Оптимизированная функция транслитерации
     * Использует цикл вместо split/map/join для лучшей производительности
     */
    function transliterate(text) {
        if (!text) return '';
        let result = '';
        for (let i = 0; i < text.length; i++) {
            result += translitMap[text[i]] !== undefined ? translitMap[text[i]] : text[i];
        }
        return result;
    }

    /**
     * Единая функция санитизации slug
     * @param {string} input - Входная строка
     * @param {Object} options - Опции санитизации
     * @param {boolean} options.removeTrailingDash - Удалять дефис в конце
     * @param {boolean} options.removeLeadingDash - Удалять дефис в начале
     * @returns {string} Санитизированная строка
     */
    function sanitizeSlug(input, options = {}) {
        if (!input) return '';
        
        const { removeTrailingDash = false, removeLeadingDash = true } = options;
        
        // Заменяем пробелы на дефисы
        let result = input.replace(/\s+/g, '-');
        
        // Транслитерация
        result = transliterate(result);
        
        // Нижний регистр
        result = result.toLowerCase();
        
        // Удаляем недопустимые символы
        result = result.replace(/[^a-z0-9\-]/g, '');
        
        // Удаляем множественные дефисы
        result = result.replace(/-+/g, '-');
        
        // Удаление дефисов в начале и конце
        if (removeLeadingDash) {
            result = result.replace(/^-+/, '');
        }
        if (removeTrailingDash) {
            result = result.replace(/-+$/, '');
        }
        
        return result;
    }

    /**
     * Форматирование slug при потере фокуса (удаляет дефисы в начале и конце)
     */
    function formatSlug(input) {
        return sanitizeSlug(input, { removeTrailingDash: true, removeLeadingDash: true });
    }

    /**
     * Санитизация slug во время ввода (не удаляет дефис в конце)
     */
    function sanitizeSlugInput(input) {
        return sanitizeSlug(input, { removeTrailingDash: false, removeLeadingDash: true });
    }

    /**
     * Валидация формата slug
     */
    function validateSlugFormat(slug) {
        return slugRegex.test(slug);
    }

    /**
     * Установка позиции курсора с безопасной обработкой ошибок
     */
    function setCursorPosition(input, position) {
        requestAnimationFrame(() => {
            const safePosition = Math.max(0, Math.min(position, input.value.length));
            try {
                input.setSelectionRange(safePosition, safePosition);
            } catch (e) {
                console.warn('Не удалось установить позицию курсора:', e);
            }
        });
    }

    /**
     * Обновление стилей border для slug контейнера
     */
    function updateSlugBorder(state) {
        const container = slugElements.container;
        if (!container) return;

        // Удаляем все возможные классы border
        container.classList.remove('border-emerald-500', 'border-rose-500', 'border-slate-300', 'dark:border-slate-700', 'focus-within:border-indigo-500');
        
        // Добавляем нужный класс в зависимости от состояния
        switch (state) {
            case 'available':
                container.classList.add('border-emerald-500');
                break;
            case 'unavailable':
            case 'formatError':
                container.classList.add('border-rose-500');
                break;
            case 'checking':
            case 'reset':
            default:
                container.classList.add('border-slate-300', 'dark:border-slate-700');
                break;
        }
    }

    /**
     * Единая функция управления состоянием slug
     * @param {string} state - Состояние: 'checking', 'available', 'unavailable', 'formatError', 'reset'
     * @param {string} message - Сообщение об ошибке (опционально)
     */
    function setSlugState(state, message = '') {
        // Скрываем все элементы состояния
        if (slugElements.checking) slugElements.checking.classList.add('hidden');
        if (slugElements.available) slugElements.available.classList.add('hidden');
        if (slugElements.unavailable) slugElements.unavailable.classList.add('hidden');
        if (slugElements.error) slugElements.error.classList.add('hidden');

        // Показываем нужный элемент состояния
        switch (state) {
            case 'checking':
                if (slugElements.checking) slugElements.checking.classList.remove('hidden');
                break;
            case 'available':
                if (slugElements.available) slugElements.available.classList.remove('hidden');
                if (slugElements.preview && slugElements.input) {
                    slugElements.preview.textContent = slugElements.input.value || 'ip-ivanov';
                }
                break;
            case 'unavailable':
                if (slugElements.unavailable) slugElements.unavailable.classList.remove('hidden');
                if (slugElements.error && message) {
                    slugElements.error.textContent = message;
                    slugElements.error.classList.remove('hidden');
            }
                break;
            case 'formatError':
                if (slugElements.error && message) {
                    slugElements.error.textContent = message;
                    slugElements.error.classList.remove('hidden');
                }
                break;
            case 'reset':
                if (slugElements.preview) {
                    slugElements.preview.textContent = 'ip-ivanov';
                }
                if (slugElements.previewCard) {
                    slugElements.previewCard.classList.add('hidden');
                }
                break;
        }

        // Обновляем border
        updateSlugBorder(state);
    }


    // ==================== ПРОВЕРКА ДОСТУПНОСТИ SLUG ====================
    
    /**
     * Проверка доступности slug с отменой предыдущих запросов
     */
    async function checkSlugAvailability(slug) {
        // Отменяем предыдущий запрос, если он существует
        if (currentAbortController) {
            currentAbortController.abort();
        }

        if (!slug || slug.length < SLUG_MIN_LENGTH) {
            resetSlugValidation();
            return;
        }

        if (!validateSlugFormat(slug)) {
            setSlugState('formatError', 'Только латинские буквы в нижнем регистре, цифры и одиночные дефисы. Дефисы не могут быть в начале или конце.');
            return;
        }
        setSlugState('checking');
        slugIsChecking = true;

        // Создаем новый AbortController для этого запроса
        currentAbortController = new AbortController();
        let timeoutId = null;

        try {
            timeoutId = setTimeout(() => currentAbortController.abort(), SLUG_CHECK_TIMEOUT);
            
            const response = await fetch('{{ route("api.slug.check") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ slug: slug }),
                signal: currentAbortController.signal
            });

            clearTimeout(timeoutId);

            // Обработка rate limiting (429 Too Many Requests)
            if (response.status === 429) {
                const retryAfter = response.headers.get('Retry-After') || 60;
                setSlugState('unavailable', `Слишком много запросов. Попробуйте через ${retryAfter} секунд.`);
                slugIsChecking = false;
                return;
            }

            // Обработка других ошибок сервера
            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                setSlugState('unavailable', data.message || 'Не удалось проверить доступность slug. Попробуйте позже.');
                slugIsChecking = false;
                return;
            }

            const data = await response.json();
            
            if (data.available === true) {
                setSlugState('available');
            } else {
                setSlugState('unavailable', 'Этот slug уже занят. Пожалуйста, выберите другой.');
            }
        } catch (error) {
            if (timeoutId) clearTimeout(timeoutId);
            
            // Игнорируем ошибки отмены запроса
            if (error.name === 'AbortError') {
                // Если это не наш таймаут, а отмена предыдущего запроса - просто выходим
                if (!currentAbortController || currentAbortController.signal.aborted) {
                    return;
                }
                setSlugState('unavailable', 'Превышено время ожидания. Проверьте подключение к интернету.');
            } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                setSlugState('unavailable', 'Ошибка сети. Проверьте подключение к интернету.');
            } else {
            console.error('Ошибка при проверке slug:', error);
                setSlugState('unavailable', 'Не удалось проверить доступность slug. Попробуйте позже.');
            }
        } finally {
            slugIsChecking = false;
            currentAbortController = null;
        }
    }

    /**
     * Сброс валидации slug
     */
    function resetSlugValidation() {
        setSlugState('reset');
        // Скрываем карточку предпросмотра при сбросе
        if (slugElements.previewCard) {
            slugElements.previewCard.classList.add('opacity-0');
            setTimeout(() => {
                if (slugElements.previewCard) {
                    slugElements.previewCard.classList.add('hidden');
                }
            }, 200);
        }
    }

    // ==================== ОБРАБОТЧИКИ СОБЫТИЙ ====================
    

    /**
     * Обработка нажатия пробела в slug (заменяем на дефис)
     */
    function handleSlugKeydown(e) {
        if (e.key === ' ' || e.keyCode === 32) {
            e.preventDefault();
            
            const input = e.target;
            const start = input.selectionStart || 0;
            const end = input.selectionEnd || start;
            const value = input.value;
            
            // Вставляем дефис вместо пробела
            const newValue = value.substring(0, start) + '-' + value.substring(end);
            input.value = newValue;
            
            // Устанавливаем курсор после вставленного дефиса
            const newPosition = start + 1;
            setCursorPosition(input, newPosition);
            
            // Триггерим событие input для обработки санитизации
            setTimeout(() => {
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }, 0);
        }
    }

    /**
     * Обработка ввода slug
     */
    function handleSlugInput(e) {
        const input = e.target;
        const originalValue = input.value;
        const cursorPosition = input.selectionStart || 0;
        
        // Сохраняем текст до курсора для правильного расчета позиции
        const textBeforeCursor = originalValue.substring(0, cursorPosition);
        
        // Санитируем весь текст
        const sanitizedValue = sanitizeSlugInput(originalValue);
        
        if (originalValue !== sanitizedValue) {
            // Санитируем текст до курсора отдельно для расчета новой позиции
            const sanitizedBefore = sanitizeSlugInput(textBeforeCursor);
            
            // Вычисляем новую позицию курсора
            const newCursorPosition = sanitizedBefore.length;
            
            input.value = sanitizedValue;
            setCursorPosition(input, newCursorPosition);
        }
        
        const slug = sanitizedValue.trim();
        
        // Показываем/скрываем карточку предпросмотра в зависимости от наличия текста
        if (slugElements.previewCard) {
            if (slug) {
                slugElements.previewCard.classList.remove('hidden', 'opacity-0');
                slugElements.previewCard.classList.add('opacity-100');
            } else {
                slugElements.previewCard.classList.add('opacity-0');
                setTimeout(() => {
                    if (slugElements.previewCard) {
                        slugElements.previewCard.classList.add('hidden');
                    }
                }, 200);
            }
        }
        
        // Обновляем предпросмотр в реальном времени
        if (slugElements.preview) {
            slugElements.preview.textContent = slug || 'ip-ivanov';
        }
        
        // Очищаем предыдущий таймаут
        if (slugCheckTimeout) {
            clearTimeout(slugCheckTimeout);
        }
        
        if (!slug) {
            resetSlugValidation();
            return;
        }
        
        // Debounce проверки доступности
        slugCheckTimeout = setTimeout(() => {
            checkSlugAvailability(slug);
        }, SLUG_CHECK_DEBOUNCE);
    }

    /**
     * Форматирование при уходе с поля
     */
    function handleSlugBlur(e) {
        const formattedSlug = formatSlug(e.target.value);
        if (e.target.value !== formattedSlug) {
            e.target.value = formattedSlug;
        }
        
        // Скрываем предпросмотр при потере фокуса
        if (slugElements.previewCard) {
            slugElements.previewCard.classList.add('opacity-0');
            setTimeout(() => {
                if (slugElements.previewCard) {
                    slugElements.previewCard.classList.add('hidden');
                }
            }, 200);
        }
        
        if (formattedSlug.trim()) {
            checkSlugAvailability(formattedSlug.trim());
        } else {
            resetSlugValidation();
        }
    }

    /**
     * Инициализация обработчиков событий для slug
     */
    function initSlugHandlers() {
        if (!slugElements.input) return;

        slugElements.input.addEventListener('keydown', handleSlugKeydown);
        slugElements.input.addEventListener('input', handleSlugInput);
        slugElements.input.addEventListener('blur', handleSlugBlur);
    }

    // ==================== ОБРАБОТКА ТЕЛЕФОНА ====================
    
    // Валидные коды операторов Беларуси
    const validOperatorCodes = ['29', '33', '44', '25'];
    const PHONE_OPERATOR_ERROR = 'Неверный код оператора. Допустимые: 29, 33, 44, 25';
    const PHONE_INCOMPLETE_ERROR = 'Введите полный номер телефона (9 цифр после +375)';
    const PHONE_REQUIRED_DIGITS = 9;
    let phoneInput = null;

    /**
     * Показать ошибку кода оператора
     */
    function showPhoneError(message) {
        const errorElement = document.getElementById('phoneError');
        if (errorElement && phoneInput) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            phoneInput.classList.add('border-rose-500');
        }
    }

    /**
     * Скрыть ошибку кода оператора
     */
    function hidePhoneError() {
        const errorElement = document.getElementById('phoneError');
        if (errorElement && phoneInput) {
            errorElement.classList.add('hidden');
            phoneInput.classList.remove('border-rose-500');
        }
    }

    /**
     * Извлечение цифр из номера телефона (после +375)
     * @param {string} value - Полное значение поля телефона
     * @returns {string} - Только цифры после +375
     */
    function extractPhoneDigits(value) {
        return value.substring(4).replace(/\D/g, '');
    }

    /**
     * Сброс телефона к префиксу +375 с установкой курсора и показом ошибки
     * @param {HTMLInputElement} input - Элемент input
     */
    function resetPhoneToPrefix(input) {
        input.value = '+375';
        setCursorPosition(input, 5);
        showPhoneError(PHONE_OPERATOR_ERROR);
    }

    /**
     * Проверка валидности кода оператора
     * @param {string} firstDigit - Первая цифра кода
     * @returns {boolean} - Может ли быть началом валидного кода
     */
    function canBeValidOperatorCode(firstDigit) {
        return validOperatorCodes.some(code => code.startsWith(firstDigit));
    }

    /**
     * Валидация и обработка кода оператора
     * @param {string} digits - Цифры после +375
     * @param {HTMLInputElement} input - Элемент input
     * @returns {boolean} - true если код валиден, false если нужно прервать обработку
     */
    function validateOperatorCode(digits, input) {
        if (digits.length < 2) {
            // Меньше 2 цифр - проверяем первую цифру
            if (digits.length === 1) {
                const firstDigit = digits;
                if (!canBeValidOperatorCode(firstDigit)) {
                    resetPhoneToPrefix(input);
                    return false;
                }
            }
            return true;
        }

        // Проверяем код оператора (первые 2 цифры)
        const operatorCode = digits.substring(0, 2);
        if (!validOperatorCodes.includes(operatorCode)) {
            const firstDigit = digits.substring(0, 1);
            if (!canBeValidOperatorCode(firstDigit)) {
                // Первая цифра не может быть началом валидного кода - удаляем все
                resetPhoneToPrefix(input);
                return false;
            } else {
                // Вторая цифра неверная - оставляем только первую
                input.value = '+375' + firstDigit;
                showPhoneError(PHONE_OPERATOR_ERROR);
                setCursorPosition(input, 5 + firstDigit.length);
                return false;
            }
        }

        return true;
    }

    /**
     * Инициализация обработчиков для поля телефона
     */
    function initPhoneHandlers() {
        phoneInput = document.getElementById('phone');
        if (!phoneInput) return;

        // Автоподстановка +375 при фокусе на поле телефона
        phoneInput.addEventListener('focus', function(e) {
            if (!e.target.value || !e.target.value.startsWith('+375')) {
                e.target.value = '+375';
                // Устанавливаем курсор после +375
                setCursorPosition(e.target, 4);
            }
        });

        // Защита от удаления +375
        phoneInput.addEventListener('keydown', function(e) {
            const selectionStart = e.target.selectionStart;
            const selectionEnd = e.target.selectionEnd;
            
            // Блокируем удаление, если курсор находится в области +375 (позиции 0-4)
            if (selectionStart < 5 || selectionEnd < 5) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    e.preventDefault();
                    // Перемещаем курсор после +375
                    setCursorPosition(e.target, 5);
                    return false;
                }
            }
        });

        // Обработка ввода: только цифры, ограничение количества и проверка кода оператора
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Если значение не начинается с +375, устанавливаем префикс
            if (!value.startsWith('+375')) {
                value = '+375';
            }
            
            // Извлекаем только цифры после +375
            const digits = extractPhoneDigits(value);
            
            // Валидация кода оператора
            if (!validateOperatorCode(digits, e.target)) {
                return;
            }
            
            // Ограничиваем до 9 цифр (белорусский номер)
            const limitedDigits = digits.substring(0, PHONE_REQUIRED_DIGITS);
            
            // Формируем финальное значение
            e.target.value = '+375' + limitedDigits;
            
            // Если номер полный, скрываем ошибку
            if (limitedDigits.length === PHONE_REQUIRED_DIGITS) {
                hidePhoneError();
            }
            
            // Устанавливаем курсор в конец, но не раньше позиции 5
            const cursorPosition = Math.max(5, e.target.value.length);
            setCursorPosition(e.target, cursorPosition);
        });

        // Проверка при потере фокуса - если номер неполный, показываем ошибку
        phoneInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            const digits = extractPhoneDigits(value);
            
            // Если есть только +375 без цифр или меньше 9 цифр
            if (value.startsWith('+375') && digits.length < PHONE_REQUIRED_DIGITS) {
                showPhoneError(PHONE_INCOMPLETE_ERROR);
            } else if (digits.length === PHONE_REQUIRED_DIGITS) {
                hidePhoneError();
            }
        });
    }

    // ==================== ПОДСКАЗКИ И СЧЕТЧИКИ ====================
    

    // Счетчик символов для описания
    function setupDescriptionCounter() {
        const descriptionField = document.getElementById('description');
        const counter = document.getElementById('descriptionCount');
        
        if (descriptionField && counter) {
            function updateCounter() {
                const length = descriptionField.value.length;
                counter.textContent = length;
                
                if (length > 450) {
                    counter.classList.add('text-amber-600', 'dark:text-amber-400');
                    counter.classList.remove('text-slate-400', 'dark:text-slate-500');
                } else {
                    counter.classList.remove('text-amber-600', 'dark:text-amber-400');
                    counter.classList.add('text-slate-400', 'dark:text-slate-500');
                }
            }
            
            descriptionField.addEventListener('input', updateCounter);
            updateCounter(); // Инициализация с учетом старого значения
        }
    }

    // ==================== ИНИЦИАЛИЗАЦИЯ ====================
    
    /**
     * Инициализация кэшированных DOM элементов
     */
    function initSlugElements() {
        slugElements.input = document.getElementById('slug');
        slugElements.checking = document.getElementById('slugChecking');
        slugElements.available = document.getElementById('slugAvailable');
        slugElements.unavailable = document.getElementById('slugUnavailable');
        slugElements.error = document.getElementById('slugError');
        slugElements.preview = document.getElementById('slugPreview');
        slugElements.previewCard = document.getElementById('slugPreviewCard');
        // Контейнер - это родительский div с border, который содержит input
        // Ищем родителя, который имеет класс border (контейнер с border)
        if (slugElements.input) {
            slugElements.container = slugElements.input.closest('div.flex.items-center');
        }
    }

    /**
     * Инициализация обработчиков для обязательных полей
     */
    function initFormHandlers() {
        // Инициализация обработчиков для телефона
        initPhoneHandlers();
    }

    /**
     * Инициализация при загрузке страницы
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализируем кэшированные элементы
        initSlugElements();
        
        // Инициализируем обработчики событий для slug
        initSlugHandlers();
        
        // Инициализируем обработчики для остальных полей формы
        initFormHandlers();
        
        // Если есть старое значение slug, проверяем его
        if (slugElements.input?.value) {
            checkSlugAvailability(slugElements.input.value.trim());
        }
        
        setupDescriptionCounter();
    });
</script>
@endpush