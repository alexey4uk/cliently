@extends('layouts.user')

@section('title', 'Услуги - Cliently')
@section('page-title', 'Услуги')
@section('page-description', 'Управление услугами вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Услуги', 'url' => null]
    ]" />
@endpush

@section('content')

<div x-data="{ 
    showDeleteModal: false,
    serviceToDelete: null,
    serviceName: '',
    openDeleteModal(serviceId, serviceName) {
        this.serviceToDelete = serviceId;
        this.serviceName = serviceName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.serviceToDelete = null;
        this.serviceName = '';
    },
    confirmDelete() {
        if (this.serviceToDelete) {
            const form = document.getElementById('delete-form-' + this.serviceToDelete);
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
                Услуги
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Управление услугами и прайс-листом вашего бизнеса
            </p>
        </div>
        <a href="{{ route('services.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить услугу</span>
        </a>
    </div>

    <!-- Список услуг -->
    @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach($services as $service)
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow h-full flex flex-col">
                    <!-- Заголовок карточки -->
                    <div class="px-4 md:px-5 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex-shrink-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate mb-0.5">
                                        {{ $service->name }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format($service->price, 0, ',', ' ') }} Br
                                        </span>
                                        <span>•</span>
                                        <div class="flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-xs"></i>
                                            <span>{{ $service->duration }} мин</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($service->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 flex-shrink-0">
                                    Активна
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex-shrink-0">
                                    Неактивна
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Содержимое карточки -->
                    <div class="p-4 md:p-5 flex-1">
                        @if($service->description)
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-info-circle text-slate-400 text-xs mt-0.5 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Описание</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-3 break-words">
                                        {{ $service->description }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Действия -->
                    <div class="px-4 md:px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex-shrink-0">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('services.edit', $service) }}"
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <i class="fa-solid fa-pencil text-xs"></i>
                                <span>Редактировать</span>
                            </a>
                            <form method="POST" action="{{ route('services.destroy', $service) }}" 
                                  id="delete-form-{{ $service->id }}"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button"
                                    @click="openDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
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
                    <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Услуги не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                    Начните работу с системой, добавив первую услугу в ваш прайс-лист
                </p>
                <a href="{{ route('services.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Добавить услугу</span>
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
                    Вы уверены, что хотите удалить услугу <span class="font-semibold" x-text="serviceName"></span>? Это действие нельзя отменить.
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
