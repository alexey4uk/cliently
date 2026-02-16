@extends('layouts.user')

@section('title', 'Данные бизнеса - Cliently')
@section('page-title', 'Данные бизнеса')
@section('page-description', 'Измените информацию о вашем бизнесе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Данные бизнеса', 'url' => null],
    ]" />
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">Данные бизнеса</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Основная информация и контакты</p>
    </div>

    <form method="POST" action="{{ route('settings.business.update') }}" class="space-y-6" id="businessEditForm">
        @csrf
        @method('PATCH')

        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Организация <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            value="{{ old('name', $business->name) }}"
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
                            По этой ссылке клиенты записываются к вам. Только латиница, цифры и дефис.
                        </p>
                        <div class="relative">
                            <input type="text" id="slug" name="slug" required
                                value="{{ old('slug', $business->slug) }}"
                                class="w-full px-4 py-2.5 border {{ $errors->has('slug') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono transition-colors"
                                placeholder="например: salon-krasoty"
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
                                {{ rtrim(config('app.url'), '/') }}/book/<span id="slugPreviewValue">{{ old('slug', $business->slug) }}</span>
                            </span>
                        </div>
                        @error('slug')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @else
                            <p id="slugError" class="mt-1 text-sm text-rose-600 dark:text-rose-400 hidden"></p>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Тип бизнеса <span class="text-rose-500">*</span>
                        </span>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="organization"
                                    {{ old('type', $business->type ?? 'organization') === 'organization' ? 'checked' : '' }}
                                    class="rounded-full border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-slate-700 dark:text-slate-300">Организация</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="master"
                                    {{ old('type', $business->type ?? 'organization') === 'master' ? 'checked' : '' }}
                                    class="rounded-full border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-slate-700 dark:text-slate-300">Мастер</span>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Описание
                        </label>
                        <textarea id="description" name="description" rows="3" maxlength="500"
                            class="w-full px-4 py-2.5 border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors"
                            placeholder="Краткое описание вашего бизнеса...">{{ old('description', $business->description) }}</textarea>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Максимум 500 символов</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Контактная информация</h2>
                @php
                    $phoneCountry = $business->primaryPhone?->country ?? $countries->first();
                    $phoneNational = '';
                    if ($business->primaryPhone && $phoneCountry) {
                        $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                        $phoneDig = preg_replace('/\D/', '', $business->primaryPhone->phone);
                        $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                    }
                @endphp
                <x-phone-input
                    :countries="$countries"
                    block-id="phoneCountryBlockEdit"
                    :old-phone="old('phone', $business->phone)"
                    :old-country-id="old('phone_country_id', $business->primaryPhone?->country_id)"
                    :old-national="old('phone_national', $phoneNational)"
                    :required="true"
                    helper-text="Код страны и номер без ведущих нулей"
                />
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('settings.index') }}"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fa-solid fa-check text-sm"></i>
                <span>Сохранить изменения</span>
            </button>
        </div>
    </form>

    @if($business && ($canDeleteBusiness ?? false))
    <div class="mt-10 pt-8 border-t border-slate-200 dark:border-slate-800" x-data>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-rose-200 dark:border-rose-800/50 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Удалить бизнес</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Все данные бизнеса (локации, услуги, мастера, клиенты, записи) будут скрыты. Восстановить бизнес будет нельзя.
            </p>
            <button type="button"
                    @click="$refs.confirmModal.showModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                <i class="fa-solid fa-trash-can text-sm"></i>
                <span>Удалить бизнес</span>
            </button>
        </div>

    <dialog x-ref="confirmModal"
            class="rounded-xl shadow-xl p-0 max-w-md w-full mx-auto border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"
            @click="if ($event.target === $el) $el.close()">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Удалить бизнес?</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                Вы уверены, что хотите удалить бизнес «{{ $business->name }}»? Это действие нельзя отменить.
            </p>
            <div class="flex flex-wrap gap-3 justify-end">
                <button type="button"
                        @click="$refs.confirmModal.close()"
                        class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Отмена
                </button>
                <form action="{{ route('settings.business.destroy') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
    </dialog>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function() {
    const currentBusinessId = {{ $business->id }};
    const currentBusinessSlug = '{{ addslashes($business->slug) }}';
    const SLUG_MIN = 3;
    const DEBOUNCE = 500;
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    const translitMap = { 'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'zh','з':'z','и':'i','й':'y','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f','х':'h','ц':'ts','ч':'ch','ш':'sh','щ':'sch','ъ':'','ы':'y','ь':'','э':'e','ю':'yu','я':'ya' };

    const slugEl = document.getElementById('slug');
    const slugPreviewValue = document.getElementById('slugPreviewValue');
    const slugChecking = document.getElementById('slugChecking');
    const slugAvailable = document.getElementById('slugAvailable');
    const slugUnavailable = document.getElementById('slugUnavailable');
    const slugError = document.getElementById('slugError');

    function transliterate(s) {
        if (!s) return '';
        let r = '';
        for (let i = 0; i < s.length; i++) r += translitMap[s[i]] !== undefined ? translitMap[s[i]] : s[i];
        return r;
    }
    function sanitize(s) {
        if (!s) return '';
        s = s.replace(/[\u2010-\u2014\u2212\u00AD]/g, '-');
        let r = s.replace(/\s+/g, '-'); r = transliterate(r); r = r.toLowerCase();
        r = r.replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-').replace(/^-/g, '');
        return r;
    }
    function slugForCheck(s) {
        return (s || '').replace(/-+$/g, '');
    }
    function updateSlugPreview() {
        if (slugPreviewValue && slugEl) slugPreviewValue.textContent = sanitize(slugEl.value) || 'ваш-адрес';
    }
    function slugReset() {
        [slugChecking, slugAvailable, slugUnavailable].forEach(el => { if (el) el.classList.add('hidden'); });
        if (slugError) { slugError.classList.add('hidden'); slugError.textContent = ''; }
    }
    function slugSet(state, msg) {
        slugReset();
        if (state === 'checking' && slugChecking) slugChecking.classList.remove('hidden');
        else if (state === 'available' && slugAvailable) slugAvailable.classList.remove('hidden');
        else if (state === 'unavailable' && slugUnavailable) { slugUnavailable.classList.remove('hidden'); if (slugError && msg) { slugError.textContent = msg; slugError.classList.remove('hidden'); } }
        else if (state === 'formatError' && slugError && msg) { slugError.textContent = msg; slugError.classList.remove('hidden'); }
    }
    let slugTimeout = null, slugAbort = null;
    async function checkSlug(v) {
        if (!v || v.length < SLUG_MIN) { slugReset(); return; }
        if (!slugRegex.test(v)) { slugSet('formatError', 'Только латиница, цифры и дефисы. Минимум 3 символа.'); return; }
        if (v === currentBusinessSlug) { slugSet('available'); return; }
        slugSet('checking');
        if (slugAbort) slugAbort.abort();
        slugAbort = new AbortController();
        try {
            const r = await fetch('{{ route("api.slug.check") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ slug: v, business_id: currentBusinessId }),
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
            const cp = this.selectionStart, s = sanitize(this.value);
            if (this.value !== s) { this.value = s; this.setSelectionRange(Math.min(cp, s.length), Math.min(cp, s.length)); }
            updateSlugPreview();
            if (slugTimeout) clearTimeout(slugTimeout);
            slugTimeout = setTimeout(() => checkSlug(slugForCheck(s)), DEBOUNCE);
        });
        slugEl.addEventListener('blur', function() {
            let s = sanitize(this.value);
            s = s.replace(/-+$/g, '');
            if (this.value !== s) this.value = s;
            updateSlugPreview();
            if (s && s.length >= SLUG_MIN) checkSlug(s);
            else slugReset();
        });
        updateSlugPreview();
        const initialSlug = slugForCheck(sanitize(slugEl.value));
        if (initialSlug.length >= SLUG_MIN) checkSlug(initialSlug);
    }

})();
</script>
@endpush
