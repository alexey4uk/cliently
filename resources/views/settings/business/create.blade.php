@extends('layouts.user')

@section('title', 'Создание бизнеса - Cliently')
@section('page-title', 'Создание бизнеса')
@section('page-description', 'Основная информация о вашем бизнесе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Главная', 'url' => route('dashboard')],
        ['title' => 'Создание бизнеса', 'url' => null],
    ]" />
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">Создание бизнеса</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Основная информация о вашем бизнесе</p>
    </div>

    <form method="POST" action="{{ route('settings.business.store') }}" class="space-y-6" id="businessCreateForm">
        @csrf

        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Организация <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            value="{{ old('name') }}"
                            class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                            placeholder="ИП Иванов"
                            autofocus>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Адрес страницы онлайн-записи <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">
                            
                        </p>
                        <div class="relative">
                            <input type="text" id="slug" name="slug" required
                                value="{{ old('slug') }}"
                                class="w-full px-4 py-2.5 border {{ $errors->has('slug') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono transition-colors"
                                placeholder="ip-ivanov"
                                autocomplete="off">
                            <div id="slugChecking" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                <div class="animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                            </div>
                            <div id="slugAvailable" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500">
                                <i class="fa-solid fa-check text-sm"></i>
                            </div>
                            <div id="slugUnavailable" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-rose-500">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap items-baseline gap-1 text-xs text-slate-500 dark:text-slate-400">
                            <span>Ссылка для клиентов:</span>
                            <span class="font-mono text-indigo-600 dark:text-indigo-400 break-all">
                                {{ rtrim(config('app.url'), '/') }}/book/<span id="slugPreviewValue">{{ old('slug', 'ivanov') }}</span>
                            </span>
                        </div>
                        @error('slug')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @else
                            <p id="slugError" class="mt-1 text-sm text-rose-600 dark:text-rose-400 hidden"></p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Информация о владельце</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Имя <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                            value="{{ old('first_name') }}"
                            class="w-full px-4 py-2.5 border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                        @error('first_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Фамилия
                        </label>
                        <input type="text" id="last_name" name="last_name"
                            value="{{ old('last_name') }}"
                            class="w-full px-4 py-2.5 border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                        @error('last_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Контактная информация</h2>
                <x-phone-input
                    :countries="$countries"
                    block-id="phoneCountryBlock"
                    :old-phone="old('phone')"
                    :old-country-id="old('phone_country_id')"
                    :old-national="old('phone_national')"
                    :required="true"
                    helper-text="Код страны и номер без ведущих нулей"
                />
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Дополнительная информация</h2>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Описание
                    </label>
                    <textarea id="description" name="description" rows="3" maxlength="500"
                        class="w-full px-4 py-2.5 border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors"
                        placeholder="Краткое описание вашего бизнеса...">{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Максимум 500 символов</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Создать бизнес
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const slugEl = document.getElementById('slug');
    const slugPreviewValue = document.getElementById('slugPreviewValue');
    const slugChecking = document.getElementById('slugChecking');
    const slugAvailable = document.getElementById('slugAvailable');
    const slugUnavailable = document.getElementById('slugUnavailable');
    const slugError = document.getElementById('slugError');
    const SLUG_MIN = 3;
    const DEBOUNCE = 500;
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    let slugTimeout = null;
    let slugAbort = null;

    function slugReset() {
        if (slugChecking) slugChecking.classList.add('hidden');
        if (slugAvailable) slugAvailable.classList.add('hidden');
        if (slugUnavailable) slugUnavailable.classList.add('hidden');
        if (slugError) { slugError.classList.add('hidden'); slugError.textContent = ''; }
    }

    function slugSet(state, msg) {
        slugReset();
        if (state === 'checking' && slugChecking) slugChecking.classList.remove('hidden');
        else if (state === 'available' && slugAvailable) slugAvailable.classList.remove('hidden');
        else if (state === 'unavailable' && slugUnavailable) { slugUnavailable.classList.remove('hidden'); if (slugError && msg) { slugError.textContent = msg; slugError.classList.remove('hidden'); } }
        else if (state === 'formatError' && slugError && msg) { slugError.textContent = msg; slugError.classList.remove('hidden'); }
    }

    function sanitize(s) {
        if (!s) return '';
        return s.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '');
    }
    function updateSlugPreview() {
        if (slugPreviewValue && slugEl) {
            const v = sanitize(slugEl.value);
            slugPreviewValue.textContent = v || 'ваш-адрес';
        }
    }

    async function checkSlug(v) {
        if (!v || v.length < SLUG_MIN) { slugReset(); return; }
        if (!slugRegex.test(v)) { slugSet('formatError', 'Только латиница, цифры и дефисы. Минимум 3 символа.'); return; }
        slugSet('checking');
        if (slugAbort) slugAbort.abort();
        slugAbort = new AbortController();
        try {
            const r = await fetch('{{ route("api.slug.check") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ slug: v }),
                signal: slugAbort.signal
            });
            const d = await r.json();
            if (d.available === true) slugSet('available');
            else slugSet('unavailable', d.message || 'Этот адрес уже занят.');
        } catch (e) {
            if (e.name !== 'AbortError') slugSet('unavailable', 'Не удалось проверить. Попробуйте позже.');
        } finally { slugAbort = null; }
    }

    if (slugEl) {
        slugEl.addEventListener('keydown', function(e) {
            if (e.key === ' ') { e.preventDefault(); const v = this.value, i = this.selectionStart; this.value = v.slice(0, i) + '-' + v.slice(i); this.setSelectionRange(i + 1, i + 1); }
        });
        slugEl.addEventListener('input', function() {
            const cp = this.selectionStart;
            const s = sanitize(this.value);
            if (this.value !== s) { this.value = s; this.setSelectionRange(Math.min(cp, s.length), Math.min(cp, s.length)); }
            updateSlugPreview();
            if (slugTimeout) clearTimeout(slugTimeout);
            slugTimeout = setTimeout(() => checkSlug(s), DEBOUNCE);
        });
        slugEl.addEventListener('blur', function() {
            const s = sanitize(this.value);
            if (this.value !== s) this.value = s;
            updateSlugPreview();
            if (s && s.length >= SLUG_MIN) checkSlug(s);
            else slugReset();
        });
        updateSlugPreview();
        if (slugEl.value && slugEl.value.length >= SLUG_MIN) checkSlug(sanitize(slugEl.value));
    }

})();
</script>
@endpush
