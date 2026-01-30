@extends('layouts.user')

@section('title', 'Редактирование бизнеса - Cliently')
@section('page-title', 'Данные бизнеса')
@section('page-description', 'Измените информацию о вашем бизнесе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Редактирование', 'url' => null],
    ]" />
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('settings.business.update') }}">
        @csrf
        @method('PATCH')

        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Название -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Название <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           value="{{ old('name', $business->name) }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        ID <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="slug" 
                               name="slug" 
                               required
                               value="{{ old('slug', $business->slug) }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('slug') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono transition-colors"
                               placeholder="vash-biznes">

                        <!-- Индикаторы проверки -->
                        <div id="slugChecking" class="hidden absolute right-3 top-1/2 transform -translate-y-1/2">
                            <div
                                class="animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full">
                            </div>
                        </div>

                        <div id="slugAvailable"
                            class="hidden absolute right-3 top-1/2 transform -translate-y-1/2 text-emerald-500">
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>

                        <div id="slugUnavailable"
                            class="hidden absolute right-3 top-1/2 transform -translate-y-1/2 text-rose-500">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </div>
                    </div>
                    @error('slug')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @else
                        <p id="slugError" class="mt-1 text-sm text-rose-600 dark:text-rose-400 hidden"></p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                       Минимум 3 символа.
                    </p>
                </div>

                <!-- Описание -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Описание
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              maxlength="500"
                              class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors"
                              placeholder="Краткое описание вашего бизнеса...">{{ old('description', $business->description) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Максимум 500 символов
                    </p>
                </div>

                <!-- Телефон -->
                @php
                    $phoneCountry = $business->primaryPhone?->country ?? $countries->first();
                    $phoneNational = '';
                    if ($business->primaryPhone && $phoneCountry) {
                        $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                        $phoneDig = preg_replace('/\D/', '', $business->primaryPhone->phone);
                        $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                    }
                @endphp
                <div class="md:col-span-2" id="phoneCountryBlockEdit"
                    data-countries="{{ json_encode($countries->map(fn ($c) => ['id' => $c->id, 'code' => $c->calling_code, 'name' => $c->name])->values()) }}"
                    data-old-phone="{{ old('phone', $business->phone) }}"
                    data-old-country="{{ old('phone_country_id', $business->primaryPhone?->country_id) }}">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Телефон <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="sm:w-48">
                            <select id="phone_country_id" name="phone_country_id" required
                                class="w-full px-4 py-2.5 border {{ $errors->has('phone_country_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ old('phone_country_id', $business->primaryPhone?->country_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} {{ $c->calling_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 relative">
                            <span id="phoneCodePrefixEdit" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm pointer-events-none"></span>
                            <input type="tel" id="phone_national_edit" inputmode="numeric" maxlength="15" required
                                value="{{ old('phone_national', $phoneNational) }}"
                                class="w-full pl-14 pr-4 py-2.5 border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                                placeholder="291234567">
                            <input type="hidden" name="phone" id="phone_edit" value="{{ old('phone', $business->phone) }}">
                        </div>
                    </div>
                    @error('phone_country_id')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    @error('phone')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Формат: код страны + номер без ведущих нулей</p>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
            <a href="{{ route('settings.index') }}"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fa-solid fa-check text-sm"></i>
                <span>Сохранить изменения</span>
            </button>
        </div>
    </form>
</div>

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
            'а': 'a',
            'б': 'b',
            'в': 'v',
            'г': 'g',
            'д': 'd',
            'е': 'e',
            'ё': 'yo',
            'ж': 'zh',
            'з': 'z',
            'и': 'i',
            'й': 'y',
            'к': 'k',
            'л': 'l',
            'м': 'm',
            'н': 'n',
            'о': 'o',
            'п': 'p',
            'р': 'r',
            'с': 's',
            'т': 't',
            'у': 'u',
            'ф': 'f',
            'х': 'h',
            'ц': 'ts',
            'ч': 'ch',
            'ш': 'sh',
            'щ': 'sch',
            'ъ': '',
            'ы': 'y',
            'ь': '',
            'э': 'e',
            'ю': 'yu',
            'я': 'ya',
            'А': 'A',
            'Б': 'B',
            'В': 'V',
            'Г': 'G',
            'Д': 'D',
            'Е': 'E',
            'Ё': 'Yo',
            'Ж': 'Zh',
            'З': 'Z',
            'И': 'I',
            'Й': 'Y',
            'К': 'K',
            'Л': 'L',
            'М': 'M',
            'Н': 'N',
            'О': 'O',
            'П': 'P',
            'Р': 'R',
            'С': 'S',
            'Т': 'T',
            'У': 'U',
            'Ф': 'F',
            'Х': 'H',
            'Ц': 'Ts',
            'Ч': 'Ch',
            'Ш': 'Sh',
            'Щ': 'Sch',
            'Ъ': '',
            'Ы': 'Y',
            'Ь': '',
            'Э': 'E',
            'Ю': 'Yu',
            'Я': 'Ya'
        };

        const slugElements = {
            input: document.getElementById('slug'),
            checking: document.getElementById('slugChecking'),
            available: document.getElementById('slugAvailable'),
            unavailable: document.getElementById('slugUnavailable'),
            error: document.getElementById('slugError')
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
            const {
                removeTrailingDash = false, removeLeadingDash = true
            } = options;
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
            return sanitizeSlug(input, {
                removeTrailingDash: true,
                removeLeadingDash: true
            });
        }

        function sanitizeSlugInput(input) {
            return sanitizeSlug(input, {
                removeTrailingDash: false,
                removeLeadingDash: true
            });
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
                setSlugState('formatError',
                    'Только латинские буквы в нижнем регистре, цифры и одиночные дефисы. Дефисы не могут быть в начале или конце.'
                    );
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

                const response = await fetch('{{ route('api.slug.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        slug: slug,
                        business_id: currentBusinessId
                    }),
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
                    setSlugState('unavailable', data.message ||
                        'Не удалось проверить доступность slug. Попробуйте позже.');
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
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
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
            }

            const block = document.getElementById('phoneCountryBlockEdit');
            const sel = document.getElementById('phone_country_id');
            const national = document.getElementById('phone_national_edit');
            const hidden = document.getElementById('phone_edit');
            const prefix = document.getElementById('phoneCodePrefixEdit');

            function updatePhone() {
                const opt = sel && sel.options[sel.selectedIndex];
                const code = opt ? (opt.dataset.code || '').replace(/\D/g, '') : '';
                const digits = national && national.value ? national.value.replace(/\D/g, '') : '';
                const full = code && digits ? '+' + code + digits : '';
                if (hidden) hidden.value = full;
                if (prefix) prefix.textContent = opt ? opt.dataset.code || '' : '';
            }

            if (sel) {
                sel.addEventListener('change', function() {
                    updatePhone();
                    if (national) national.placeholder = (this.options[this.selectedIndex].dataset.code === '+375') ? '291234567' : '9123456789';
                });
            }
            if (national) {
                national.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 15);
                    updatePhone();
                });
            }

            if (sel && sel.options.length) {
                const opt = sel.options[sel.selectedIndex];
                if (prefix) prefix.textContent = opt ? opt.dataset.code || '' : '';
                if (national) national.placeholder = (opt && opt.dataset.code === '+375') ? '291234567' : '9123456789';
                const op = block && block.dataset.oldPhone ? block.dataset.oldPhone : '';
                const oc = block && block.dataset.oldCountry ? String(block.dataset.oldCountry) : '';
                if (op && oc && sel.value === oc && opt) {
                    const codeDigits = (opt.dataset.code || '').replace(/\D/g, '');
                    const phoneDigits = op.replace(/\D/g, '');
                    if (phoneDigits.startsWith(codeDigits)) national.value = phoneDigits.slice(codeDigits.length);
                }
                updatePhone();
            }

            const form = block && block.closest('form');
            if (form) form.addEventListener('submit', updatePhone);
        });
    </script>
@endpush
