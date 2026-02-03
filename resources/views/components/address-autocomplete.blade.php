{{-- Автопоиск адреса (Photon API, РФ и РБ). При выборе подсказки заполняются поля: city, street, house. --}}
<div data-address-autocomplete {{ $attributes->merge(['class' => 'relative']) }}>
    <label for="{{ $inputId ?? 'address-search' }}" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
        Поиск адреса
    </label>
    <input type="text"
           id="{{ $inputId ?? 'address-search' }}"
           autocomplete="off"
           placeholder="Начните вводить город, улицу, дом…"
           class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
    <div data-address-suggestions
         class="hidden absolute left-0 right-0 top-full z-20 mt-1 max-h-60 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg"
         role="listbox"></div>
</div>
