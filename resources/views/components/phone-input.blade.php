@props([
    'countries',
    'blockId' => 'phoneBlock',
    'oldPhone' => '',
    'oldCountryId' => '',
    'oldNational' => '',
    'required' => true,
    'placeholder' => '29 123 45 67',
    'helperText' => 'Код страны и номер без ведущих нулей',
])

@php
    $hasError = $errors->has('phone_country_id') || $errors->has('phone');
    $nationalId = $blockId . '_national';
@endphp

<div id="{{ $blockId }}"
    data-phone-block
    data-old-phone="{{ e($oldPhone) }}"
    data-old-country="{{ e($oldCountryId) }}"
    {{ $attributes->only('class')->merge(['class' => '']) }}
>
    <label for="{{ $nationalId }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
        Телефон @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    <div class="flex flex-row rounded-xl overflow-hidden {{ $hasError ? 'ring-2 ring-rose-500 ring-offset-0' : '' }} border border-slate-300 dark:border-slate-700 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 dark:focus-within:border-indigo-500 transition-all bg-white dark:bg-slate-900">
        <select name="phone_country_id" {{ $required ? 'required' : '' }}
            class="w-[5.5rem] min-w-[5.5rem] sm:min-w-[8.5rem] sm:max-w-[10.5rem] sm:w-auto pl-2 sm:pl-4 pr-8 sm:pr-9 py-2.5 bg-slate-50 dark:bg-slate-800/80 border-0 border-r border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm focus:ring-0 focus:outline-none cursor-pointer appearance-none bg-[length:1rem_1rem] bg-[right_0.5rem_center] sm:bg-[right_0.75rem_center] bg-no-repeat flex-shrink-0"
            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E');">
            @if(!$required)
                <option value="" {{ $oldCountryId === '' || $oldCountryId === null ? 'selected' : '' }}>—</option>
            @endif
            @foreach($countries as $c)
                <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ (string) $oldCountryId === (string) $c->id ? 'selected' : '' }}>
                    {{ $c->code_3 ?? $c->code }} {{ $c->calling_code }}
                </option>
            @endforeach
        </select>
        <div class="flex-1 min-w-0">
            <input type="tel" id="{{ $nationalId }}" name="phone_national" inputmode="numeric" maxlength="15" {{ $required ? 'required' : '' }}
                value="{{ e($oldNational) }}"
                class="w-full px-4 py-2.5 border-0 bg-transparent text-slate-900 dark:text-white text-sm placeholder:text-slate-400 focus:ring-0 focus:outline-none"
                placeholder="{{ $placeholder }}">
            <input type="hidden" name="phone" value="{{ e($oldPhone) }}">
        </div>
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

@push('scripts')
<script>
(function() {
    var block = document.getElementById('{{ $blockId }}');
    if (!block) return;
    var sel = block.querySelector('select[name="phone_country_id"]');
    var national = block.querySelector('input[name="phone_national"]');
    var hidden = block.querySelector('input[name="phone"]');

    function updatePhone() {
        var opt = sel ? sel.options[sel.selectedIndex] : null;
        var code = opt ? (opt.dataset.code || '').replace(/\D/g, '') : '';
        var digits = (national && national.value) ? national.value.replace(/\D/g, '') : '';
        var full = code && digits ? '+' + code + digits : '';
        if (hidden) hidden.value = full;
    }

    if (sel) sel.addEventListener('change', updatePhone);
    if (national) {
        national.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 15);
            updatePhone();
        });
    }

    var op = block.dataset.oldPhone || '';
    var oc = block.dataset.oldCountry || '';
    if (op && oc && sel && sel.value === oc) {
        var opt = sel.options[sel.selectedIndex];
        if (opt) {
            var codeDigits = (opt.dataset.code || '').replace(/\D/g, '');
            var phoneDigits = op.replace(/\D/g, '');
            if (phoneDigits.indexOf(codeDigits) === 0 && national) {
                national.value = phoneDigits.slice(codeDigits.length);
            }
        }
    }
    updatePhone();

    var form = block.closest('form');
    if (form) {
        form.addEventListener('submit', updatePhone);
    }
})();
</script>
@endpush
