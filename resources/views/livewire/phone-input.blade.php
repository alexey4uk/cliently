<div x-data>
    @if ($label)
        <label for="{{ $name }}"
            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            <span>{{ $label }}@if($required)*@endif</span>
        </label>
    @endif
    <div class="relative">
        <input 
            type="tel" 
            name="display_{{ $name }}" 
            id="{{ $name }}"
            wire:model.live="phone" 
            x-mask="+375 (99) 999-99-99" {{-- Маска на стороне клиента --}}
            placeholder="{{ $placeholder }}"
            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"   
        />
        
        {{-- Галочка появляется только если введено ровно 12 цифр (375 + 9 цифр номера) --}}
        @if (Str::length($this->cleanPhone) === 12)
            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        @endif
        
        {{-- Скрытое поле для отправки чистых цифр через обычный POST --}}
        <input type="hidden" name="{{ $name }}" value="{{ $this->cleanPhone }}">

        @error($name)
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>
</div>
