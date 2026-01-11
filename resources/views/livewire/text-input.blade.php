<div>
    @if ($label)
        <label for="{{ $id }}"
            class="flex items-center gap-1.5 md:gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            <span>{{ $label }}@if($required)*@endif</span>
        </label>
    @endif

    <input
    type="{{ $type }}" 
    name="{{ $name }}" 
    id="{{ $id }}"
    wire:model.live.debounce.1000ms="{{ $wireModel }}"
        @if ($required) required @endif placeholder="{{ $placeholder }}"
        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">


    @error($name)
        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
