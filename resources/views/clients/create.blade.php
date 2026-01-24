@extends('layouts.user')

@section('title', 'Добавление клиента - Cliently')
@section('page-title', 'Новый клиент')
@section('page-description', 'Добавление нового клиента в базу')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => 'Добавление клиента', 'url' => null],
    ]" />
@endpush

@section('content')

    <div class="max-w-2xl mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">
                Новый клиент
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Заполните информацию о новом клиенте
            </p>
        </div>

        <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-6">
            <!-- Карточка основной информации -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Основная информация</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Имя -->
                        <div class="space-y-2">
                            <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Имя <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="first_name" name="first_name" required
                                    value="{{ old('first_name') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('first_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                                    placeholder="Введите имя клиента" autofocus>
                                @if ($errors->has('first_name'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Фамилия -->
                        <div class="space-y-2">
                            <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Фамилия
                            </label>
                            <div class="relative">
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('last_name') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                                    placeholder="Введите фамилию">
                                @if ($errors->has('last_name'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Карточка контактной информации -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Контактная информация</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2" id="clientPhoneBlock"
                            data-countries="{{ json_encode($countries->map(fn ($c) => ['id' => $c->id, 'code' => $c->calling_code, 'name' => $c->name])->values()) }}"
                            data-old-phone="{{ old('phone') }}"
                            data-old-country="{{ old('phone_country_id') }}">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Телефон <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="sm:w-48">
                                    <select id="phone_country_id" name="phone_country_id" required
                                        class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('phone_country_id') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                        @foreach($countries as $c)
                                            <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ old('phone_country_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->calling_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1 relative">
                                    <span id="clientPhonePrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm pointer-events-none"></span>
                                    <input type="tel" id="phone_national" inputmode="numeric" maxlength="15" required
                                        value="{{ old('phone_national') }}"
                                        class="w-full pl-14 pr-4 py-2.5 text-sm rounded-lg {{ $errors->has('phone') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                        placeholder="291234567">
                                    <input type="hidden" name="phone" id="client_phone" value="{{ old('phone') }}">
                                </div>
                            </div>
                            @error('phone_country_id')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-500 dark:text-slate-400">Формат: код страны + номер</p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Email
                            </label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg {{ $errors->has('email') ? 'border border-rose-500' : 'border border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                                    placeholder="client@example.com">
                                @if ($errors->has('email'))
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('clients.index') }}"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left text-sm"></i>
                Отмена
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                Создать клиента
            </button>
        </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        (function() {
            const block = document.getElementById('clientPhoneBlock');
            const sel = document.getElementById('phone_country_id');
            const national = document.getElementById('phone_national');
            const hidden = document.getElementById('client_phone');
            const prefix = document.getElementById('clientPhonePrefix');

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
        })();
    </script>
@endpush
