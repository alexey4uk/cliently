<div>
    <div class="relative">
        <!-- <input type="hidden" name="phone" value="{{ $cleanPhone }}" /> -->
        <input
            type="tel"
            name="phone"
            wire:model.live="phone"
            wire:focus="onFocus"
            wire:blur="onBlur"
            placeholder="+375 (29) 123-45-67"
            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-sm rounded-md border {{ $errors->has('phone') || $error ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
            maxlength="20"
        />
        
        <!-- Зеленая галочка при валидном полном номере -->
        @if($phone && !$error && strlen($cleanPhone) === 12)
            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif
    </div>
    
    {{-- <!-- Сообщение об ошибке -->
    @if($error)
        <div class="mt-1 text-sm text-rose-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </div>
    @endif
    
    <!-- Отображение отформатированного номера -->
    @if($formattedPhone && !$error && strlen($cleanPhone) === 12)
        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            <div class="font-semibold text-green-600 dark:text-green-500">{{ $formattedPhone }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                Для сохранения: <span class="font-mono">{{ $cleanPhone }}</span>
            </div>
        </div>
    @endif --}}
</div>