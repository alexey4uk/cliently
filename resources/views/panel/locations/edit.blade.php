@extends('layouts.panel')

@section('title', 'Редактирование локации')

@section('content')
    <div class="space-y-6">
        <!-- Flash сообщения -->
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-3 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm sm:text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
                </div>
                <button @click="show = false"
                    class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                    <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
                </button>
            </div>
        @endif

        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-location-dot text-white text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Редактирование локации</h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Измените информацию о локации</p>
                    </div>
                </div>
                <a href="{{ route('panel.locations.show', $location) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs sm:text-sm"></i>
                    <span>Назад</span>
                </a>
            </div>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <form method="POST" action="{{ route('panel.locations.update', $location) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Бизнес -->
                    <div>
                        <label for="business_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Бизнес <span class="text-rose-500">*</span>
                        </label>
                        <select id="business_id"
                                name="business_id"
                                class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('business_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                required>
                            <option value="">Выберите бизнес</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" {{ old('business_id', $location->business_id) == $business->id ? 'selected' : '' }}>
                                    {{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Название -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Название локации <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $location->name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="Введите название локации"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Адрес -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Адрес <span class="text-rose-500">*</span>
                        </label>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="city" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Город*</label>
                                    <input type="text"
                                           id="city"
                                           name="city"
                                           value="{{ old('city', $location->city) }}"
                                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('city') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                           required>
                                    @error('city')
                                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="street" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Улица*</label>
                                    <input type="text"
                                           id="street"
                                           name="street"
                                           value="{{ old('street', $location->street) }}"
                                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('street') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                           required>
                                    @error('street')
                                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="house" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Дом*</label>
                                    <input type="text"
                                           id="house"
                                           name="house"
                                           value="{{ old('house', $location->house) }}"
                                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('house') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                           required>
                                    @error('house')
                                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="building" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Корпус</label>
                                    <input type="text"
                                           id="building"
                                           name="building"
                                           value="{{ old('building', $location->building) }}"
                                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                </div>
                                <div>
                                    <label for="apartment" class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Квартира/Офис</label>
                                    <input type="text"
                                           id="apartment"
                                           name="apartment"
                                           value="{{ old('apartment', $location->apartment) }}"
                                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $locPhoneCountry = $location->primaryPhone?->country ?? $countries->first();
                        $locPhoneNational = '';
                        if ($location->primaryPhone && $locPhoneCountry) {
                            $codeDig = preg_replace('/\D/', '', $locPhoneCountry->calling_code);
                            $phoneDig = preg_replace('/\D/', '', $location->primaryPhone->phone);
                            $locPhoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                        }
                    @endphp
                    <div id="panelLocationEditPhoneBlock"
                        data-old-country="{{ old('phone_country_id', $location->primaryPhone?->country_id) }}"
                        data-old-phone="{{ old('phone', $location->phone) }}">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Телефон <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="sm:w-48">
                                <select id="phone_country_id" name="phone_country_id"
                                    class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('phone_country_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-colors">
                                    <option value="">—</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ old('phone_country_id', $location->primaryPhone?->country_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->calling_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 relative">
                                <span id="panelLocationEditPhonePrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm pointer-events-none"></span>
                                <input type="tel" id="panelLocationEditPhoneNational" inputmode="numeric" maxlength="15"
                                    value="{{ old('phone_national', $locPhoneNational) }}"
                                    class="w-full pl-14 pr-4 py-2.5 rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-colors"
                                    placeholder="291234567">
                                <input type="hidden" name="phone" id="panelLocationEditPhone" value="{{ old('phone', $location->phone) }}">
                            </div>
                        </div>
                        @error('phone_country_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        @error('phone')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Описание -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Описание
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1">(необязательно)</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  maxlength="500"
                                  class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors resize-none"
                                  placeholder="Краткое описание локации...">{{ old('description', $location->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                            Максимум 500 символов
                        </p>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.locations.show', $location) }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Сохранить изменения</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const block = document.getElementById('panelLocationEditPhoneBlock');
            if (!block) return;
            const sel = document.getElementById('phone_country_id');
            const prefixSpan = document.getElementById('panelLocationEditPhonePrefix');
            const nationalInput = document.getElementById('panelLocationEditPhoneNational');
            const hidden = document.getElementById('panelLocationEditPhone');
            const oldCountry = block.dataset.oldCountry || '';
            const oldPhone = block.dataset.oldPhone || '';

            function updatePrefix() {
                const opt = sel?.selectedOptions?.[0];
                const code = opt?.dataset?.code || '';
                if (prefixSpan) prefixSpan.textContent = code || '—';
            }

            function buildE164() {
                const opt = sel?.selectedOptions?.[0];
                if (!opt?.value) { if (hidden) hidden.value = ''; return; }
                const code = opt?.dataset?.code?.replace(/\D/g, '') || '';
                const national = nationalInput?.value?.replace(/\D/g, '') || '';
                const digits = code + national;
                if (digits.length >= 10 && hidden) hidden.value = '+' + digits; else if (hidden) hidden.value = '';
            }

            if (sel) { sel.addEventListener('change', updatePrefix); sel.addEventListener('change', buildE164); }
            if (nationalInput) {
                nationalInput.addEventListener('input', function() { this.value = this.value.replace(/\D/g, ''); buildE164(); });
                nationalInput.addEventListener('blur', buildE164);
            }
            updatePrefix();
            if (oldCountry && sel) { const o = Array.from(sel.options).find(op => op.value === oldCountry); if (o) { sel.value = oldCountry; updatePrefix(); } }
            if (oldPhone && oldPhone.startsWith('+') && nationalInput && hidden) {
                const code = sel?.selectedOptions?.[0]?.dataset?.code?.replace(/\D/g, '') || '375';
                const rest = oldPhone.replace(/\D/g, '').replace(new RegExp('^' + code), '') || '';
                nationalInput.value = rest;
                hidden.value = oldPhone;
            }
        });
    </script>
    @endpush
@endsection
