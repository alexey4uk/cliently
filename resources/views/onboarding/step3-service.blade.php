@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-baseline justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Добавление услуги</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">Шаг 3 из 4</p>
        </div>

        <!-- Индикатор прогресса -->
        <div class="flex items-center gap-2">
            <div class="flex items-center">
                @for($i = 1; $i <= 4; $i++)
                    <div class="w-2 h-2 rounded-full {{ $i <= 3 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }} {{ $i < 4 ? 'mr-1' : '' }}"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Информационная карточка -->
    <div class="rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-scissors text-slate-600 dark:text-slate-400 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    Добавьте хотя бы одну услугу, которую вы оказываете.
                    Вы можете добавить больше услуг позже в разделе "Услуги".
                </p>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.service.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Название услуги*</label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Стрижка женская"
                       autofocus>
                @error('name')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание (необязательно)</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Подробное описание услуги...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="duration" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Длительность*</label>
                    <select id="duration" name="duration" required
                            class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('duration') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        <option value="">Выберите длительность</option>
                        <option value="30" {{ old('duration') == '30' ? 'selected' : '' }}>30 минут</option>
                        <option value="45" {{ old('duration') == '45' ? 'selected' : '' }}>45 минут</option>
                        <option value="60" {{ old('duration') == '60' ? 'selected' : (old('duration') ? '' : 'selected') }}>1 час</option>
                        <option value="90" {{ old('duration') == '90' ? 'selected' : '' }}>1 час 30 минут</option>
                        <option value="120" {{ old('duration') == '120' ? 'selected' : '' }}>2 часа</option>
                        <option value="180" {{ old('duration') == '180' ? 'selected' : '' }}>3 часа</option>
                    </select>
                    @error('duration')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Цена*</label>
                    <div class="relative">
                        <input type="number" id="price" name="price" required min="0" step="50" value="{{ old('price') }}"
                               class="w-full pl-8 pr-3 py-2 text-sm rounded-md border {{ $errors->has('price') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="1000">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 dark:text-slate-400">₽</span>
                    </div>
                    @error('price')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('onboarding.location') }}"
               class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Назад
            </a>

            <div class="flex items-center gap-3">
                <button type="submit" name="action" value="skip"
                        class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                    Пропустить
                </button>

                <button type="submit" name="action" value="save"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Сохранить и продолжить <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </div>
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
