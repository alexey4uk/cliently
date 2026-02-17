@extends('layouts.panel')

@section('title', 'Редактирование записи')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Редактирование записи</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Измените статус и примечания к записи</p>
            </div>
            <a href="{{ route('panel.appointments') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Информация о записи -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Информация о записи</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Клиент -->
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-sm">{{ substr($appointment->client?->first_name ?? 'Н', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->client?->full_name ?? 'Клиент удалён' }}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">{{ $appointment->client?->phone ?? '—' }}</p>
                    </div>
                </div>

                <!-- Услуга -->
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-scissors text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->service?->name ?? 'Услуга удалена' }}</p>
                        @if($appointment->duration)
                            <p class="text-xs text-slate-600 dark:text-slate-400">{{ $appointment->duration }} мин</p>
                        @endif
                    </div>
                </div>

                <!-- Мастер -->
                @if($appointment->master)
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user-tie text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->master?->name ?? 'Не назначен' }}</p>
                        </div>
                    </div>
                @endif

                <!-- Дата и время -->
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointment->date->format('d.m.Y') }}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">{{ $appointment->time }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Форма редактирования -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('panel.appointments.update', $appointment) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Статус -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Статус записи</label>
                        <select id="status"
                                name="status"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="pending" {{ $appointment->status === 'pending' ? 'selected' : '' }}>Ожидает подтверждения</option>
                            <option value="confirmed" {{ $appointment->status === 'confirmed' ? 'selected' : '' }}>Подтверждена</option>
                            <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>Завершена</option>
                            <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Отменена</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Измените статус записи для отслеживания её состояния
                        </p>
                    </div>

                    <!-- Примечания -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Примечания</label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="4"
                                  class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                  placeholder="Добавьте примечания к записи...">{{ old('notes', $appointment->notes) }}</textarea>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Дополнительная информация о записи (необязательно)
                        </p>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.appointments') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-sm hover:shadow-md">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection