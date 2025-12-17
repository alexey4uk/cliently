@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Добавление услуги</h1>
        </div>
        
        <!-- Индикатор прогресса -->
        <div class="w-full md:w-auto">
            <div class="flex items-center w-full md:w-auto md:gap-1.5">
                @for($i = 1; $i <= 4; $i++)
                    <div class="flex items-center {{ $i < 4 ? 'flex-1 md:flex-none' : 'flex-shrink-0' }}">
                        <div class="flex items-center justify-center w-6 md:w-7 h-6 md:h-7 rounded-full text-xs font-semibold transition-colors flex-shrink-0 {{ $i <= 3 ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $i }}
                        </div>
                        @if($i < 4)
                            <div class="flex-1 md:w-6 md:flex-none h-0.5 mx-1 md:mx-0 {{ $i <= 3 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Информационная карточка -->
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 dark:border-indigo-900 dark:bg-indigo-900/30 p-3 md:p-4 mb-6">
        <div class="flex items-start gap-2 md:gap-3">
            <div class="hidden md:flex flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center">
                    <i class="fa-solid fa-scissors text-white text-sm"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-xs md:text-sm font-semibold text-indigo-900 dark:text-indigo-100 mb-1 md:mb-2">
                    Что такое услуга?
                </h3>
                <p class="text-xs md:text-sm text-indigo-800 dark:text-indigo-300 mb-2 md:mb-3">
                    Услуга, которую вы оказываете клиентам
                </p>
                <div class="space-y-1.5 md:space-y-2">
                    <div class="flex items-start gap-1.5 md:gap-2">
                        <i class="fa-solid fa-check-circle text-indigo-600 dark:text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            Добавьте хотя бы одну услугу
                        </p>
                    </div>
                    <div class="flex items-start gap-1.5 md:gap-2">
                        <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            Остальные — позже
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.service.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="name" class="flex items-center gap-1.5 md:gap-2 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Название услуги*</span>
                </label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-xs md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Стрижка женская"
                       autofocus>
                @error('name')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="flex items-center gap-1.5 md:gap-2 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    <span>Описание (необязательно)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-xs md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Подробное описание услуги...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="duration" class="flex items-center gap-1.5 md:gap-2 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Длительность (минуты)*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="duration" name="duration" required min="15" step="15" value="{{ old('duration', 60) }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-xs md:text-sm rounded-md border {{ $errors->has('duration') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="60">
                        <span class="absolute right-2.5 md:right-3 top-1/2 transform -translate-y-1/2 text-slate-500 dark:text-slate-400 text-xs md:text-sm">мин</span>
                    </div>
                    @error('duration')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="flex items-center gap-1.5 md:gap-2 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fa-solid fa-dollar-sign text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        <span>Цена (BYN)*</span>
                    </label>
                    <input type="number" id="price" name="price" required min="0" step="50" value="{{ old('price') }}"
                           class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-xs md:text-sm rounded-md border {{ $errors->has('price') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="1000">
                    @error('price')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
            <button type="submit"
                        class="px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Сохранить и продолжить <i class="fa-solid fa-arrow-right ml-1.5 md:ml-2"></i>
                </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Автоматическое форматирование цены
        document.getElementById('price').addEventListener('blur', function(e) {
            let value = parseFloat(e.target.value);
            if (!isNaN(value)) {
                // Округляем до ближайших 50 рублей
                value = Math.round(value / 50) * 50;
                e.target.value = value;
            }
        });
    </script>
@endpush
