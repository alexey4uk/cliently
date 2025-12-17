@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-baseline justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Добавление локации</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">Шаг 2 из 4</p>
        </div>

        <!-- Индикатор прогресса -->
        <div class="flex items-center gap-2">
            <div class="flex items-center">
                @for($i = 1; $i <= 4; $i++)
                    <div class="w-2 h-2 rounded-full {{ $i <= 2 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }} {{ $i < 4 ? 'mr-1' : '' }}"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Информационная карточка -->
    <div class="rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-store text-slate-600 dark:text-slate-400 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    Добавьте хотя бы одну локацию (салон или студию), где будут оказываться услуги.
                    Вы можете добавить больше локаций позже в настройках бизнеса.
                </p>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.location.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Название локации*</label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="Например: Основной салон"
                       autofocus>
                @error('name')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Адрес*</label>
                <input type="text" id="address" name="address" required value="{{ old('address') }}"
                       class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('address') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                       placeholder="ул. Пушкинская, д. 10">
                @error('address')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание (необязательно)</label>
                <textarea id="description" name="description" rows="2"
                          class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Описание локации...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Телефон*</label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-3 py-2 text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="+7 (999) 123-45-67">
                    @error('phone')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Почта (необязательно)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                           placeholder="salon@example.com">
                </div>
            </div>

            <!-- Время работы -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Время работы*</label>
                <div class="space-y-3">
                    @php
                        $days = [
                            'monday' => 'Понедельник',
                            'tuesday' => 'Вторник',
                            'wednesday' => 'Среда',
                            'thursday' => 'Четверг',
                            'friday' => 'Пятница',
                            'saturday' => 'Суббота',
                            'sunday' => 'Воскресенье'
                        ];
                    @endphp

                    @foreach($days as $key => $day)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                            <div class="w-32">
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $day }}</span>
                            </div>

                            <div class="flex-1 flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">С</label>
                                    <select name="working_hours[{{ $key }}][from]"
                                            class="w-full px-2 py-1.5 text-sm rounded border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-transparent">
                                        <option value="">--:--</option>
                                        @for($i = 8; $i <= 22; $i++)
                                            <option value="{{ sprintf('%02d:00', $i) }}" {{ old("working_hours.{$key}.from") == sprintf('%02d:00', $i) ? 'selected' : ($i == 9 ? 'selected' : '') }}>
                                                {{ sprintf('%02d:00', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="flex-1">
                                    <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">До</label>
                                    <select name="working_hours[{{ $key }}][to]"
                                            class="w-full px-2 py-1.5 text-sm rounded border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-transparent">
                                        <option value="">--:--</option>
                                        @for($i = 8; $i <= 22; $i++)
                                            <option value="{{ sprintf('%02d:00', $i) }}" {{ old("working_hours.{$key}.to") == sprintf('%02d:00', $i) ? 'selected' : ($i == 18 ? 'selected' : '') }}>
                                                {{ sprintf('%02d:00', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="working_hours[{{ $key }}][day_off]" value="1"
                                           class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-1 focus:ring-indigo-500 focus:ring-offset-0"
                                        {{ old("working_hours.{$key}.day_off") ? 'checked' : '' }}>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Выходной</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('working_hours')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('onboarding.business') }}"
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
        // Скрипт для синхронизации времени работы
        document.addEventListener('DOMContentLoaded', function() {
            // При изменении чекбокса "Выходной" блокируем выбор времени
            document.querySelectorAll('input[name^="working_hours"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const dayContainer = this.closest('.flex.items-center.gap-3');
                    const fromSelect = dayContainer.querySelector('select[name$="[from]"]');
                    const toSelect = dayContainer.querySelector('select[name$="[to]"]');

                    if (this.checked) {
                        fromSelect.disabled = true;
                        toSelect.disabled = true;
                        fromSelect.value = '';
                        toSelect.value = '';
                    } else {
                        fromSelect.disabled = false;
                        toSelect.disabled = false;
                    }
                });

                // Инициализируем состояние при загрузке
                if (checkbox.checked) {
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
@endpush
