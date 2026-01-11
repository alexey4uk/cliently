<div>
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        wire:model.live.debounce.1000ms="{{ $wireModel }}"
        @if($required) required @endif
        placeholder="{{ $placeholder }}"
        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has($name) ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
    
    <!-- Отображение ошибок -->
    @error($name)
        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
    @enderror
</div>