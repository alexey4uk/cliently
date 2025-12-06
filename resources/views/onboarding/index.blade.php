@extends('layouts.user')

@section('title', 'Настройка профиля - Cliently')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
            <!-- Заголовок -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Настройте профиль
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Заполните основные данные, чтобы начать работу
                </p>
            </div>

            <!-- Форма -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <form id="onboarding-form" method="POST" action="{{ route('onboarding.store') }}">
                    @csrf
                    @method('PUT')
                    <!-- Поле 1: Имя/Название -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <span class="flex items-center">
                            <i class="fas fa-user-tie text-gray-400 mr-2 text-sm"></i>
                            ИП или ФИО
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   class="w-full px-4 py-3 pl-11 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 transition-colors"
                                   placeholder="ИП Иванов"
                                   required>
                            <div class="absolute left-3 top-3.5 text-gray-400">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Поле 2: Адрес профиля (slug) -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                <span class="flex items-center">
                                    <i class="fas fa-link text-gray-400 mr-2 text-sm"></i>
                                    Адрес вашего профиля
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <div id="slug-status" class="flex items-center hidden">
                                <span class="text-xs font-medium mr-2"></span>
                                <i class="fas"></i>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">/</span>
                            </div>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   value="{{ old('slug') }}"
                                   maxlength="30"
                                   class="w-full px-4 py-3text-gray-900 pl-5 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                   placeholder="ivanov"
                                   pattern="^[a-z0-9\-]+$"
                                   title="Только латинские буквы в нижнем регистре, цифры и дефисы"
                                   required>

                            <!-- Уведомление когда slug СВОБОДЕН -->
                            <div id="slug-available" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none hidden">
                                <div class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs font-medium hidden md:block">Свободен</span>
                                </div>
                            </div>

                            <!-- Уведомление когда slug ЗАНЯТ -->
                            <div id="slug-taken" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none hidden">
                                <div class="flex items-center gap-1.5 text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs font-medium hidden md:block">Занят</span>
                                </div>
                            </div>
                        </div>

                        <!-- Сообщения об ошибках/успехе -->
                        <div id="slug-message" class="mt-2 text-xs hidden">
                            <div class="flex items-center">
                                <i class="fas mr-2"></i>
                                <span></span>
                            </div>
                        </div>

                        <!-- Объяснение для пользователя -->
                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-question-circle text-blue-500 mt-0.5"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">
                                        Для чего нужен адрес профиля?
                                    </h4>
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        Клиенты смогут найти вас по ссылке
                                        <span class="font-mono bg-blue-100 dark:bg-blue-800 px-1 rounded">cliently.by/<span id="slug-info-block">ваш-адрес</span></span>.<br>
                                        Используйте латинские буквы, цифры и дефисы.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Поле 3: Краткое описание -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <span class="flex items-center">
                            <i class="fas fa-comment-alt text-gray-400 mr-2 text-sm"></i>
                            Короткое описание
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="short_description"
                                   id="short_description"
                                   maxlength="100"
                                   class="w-full px-4 py-3 pl-11 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 transition-colors"
                                   placeholder="Профессиональный парикмахер с 5-летним опытом"
                                   required>
                            <div class="absolute left-3 top-3.5 text-gray-400">
                                <i class="fas fa-align-left"></i>
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Отображается в карточке профиля
                            </p>
                            <span id="char-counter" class="text-xs text-gray-400">0/100</span>
                        </div>
                    </div>

                    <!-- Поле 4: Полное описание -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <span class="flex items-center">
                            <i class="fas fa-file-alt text-gray-400 mr-2 text-sm"></i>
                            Полное описание
                            <span class="text-gray-400 text-xs ml-2">(не обязательно)</span>
                        </span>
                        </label>
                        <div class="relative">
                        <textarea
                            name="full_description"
                            rows="4"
                            class="w-full px-4 py-3 pl-11 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 transition-colors resize-none"
                            placeholder="Расскажите о своих услугах, подходе к работе и опыте..."></textarea>
                            <div class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-edit"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Кнопки -->
            <div class="w-full">
                <button type="submit"
                        id="submit-btn"
                        form="onboarding-form"
                        class="w-full px-6 py-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors text-center disabled:opacity-50 disabled:cursor-not-allowed text-lg">
                    Сохранить и продолжить
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>

            <!-- Подсказка -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            Все данные можно изменить позже в настройках профиля
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slugInput = document.getElementById('slug')
            const charCounter = document.getElementById('char-counter')
            const InputShortDescription = document.getElementById('short_description')
            const slugInfoBlock = document.getElementById('slug-info-block')

            let debounceTimer;
            let abortController; // Для отмены предыдущих запросов

            async function checkSlugAvailability(slug) {
                if (!slug || slug.length < 3) {
                    hideAllMessages();
                    return;
                }

                // Отменяем предыдущий запрос, если он еще выполняется
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                try {
                    const response = await fetch('{{ route("api.slug.check") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ slug: slug }),
                        signal: abortController.signal
                    });

                    if (response.ok) {
                        const data = await response.json();
                        updateSlugMessages(data.available);
                    }
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error('Error checking slug:', error);
                    }
                }
            }

            function formatSlug(text) {
                if (!text) return '';
                return text
                    .toLowerCase()
                    .replace(/[^a-z0-9\-]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+/, '');
            }

            function updateSlugMessages(isAvailable) {
                if (isAvailable) {
                    document.getElementById('slug-available').classList.remove('hidden')
                    document.getElementById('slug-taken').classList.add('hidden')
                } else {
                    document.getElementById('slug-available').classList.add('hidden')
                    document.getElementById('slug-taken').classList.remove('hidden')
                }
            }

            function hideAllMessages() {
                document.getElementById('slug-available').classList.add('hidden')
                document.getElementById('slug-taken').classList.add('hidden')
            }

            slugInput.addEventListener('input', function (){
                const originalValue = slugInput.value;
                const formatted = formatSlug(slugInput.value);

                if (formatted !== originalValue) {
                    slugInput.value = formatted;
                }

                // Обновляем информационный блок
                slugInfoBlock.textContent = slugInput.value;

                // Debounce для API-запроса
                clearTimeout(debounceTimer);
                hideAllMessages(); // Скрываем сообщения пока ждем

                debounceTimer = setTimeout(() => {
                    checkSlugAvailability(slugInput.value);
                }, 300); // Оптимальная задержка
            });

            slugInput.addEventListener('blur', function () {
                // Удаляем дефис в конце только если он один
                if (this.value.endsWith('-') && !this.value.endsWith('--')) {
                    this.value = this.value.slice(0, -1);
                    checkSlugAvailability(this.value);
                }
            });

            // Очищаем таймер при размонтировании
            window.addEventListener('beforeunload', function() {
                clearTimeout(debounceTimer);
                if (abortController) {
                    abortController.abort();
                }
            });

            InputShortDescription.addEventListener('input', function () {
                const length = InputShortDescription.value.length;
                charCounter.textContent = `${length}/100`;
            })
        });
    </script>
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            const slugInput = document.getElementById('slug')--}}
{{--            const slugInfoBlock = document.getElementById('slug-info-block')--}}

{{--            async function checkSlugAvailability(slug) {--}}
{{--                if (!slug || slug.length < 3) return;--}}

{{--                const response = await fetch('{{ route("api.slug.check") }}', {--}}
{{--                    method: 'POST',--}}
{{--                    headers: {--}}
{{--                        'Content-Type': 'application/json',--}}
{{--                        'X-CSRF-TOKEN': '{{ csrf_token() }}',--}}
{{--                        'Accept': 'application/json'--}}
{{--                    },--}}
{{--                    body: JSON.stringify({ slug: slug })--}}
{{--                });--}}

{{--                if (response.ok) {--}}
{{--                    const data = await response.json();--}}

{{--                    if (data.available) {--}}
{{--                        document.getElementById('slug-available').classList.remove('hidden')--}}
{{--                        document.getElementById('slug-taken').classList.add('hidden')--}}
{{--                    } else {--}}
{{--                        document.getElementById('slug-available').classList.add('hidden')--}}
{{--                        document.getElementById('slug-taken').classList.remove('hidden')--}}
{{--                    }--}}
{{--                } else {--}}
{{--                    console.error('Error checking slug:', response.statusText);--}}
{{--                }--}}
{{--            }--}}

{{--            // Форматирование slug (без сохранения позиции курсора)--}}
{{--            function formatSlug(text) {--}}
{{--                if (!text) return '';--}}
{{--                return text--}}
{{--                    .toLowerCase()--}}
{{--                    .replace(/[^a-z0-9\-]/g, '-')--}}
{{--                    .replace(/-+/g, '-')--}}
{{--                    .replace(/^-+/, '');--}}
{{--            }--}}

{{--            slugInput.addEventListener('input', function (){--}}
{{--                const originalValue = slugInput.value;--}}
{{--                const formatted = formatSlug(slugInput.value);--}}

{{--                if (formatted !== originalValue) {--}}
{{--                    slugInput.value = formatted;--}}
{{--                }--}}
{{--                checkSlugAvailability(slugInput.value)--}}
{{--                slugInfoBlock.textContent = this.value--}}
{{--            });--}}

{{--            slugInput.addEventListener('blur', function () {--}}
{{--                // Удаляем дефис в конце только если он один--}}
{{--                if (this.value.endsWith('-') && !this.value.endsWith('--')) {--}}
{{--                    this.value = this.value.slice(0, -1);--}}
{{--                    checkSlugAvailability(this.value);--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endpush
@endsection
