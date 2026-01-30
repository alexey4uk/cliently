<div>
    @if($label)
        <label for="{{ $name }}" class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            <span>{{ $label }}</span>
            @if($required)
                <span class="text-xs text-slate-400 dark:text-slate-500">(необязательно)</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            wire:model.live="value"
            rows="{{ $rows }}"
            maxlength="{{ $maxlength }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has($name) ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} {{ $isLimitExceeded ? 'border-rose-500' : '' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors @if($resize === 'vertical') resize-y @elseif($resize === 'horizontal') resize-x @elseif($resize === 'both') resize @else resize-none @endif"
        ></textarea>
        
        @if($showCounter)
            <div class="absolute bottom-2 right-2 flex items-center gap-1 bg-white dark:bg-slate-900 px-1 rounded">
                <span class="text-xs {{ $counterClasses }}" id="{{ $name }}Count">
                    {{ $characterCount }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500">/</span>
                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $maxlength }}</span>
            </div>
        @endif
    </div>
    
    @if($isLimitExceeded)
        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
            Превышен лимит символов ({{ $characterCount }} из {{ $maxlength }})
        </p>
    @endif
    
    @error($name)
        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>