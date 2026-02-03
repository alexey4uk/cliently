@props([
    'countries',
    'blockId' => 'phoneBlock',
    'oldPhone' => '',
    'oldCountryId' => '',
    'oldNational' => '',
    'required' => true,
    'placeholder' => '',
    'helperText' => 'Код страны и номер без ведущих нулей',
])
{{-- Виджет intl-tel-input: при загрузке показывается первая страна из списка или номер из oldPhone; при вводе/смене страны скрипт заполняет скрытые поля phone (E.164) и phone_country_id. --}}

@php
    $hasError = $errors->has('phone_country_id') || $errors->has('phone');
    $inputId = $blockId . '_tel';
    // Если в админке ни одна страна не помечена для телефона — показываем запасной список (РФ, РБ, Украина и др.)
    if ($countries->isEmpty()) {
        $countries = \App\Models\Country::where('is_active', true)
            ->whereIn('code', ['RU', 'BY', 'UA', 'KZ', 'PL', 'DE', 'US', 'LT', 'LV', 'EE'])
            ->orderBy('name')
            ->limit(30)
            ->get();
    }
    $onlyCountries = $countries->map(fn ($c) => strtolower($c->code))->values()->all();
    $countryMap = $countries->keyBy(fn ($c) => strtolower($c->code))->map(fn ($c) => $c->id)->all();
@endphp

{{-- JSON в одиночных кавычках, чтобы не ломать разбор в JS (без HTML-сущностей) --}}
<div id="{{ $blockId }}"
    data-phone-input
    data-initial-phone="{{ e($oldPhone) }}"
    data-country-map='@json($countryMap)'
    data-only-countries='@json($onlyCountries)'
    data-required="{{ $required ? '1' : '0' }}"
    {{ $attributes->only('class')->merge(['class' => '']) }}
>
    <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
        Телефон @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    <div class="phone-input-wrapper {{ $hasError ? 'ring-2 ring-rose-500 ring-offset-0' : '' }} rounded-xl border border-slate-300 dark:border-slate-700 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 dark:focus-within:border-indigo-500 transition-all bg-white dark:bg-slate-900">
        <input type="tel"
            id="{{ $inputId }}"
            {{ $required ? 'required' : '' }}
            class="w-full px-4 py-2.5 text-slate-900 dark:text-white text-sm placeholder:text-slate-400 focus:ring-0 focus:outline-none border-0 bg-transparent"
            placeholder="{{ $placeholder }}"
            autocomplete="tel">
        <input type="hidden" name="phone" value="{{ e($oldPhone) }}">
        <input type="hidden" name="phone_country_id" value="{{ e($oldCountryId) }}">
    </div>
    @error('phone_country_id')
        <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
    @error('phone')
        <p class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
    @if($helperText)
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ $helperText }}</p>
    @endif
</div>
