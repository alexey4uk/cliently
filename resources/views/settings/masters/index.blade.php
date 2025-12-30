@extends('layouts.user')

@section('title', 'Мастера - Cliently')
@section('page-title', 'Мастера')
@section('page-description', 'Управление мастерами вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => null]
    ]" />
@endpush

@section('content')

<div x-data="{ 
    showDeleteModal: false,
    masterToDelete: null,
    masterName: '',
    openDeleteModal(masterId, masterName) {
        this.masterToDelete = masterId;
        this.masterName = masterName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.masterToDelete = null;
        this.masterName = '';
    },
    confirmDelete() {
        if (this.masterToDelete) {
            const form = document.getElementById('delete-form-' + this.masterToDelete);
            if (form) {
                form.submit();
            }
        }
    }
}" class="space-y-4 md:space-y-6">
    
    <!-- Заголовок с кнопкой -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white mb-1">
                Мастера
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Управление мастерами и их рабочим расписанием
            </p>
        </div>
        <a href="{{ route('settings.masters.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить мастера</span>
        </a>
    </div>

    <!-- Список мастеров -->
    @if($masters->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach($masters as $master)
                @php
                    $workingHours = json_decode($master->working_hours, true);
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow h-full flex flex-col">
                    <!-- Заголовок карточки -->
                    <div class="px-4 md:px-5 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex-shrink-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate mb-0.5">
                                        {{ $master->first_name }} {{ $master->last_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $master->specialization }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Содержимое карточки -->
                    <div class="p-4 md:p-5 space-y-3 flex-1">
                        <!-- Контакты -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-phone text-slate-400 text-xs flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Телефон</p>
                                    <p class="text-sm text-slate-700 dark:text-slate-300 break-all">
                                        {{ $master->phone }}
                                    </p>
                                </div>
                            </div>
                            @if($master->email)
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-envelope text-slate-400 text-xs flex-shrink-0"></i>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Email</p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300 break-all truncate">
                                            {{ $master->email }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Время работы -->
                        @if($workingHours)
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-clock text-slate-400 text-xs mt-0.5 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Время работы</p>
                                    @if($workingHours['24_hours'] ?? false)
                                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                            Круглосуточно
                                        </p>
                                    @else
                                        <p class="text-sm text-slate-700 dark:text-slate-300">
                                            {{ $workingHours['from'] ?? '—' }} - {{ $workingHours['to'] ?? '—' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Описание -->
                        @if($master->description)
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-info-circle text-slate-400 text-xs mt-0.5 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Описание</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 break-words">
                                        {{ $master->description }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Локации и услуги -->
                        <div class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-200 dark:border-slate-800">
                            @if($master->locations->count() > 0)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-medium">
                                    <i class="fa-solid fa-location-dot text-xs"></i>
                                    <span>{{ $master->locations->count() }} {{ $master->locations->count() === 1 ? 'локация' : 'локаций' }}</span>
                                </div>
                            @endif
                            @if($master->services->count() > 0)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 text-xs font-medium">
                                    <i class="fa-solid fa-scissors text-xs"></i>
                                    <span>{{ $master->services->count() }} {{ $master->services->count() === 1 ? 'услуга' : 'услуг' }}</span>
                                </div>
                            @endif
                            @if($master->locations->count() === 0 && $master->services->count() === 0)
                                <span class="text-xs text-slate-400 dark:text-slate-500 italic">Не назначены</span>
                            @endif
                        </div>
                    </div>

                    <!-- Действия -->
                    <div class="px-4 md:px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex-shrink-0">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('settings.masters.edit', $master) }}"
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <i class="fa-solid fa-pencil text-xs"></i>
                                <span>Редактировать</span>
                            </a>
                            <form method="POST" action="{{ route('settings.masters.destroy', $master) }}" 
                                  id="delete-form-{{ $master->id }}"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button"
                                    @click="openDeleteModal({{ $master->id }}, '{{ addslashes($master->first_name . ' ' . $master->last_name) }}')"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 bg-white dark:bg-slate-800 border border-rose-300 dark:border-rose-700/50 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                <i class="fa-solid fa-trash text-xs"></i>
                                <span>Удалить</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Пустое состояние -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-8 md:p-12 text-center">
            <div class="max-w-sm mx-auto">
                <div class="h-16 w-16 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Мастера не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                    Начните работу с системой, добавив первого мастера с контактами и рабочим расписанием
                </p>
                <a href="{{ route('settings.masters.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить мастера</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" 
         @click.away="closeDeleteModal()"
         @keydown.escape.window="closeDeleteModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display: none;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                <button @click="closeDeleteModal()" 
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 mb-6">
                    Вы уверены, что хотите удалить мастера <span class="font-semibold" x-text="masterName"></span>? Это действие нельзя отменить.
                </p>
                <div class="flex gap-3">
                    <button @click="closeDeleteModal()" 
                            class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </button>
                    <button @click="confirmDelete()" 
                            class="flex-1 px-4 py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium transition-colors">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
