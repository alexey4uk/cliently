@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-baseline justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Создание бизнеса</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">Шаг 1 из 4</p>
        </div>
        
        <!-- Индикатор прогресса -->
        <div class="flex items-center gap-2">
            <div class="flex items-center">
                @for($i = 1; $i <= 4; $i++)
                    <div class="w-2 h-2 rounded-full {{ $i == 1 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }} {{ $i < 4 ? 'mr-1' : '' }}"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Информационная карточка -->
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 dark:border-indigo-900 dark:bg-indigo-900/30 p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-lightbulb text-indigo-600 dark:text-indigo-400 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-indigo-800 dark:text-indigo-300">
                    <span class="font-medium">Ваш бизнес будет доступен по адресу:</span><br>
                    <span class="font-mono">beautybook.ru/<span id="slugPreview" class="font-semibold">ваш-slug</span></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.business.store') }}" class="space-y-6">
        @csrf
        
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Название бизнеса*</label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                    class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                    placeholder="Например: Elite Beauty Salon"
                    autofocus>
                @error('name')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Slug*</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-sm">
                        beautybook.ru/
                    </span>
                    <div class="flex-1 relative">
                        <input type="text" id="slug" name="slug" required value="{{ old('slug') }}"
                            class="w-full px-3 py-2 text-sm rounded-r-md border {{ $errors->has('slug') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                            placeholder="elite-beauty">
                        
                        <!-- Индикаторы проверки -->
                        <div id="slugChecking" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                            <div class="animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                        </div>
                        
                        <div id="slugAvailable" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2 text-emerald-500">
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>
                        
                        <div id="slugUnavailable" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2 text-rose-500">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Только латинские буквы в нижнем регистре, цифры и одиночные дефисы
                </p>
                @error('slug')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @else
                    <p id="slugError" class="mt-1 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                    placeholder="Краткое описание вашего бизнеса...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Телефон*</label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                        class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="+7 (999) 123-45-67">
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Почта</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="info@example.com">
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Назад в дашборд
            </a>
            
            <button type="submit" id="submitButton"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Продолжить <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let isSlugAvailable = false;
    let slugCheckTimeout = null;
    let slugIsChecking = false;
    let slugFormatIsValid = false;

    // Регулярное выражение для проверки формата slug
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

    function validateSlugFormat(slug) {
        return slugRegex.test(slug);
    }

    function formatSlug(input) {
        let formatted = input.toLowerCase().replace(/[^a-z0-9\-]/g, '');
        formatted = formatted.replace(/-+/g, '-');
        formatted = formatted.replace(/^-+/, '').replace(/-+$/, '');
        return formatted;
    }

    function sanitizeSlugInput(input, cursorPosition) {
        let sanitized = input.toLowerCase();
        
        // Блокируем дефис в начале
        if (cursorPosition === 1 && sanitized.charAt(0) === '-') {
            return input.slice(0, -1);
        }
        
        // Блокируем двойные дефисы
        if (cursorPosition > 1 && sanitized.charAt(cursorPosition - 1) === '-') {
            const prevChar = sanitized.charAt(cursorPosition - 2);
            if (prevChar === '-') {
                return input.slice(0, -1);
            }
        }
        
        sanitized = sanitized.replace(/[^a-z0-9\-]/g, '');
        return sanitized;
    }

    async function checkSlugAvailability(slug) {
        if (!slug || slug.length < 3) {
            resetSlugValidation();
            return;
        }

        if (!validateSlugFormat(slug)) {
            showSlugFormatError('Только латинские буквы в нижнем регистре, цифры и одиночные дефисы. Дефисы не могут быть в начале или конце.');
            slugFormatIsValid = false;
            isSlugAvailable = false;
            updateSubmitButton();
            return;
        }

        slugFormatIsValid = true;
        showSlugChecking();
        slugIsChecking = true;

        try {
            const response = await fetch('{{ route("api.slug.check") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ slug: slug })
            });

            const data = await response.json();
            
            if (data.available === true) {
                showSlugAvailable();
                isSlugAvailable = true;
            } else {
                showSlugUnavailable('Этот slug уже занят. Пожалуйста, выберите другой.');
                isSlugAvailable = false;
            }
        } catch (error) {
            console.error('Ошибка при проверке slug:', error);
            showSlugUnavailable('Не удалось проверить доступность slug. Попробуйте позже.');
            isSlugAvailable = false;
        } finally {
            slugIsChecking = false;
            updateSubmitButton();
        }
    }

    function showSlugChecking() {
        document.getElementById('slugChecking').classList.remove('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        document.getElementById('slugError').classList.add('hidden');
        
        const slugInput = document.getElementById('slug');
        slugInput.classList.remove('border-emerald-500', 'border-rose-500');
        slugInput.classList.add('border-slate-300', 'dark:border-slate-700');
    }

    function showSlugAvailable() {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.remove('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        document.getElementById('slugError').classList.add('hidden');
        document.getElementById('slugPreview').textContent = document.getElementById('slug').value;
        
        const slugInput = document.getElementById('slug');
        slugInput.classList.remove('border-rose-500', 'border-slate-300', 'dark:border-slate-700');
        slugInput.classList.add('border-emerald-500');
    }

    function showSlugUnavailable(message) {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.remove('hidden');
        
        const errorElement = document.getElementById('slugError');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        
        const slugInput = document.getElementById('slug');
        slugInput.classList.remove('border-emerald-500', 'border-slate-300', 'dark:border-slate-700');
        slugInput.classList.add('border-rose-500');
    }

    function showSlugFormatError(message) {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        
        const errorElement = document.getElementById('slugError');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        
        const slugInput = document.getElementById('slug');
        slugInput.classList.remove('border-emerald-500', 'border-slate-300', 'dark:border-slate-700');
        slugInput.classList.add('border-rose-500');
    }

    function resetSlugValidation() {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        document.getElementById('slugError').classList.add('hidden');
        document.getElementById('slugPreview').textContent = 'ваш-slug';
        
        const slugInput = document.getElementById('slug');
        slugInput.classList.remove('border-emerald-500', 'border-rose-500');
        slugInput.classList.add('border-slate-300', 'dark:border-slate-700');
        
        slugFormatIsValid = false;
        isSlugAvailable = false;
        updateSubmitButton();
    }

    function updateSubmitButton() {
        const submitButton = document.getElementById('submitButton');
        const slugValue = document.getElementById('slug').value.trim();
        const nameValue = document.getElementById('name').value.trim();
        const phoneValue = document.getElementById('phone').value.trim();
        
        // Проверяем, все ли обязательные поля заполнены и slug доступен
        submitButton.disabled = slugIsChecking || !slugFormatIsValid || !isSlugAvailable || 
                               !nameValue || !phoneValue || !slugValue;
    }

    // Обработка ввода slug
    document.getElementById('slug').addEventListener('input', function(e) {
        const originalValue = e.target.value;
        const cursorPosition = e.target.selectionStart;
        
        const sanitizedValue = sanitizeSlugInput(originalValue, cursorPosition);
        
        if (originalValue !== sanitizedValue) {
            e.target.value = sanitizedValue;
            const newCursorPosition = Math.max(0, cursorPosition - 1);
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);
        }
        
        const slug = sanitizedValue.trim();
        
        if (slugCheckTimeout) {
            clearTimeout(slugCheckTimeout);
        }
        
        if (!slug) {
            resetSlugValidation();
            updateSubmitButton();
            return;
        }
        
        slugCheckTimeout = setTimeout(() => {
            checkSlugAvailability(slug);
        }, 500);
        
        updateSubmitButton();
    });

    // Форматирование при уходе с поля
    document.getElementById('slug').addEventListener('blur', function(e) {
        const formattedSlug = formatSlug(e.target.value);
        if (e.target.value !== formattedSlug) {
            e.target.value = formattedSlug;
        }
        
        if (formattedSlug.trim()) {
            checkSlugAvailability(formattedSlug.trim());
        } else {
            resetSlugValidation();
        }
    });

    // Проверка других обязательных полей
    document.getElementById('name').addEventListener('input', updateSubmitButton);
    document.getElementById('phone').addEventListener('input', updateSubmitButton);

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        // Если есть старое значение slug, проверяем его
        const slugInput = document.getElementById('slug');
        if (slugInput.value) {
            checkSlugAvailability(slugInput.value.trim());
        }
        
        updateSubmitButton();
    });
</script>
@endpush