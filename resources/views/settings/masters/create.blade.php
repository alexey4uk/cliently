@extends('layouts.user')

@section('title', 'Добавление мастера - Cliently')
@section('page-title', 'Добавление мастера')
@section('page-description', 'Добавьте нового мастера для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => route('settings.masters')],
        ['title' => 'Добавление', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Добавить мастера</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Заполните информацию о новом мастере</p>
    </div>

    <form method="POST" action="{{ route('settings.masters.store') }}">
        @csrf

        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Основная информация</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Имя <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               required
                               value="{{ old('first_name') }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                               autofocus>
                        @error('first_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Фамилия
                        </label>
                        <input type="text"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               class="w-full px-4 py-2.5 border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                        @error('last_name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="specialization" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Специализация <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="specialization"
                           name="specialization"
                           required
                           value="{{ old('specialization') }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           placeholder="Парикмахер, Массажист">
                    @error('specialization')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Описание
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white resize-none transition-colors">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Контактная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Контактная информация</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2" id="masterCreatePhoneBlock"
                    data-countries="{{ json_encode($countries->map(fn ($c) => ['id' => $c->id, 'code' => $c->calling_code, 'name' => $c->name])->values()) }}"
                    data-old-phone="{{ old('phone') }}"
                    data-old-country="{{ old('phone_country_id') }}">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Телефон <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="sm:w-48">
                            <select id="phone_country_id" name="phone_country_id" required
                                class="w-full px-4 py-2.5 border {{ $errors->has('phone_country_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ old('phone_country_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->calling_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 relative">
                            <span id="masterCreatePhonePrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm pointer-events-none"></span>
                            <input type="tel" id="phone_national" inputmode="numeric" maxlength="15" required
                                value="{{ old('phone_national') }}"
                                class="w-full pl-14 pr-4 py-2.5 border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
                            <input type="hidden" name="phone" id="masterCreatePhone" value="{{ old('phone') }}">
                        </div>
                    </div>
                    @error('phone_country_id')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    @error('phone')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Формат: код страны + номер</p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-2.5 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors"
                           placeholder="example@mail.com">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Расписание -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Расписание мастера</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Расписание можно настроить после создания мастера</p>
                </div>
            </div>
        </div>

        <!-- Локации и услуги -->
        @if($locations->count() > 0 || $services->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Связи</h2>

            <div class="space-y-6">
                @if($locations->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                        Локации
                    </label>
                    <div class="space-y-3">
                        @foreach($locations as $location)
                            <label class="flex items-center p-3 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="location_ids[]"
                                       value="{{ $location->id }}"
                                       {{ in_array($location->id, old('location_ids', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                                <span class="ml-3 text-sm font-medium text-slate-900 dark:text-white">{{ $location->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($services->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                        Услуги
                    </label>
                    <div class="space-y-3">
                        @foreach($services as $service)
                            <label class="flex items-center p-3 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="service_ids[]"
                                       value="{{ $service->id }}"
                                       {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                                <span class="ml-3 text-sm font-medium text-slate-900 dark:text-white">{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('settings.masters') }}"
               class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                Сохранить мастера
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const block = document.getElementById('masterCreatePhoneBlock');
    const sel = document.getElementById('phone_country_id');
    const national = document.getElementById('phone_national');
    const hidden = document.getElementById('masterCreatePhone');
    const prefix = document.getElementById('masterCreatePhonePrefix');
    function updatePhone() {
        const opt = sel && sel.options[sel.selectedIndex];
        const code = opt ? (opt.dataset.code || '').replace(/\D/g, '') : '';
        const digits = national && national.value ? national.value.replace(/\D/g, '') : '';
        const full = code && digits ? '+' + code + digits : '';
        if (hidden) hidden.value = full;
        if (prefix) prefix.textContent = opt ? opt.dataset.code || '' : '';
    }
    //if (sel) sel.addEventListener('change', function() { updatePhone(); if (national) national.placeholder = (this.options[this.selectedIndex].dataset.code === '+375') ? '291234567' : '9123456789'; });
    if (national) national.addEventListener('input', function() { this.value = this.value.replace(/\D/g, '').slice(0, 15); updatePhone(); });
    if (sel && sel.options.length) {
        const opt = sel.options[sel.selectedIndex];
        if (prefix) prefix.textContent = opt ? opt.dataset.code || '' : '';
        // if (national) national.placeholder = (opt && opt.dataset.code === '+375') ? '291234567' : '9123456789';
        const op = block && block.dataset.oldPhone ? block.dataset.oldPhone : '', oc = block && block.dataset.oldCountry ? String(block.dataset.oldCountry) : '';
        if (op && oc && sel.value === oc && opt) { const codeDigits = (opt.dataset.code || '').replace(/\D/g, ''), phoneDigits = op.replace(/\D/g, ''); if (phoneDigits.startsWith(codeDigits)) national.value = phoneDigits.slice(codeDigits.length); }
        updatePhone();
    }
    const form = block && block.closest('form');
    if (form) form.addEventListener('submit', updatePhone);
})();
</script>
@endpush
