@extends('layouts.user')

@section('title', 'Редактирование бизнеса - Cliently')
@section('page-title', 'Данные бизнеса')
@section('page-description', 'Измените информацию о вашем бизнесе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Данные бизнеса', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('settings.business.update') }}" class="space-y-6">
    @csrf
    @method('PATCH')
    
    <div class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
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
                    <input type="text" id="name" name="name" required value="{{ old('name', $business->name) }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        autofocus>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Ссылка на запись*</span>
                    </label>
                    <div class="flex items-center bg-white dark:bg-slate-900 rounded-md border border-slate-300 dark:border-slate-700 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all" id="slugContainer">
                        <!-- Префикс -->
                        <span class="inline-flex items-center px-2.5 md:px-3 py-2 md:py-2.5 bg-slate-50 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 text-slate-400 dark:text-slate-500 text-base md:text-sm font-mono select-none">
                            /
                        </span>
                        <!-- Поле ввода -->
                        <div class="flex-1 relative">
                            <input type="text" id="slug" name="slug" required value="{{ old('slug', $business->slug) }}"
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
                    <div id="slugPreviewCard" class="{{ old('slug', $business->slug) ? '' : 'hidden' }} mt-2 transition-opacity duration-200">
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono flex items-center">
                            <i class="fa-solid fa-link text-indigo-600 dark:text-indigo-400 text-xs mr-1.5"></i>
                            <span class="select-none">{{ url('/') }}/book/</span><span id="slugPreview" class="font-semibold text-indigo-600 dark:text-indigo-400">{{ old('slug', $business->slug) }}</span>
                        </p>
                    </div>
                    @error('slug')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @else
                        <p id="slugError" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
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
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone', $business->phone) }}"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="+375 (29) 123-45-67">
                    <p class="mt-2.5 text-xs text-slate-500 dark:text-slate-400">
                        Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                    </p>
                    @error('phone')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
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
                    <textarea id="description" name="description" rows="4" maxlength="500"
                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Краткое описание вашего бизнеса...">{{ old('description', $business->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('settings.index') }}" 
           class="px-4 py-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            Отмена
        </a>
        <button type="submit"
            class="px-4 py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Сохранить изменения
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // ==================== КОНСТАНТЫ И ПЕРЕМЕННЫЕ ====================
    let slugIsChecking = false;
    let slugCheckTimeout = null;
    let currentAbortController = null;
    const currentBusinessId = {{ $business->id }};
    const currentBusinessSlug = '{{ $business->slug }}';

    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    const SLUG_MIN_LENGTH = 3;
    const SLUG_CHECK_DEBOUNCE = 500;
    const SLUG_CHECK_TIMEOUT = 10000;

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

    const slugElements = {
        input: document.getElementById('slug'),
        checking: document.getElementById('slugChecking'),
        available: document.getElementById('slugAvailable'),
        unavailable: document.getElementById('slugUnavailable'),
        error: document.getElementById('slugError'),
        preview: document.getElementById('slugPreview'),
        previewCard: document.getElementById('slugPreviewCard'),
        container: document.getElementById('slugContainer')
    };

    function transliterate(text) {
        if (!text) return '';
        let result = '';
        for (let i = 0; i < text.length; i++) {
            result += translitMap[text[i]] !== undefined ? translitMap[text[i]] : text[i];
        }
        return result;
    }

    function sanitizeSlug(input, options = {}) {
        if (!input) return '';
        const { removeTrailingDash = false, removeLeadingDash = true } = options;
        let result = input.replace(/\s+/g, '-');
        result = transliterate(result);
        result = result.toLowerCase();
        result = result.replace(/[^a-z0-9\-]/g, '');
        result = result.replace(/-+/g, '-');
        if (removeLeadingDash) {
            result = result.replace(/^-+/, '');
        }
        if (removeTrailingDash) {
            result = result.replace(/-+$/, '');
        }
        return result;
    }

    function formatSlug(input) {
        return sanitizeSlug(input, { removeTrailingDash: true, removeLeadingDash: true });
    }

    function sanitizeSlugInput(input) {
        return sanitizeSlug(input, { removeTrailingDash: false, removeLeadingDash: true });
    }

    function validateSlugFormat(slug) {
        return slugRegex.test(slug);
    }

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

    function setSlugState(state, message = '') {
        if (slugElements.checking) slugElements.checking.classList.add('hidden');
        if (slugElements.available) slugElements.available.classList.add('hidden');
        if (slugElements.unavailable) slugElements.unavailable.classList.add('hidden');
        if (slugElements.error) slugElements.error.classList.add('hidden');

        switch (state) {
            case 'checking':
                if (slugElements.checking) slugElements.checking.classList.remove('hidden');
                break;
            case 'available':
                if (slugElements.available) slugElements.available.classList.remove('hidden');
                if (slugElements.preview && slugElements.input) {
                    slugElements.preview.textContent = slugElements.input.value || currentBusinessSlug;
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
                    slugElements.preview.textContent = currentBusinessSlug;
                }
                if (slugElements.previewCard) {
                    slugElements.previewCard.classList.add('hidden');
                }
                break;
        }
    }

    async function checkSlugAvailability(slug) {
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

        // Если slug не изменился, считаем его доступным
        if (slug === currentBusinessSlug) {
            setSlugState('available');
            return;
        }

        setSlugState('checking');
        slugIsChecking = true;

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
                body: JSON.stringify({ slug: slug, business_id: currentBusinessId }),
                signal: currentAbortController.signal
            });

            clearTimeout(timeoutId);

            if (response.status === 429) {
                const retryAfter = response.headers.get('Retry-After') || 60;
                setSlugState('unavailable', `Слишком много запросов. Попробуйте через ${retryAfter} секунд.`);
                slugIsChecking = false;
                return;
            }

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
            
            if (error.name === 'AbortError') {
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

    function resetSlugValidation() {
        setSlugState('reset');
        if (slugElements.previewCard) {
            slugElements.previewCard.classList.add('opacity-0');
            setTimeout(() => {
                if (slugElements.previewCard) {
                    slugElements.previewCard.classList.add('hidden');
                }
            }, 200);
        }
    }

    function handleSlugKeydown(e) {
        if (e.key === ' ' || e.keyCode === 32) {
            e.preventDefault();
            const input = e.target;
            const start = input.selectionStart || 0;
            const end = input.selectionEnd || start;
            const value = input.value;
            const newValue = value.substring(0, start) + '-' + value.substring(end);
            input.value = newValue;
            const newPosition = start + 1;
            setCursorPosition(input, newPosition);
            setTimeout(() => {
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }, 0);
        }
    }

    function handleSlugInput(e) {
        const input = e.target;
        const originalValue = input.value;
        const cursorPosition = input.selectionStart || 0;
        const textBeforeCursor = originalValue.substring(0, cursorPosition);
        const sanitizedValue = sanitizeSlugInput(originalValue);
        
        if (originalValue !== sanitizedValue) {
            const sanitizedBefore = sanitizeSlugInput(textBeforeCursor);
            const newCursorPosition = sanitizedBefore.length;
            input.value = sanitizedValue;
            setCursorPosition(input, newCursorPosition);
        }
        
        const slug = sanitizedValue.trim();
        
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
        
        if (slugElements.preview) {
            slugElements.preview.textContent = slug || currentBusinessSlug;
        }
        
        if (slugCheckTimeout) {
            clearTimeout(slugCheckTimeout);
        }
        
        if (!slug) {
            resetSlugValidation();
            return;
        }
        
        slugCheckTimeout = setTimeout(() => {
            checkSlugAvailability(slug);
        }, SLUG_CHECK_DEBOUNCE);
    }

    function handleSlugBlur(e) {
        const input = e.target;
        const formatted = formatSlug(input.value);
        
        if (input.value !== formatted) {
            input.value = formatted;
            if (slugElements.preview) {
                slugElements.preview.textContent = formatted || currentBusinessSlug;
            }
        }
        
        const slug = formatted.trim();
        if (slug && slug.length >= SLUG_MIN_LENGTH) {
            checkSlugAvailability(slug);
        } else {
            resetSlugValidation();
        }
    }

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        if (slugElements.input) {
            slugElements.input.addEventListener('keydown', handleSlugKeydown);
            slugElements.input.addEventListener('input', handleSlugInput);
            slugElements.input.addEventListener('blur', handleSlugBlur);
            
            // Показываем предпросмотр если slug уже есть
            if (slugElements.input.value) {
                if (slugElements.previewCard) {
                    slugElements.previewCard.classList.remove('hidden');
                }
                if (slugElements.preview) {
                    slugElements.preview.textContent = slugElements.input.value;
                }
            }
        }
    });
</script>
@endpush

