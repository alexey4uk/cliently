@extends('layouts.panel')

@section('title', 'Создание клиента')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Создание клиента</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Добавьте нового клиента в систему</p>
            </div>
            <a href="{{ route('panel.clients') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('panel.clients.store') }}">
                @csrf

                <div class="space-y-6">
                    <!-- Бизнес -->
                    <div>
                        <label for="business_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Бизнес</label>
                        <select id="business_id"
                                name="business_id"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                required>
                            <option value="">Выберите бизнес</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" {{ old('business_id') == $business->id ? 'selected' : '' }}>
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Имя -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Имя</label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Фамилия -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Фамилия</label>
                        <input type="text"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('last_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="panelClientCreatePhoneBlock"
                        data-countries="{{ json_encode($countries->map(fn ($c) => ['id' => $c->id, 'code' => $c->calling_code, 'name' => $c->name])->values()) }}"
                        data-old-phone="{{ old('phone') }}"
                        data-old-country="{{ old('phone_country_id') }}">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Телефон</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="sm:w-48">
                                <select id="phone_country_id" name="phone_country_id" required
                                    class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('phone_country_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ old('phone_country_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->calling_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 relative">
                                <span id="panelClientCreatePhonePrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm pointer-events-none"></span>
                                <input type="tel" id="phone_national" inputmode="numeric" maxlength="15" required
                                    value="{{ old('phone_national') }}"
                                    class="w-full pl-14 pr-4 py-2.5 rounded-lg border {{ $errors->has('phone') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                    placeholder="291234567">
                                <input type="hidden" name="phone" id="panelClientCreatePhone" value="{{ old('phone') }}">
                            </div>
                        </div>
                        @error('phone_country_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        @error('phone')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('email')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.clients') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-sm hover:shadow-md">
                        Создать клиента
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    const block = document.getElementById('panelClientCreatePhoneBlock');
    const sel = document.getElementById('phone_country_id');
    const national = document.getElementById('phone_national');
    const hidden = document.getElementById('panelClientCreatePhone');
    const prefix = document.getElementById('panelClientCreatePhonePrefix');
    function updatePhone() {
        const opt = sel && sel.options[sel.selectedIndex];
        const code = opt ? (opt.dataset.code || '').replace(/\D/g, '') : '';
        const digits = national && national.value ? national.value.replace(/\D/g, '') : '';
        const full = code && digits ? '+' + code + digits : '';
        if (hidden) hidden.value = full;
        if (prefix) prefix.textContent = opt ? opt.dataset.code || '' : '';
    }
    if (sel) sel.addEventListener('change', function() { updatePhone(); if (national) national.placeholder = (this.options[this.selectedIndex].dataset.code === '+375') ? '291234567' : '9123456789'; });
    if (national) national.addEventListener('input', function() { this.value = this.value.replace(/\D/g, '').slice(0, 15); updatePhone(); });
    if (sel && sel.options.length) {
        const opt = sel.options[sel.selectedIndex];
        if (prefix) prefix.textContent = opt ? opt.dataset.code || '' : '';
        if (national) national.placeholder = (opt && opt.dataset.code === '+375') ? '291234567' : '9123456789';
        const op = block && block.dataset.oldPhone ? block.dataset.oldPhone : '', oc = block && block.dataset.oldCountry ? String(block.dataset.oldCountry) : '';
        if (op && oc && sel.value === oc && opt) { const codeDigits = (opt.dataset.code || '').replace(/\D/g, ''), phoneDigits = op.replace(/\D/g, ''); if (phoneDigits.startsWith(codeDigits)) national.value = phoneDigits.slice(codeDigits.length); }
        updatePhone();
    }
    const form = block && block.closest('form');
    if (form) form.addEventListener('submit', updatePhone);
})();
</script>
@endpush
@endsection