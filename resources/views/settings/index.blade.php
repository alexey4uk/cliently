@extends('layouts.user')

@section('title', 'Настройки бизнеса - Cliently')
@section('page-title', 'Настройки бизнеса')
@section('page-description', 'Управление данными вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')]]" />
@endpush

@section('content')

    <div class="space-y-4 md:space-y-6 w-full overflow-x-hidden">
        <!-- Приветственный блок с информацией о бизнесе -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 md:p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    {{-- Акцентная иконка --}}
                    <div
                        class="h-16 w-16 md:h-20 md:w-20 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fa-solid fa-building text-white text-2xl md:text-3xl"></i>
                    </div>

                    {{-- Информация о бизнесе --}}
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white mb-1.5 truncate">
                            {{ $business->name }}
                        </h2>
                        <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-3">
                            Управляйте настройками вашего бизнеса
                        </p>

                        @if ($business->phone || $business->email)
                            <div class="flex flex-wrap items-center gap-3 md:gap-4 text-xs md:text-sm">
                                @if ($business->phone)
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <i class="fa-solid fa-phone text-indigo-500 dark:text-indigo-400"></i>
                                        <span>{{ $business->phone }}</span>
                                    </div>
                                @endif
                                @if ($business->email)
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <i class="fa-solid fa-envelope text-indigo-500 dark:text-indigo-400"></i>
                                        <span class="truncate max-w-[200px]">{{ $business->email }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Кнопка "Редактировать" --}}
                    <a href="{{ route('settings.business.edit') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all flex-shrink-0 shadow-sm active:scale-95">
                        <i class="fa-solid fa-pencil text-xs"></i>
                        <span>Редактировать</span>
                    </a>
                </div>
            </div>
        </div>


        <!-- Публичная ссылка -->
        @php
            $fullUrl = route('public.appointments.show', ['slug' => $business->slug]);
            $displayUrl = str_replace(['http://', 'https://', 'www.'], '', $fullUrl);
        @endphp

        @if ($business->slug)
            <div x-data="{
                copied: false,
                publicUrl: '{{ $fullUrl }}',
                async copyUrl() {
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(this.publicUrl);
                        } else {
                            throw new Error();
                        }
                    } catch (err) {
                        const textArea = document.createElement('textarea');
                        textArea.value = this.publicUrl;
                        textArea.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0;';
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                    }
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }"
                class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 md:p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="h-10 w-10 rounded-xl bg-green-500 dark:bg-green-600 flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fa-solid fa-share-alt text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-0.5">
                                Ваша публичная ссылка
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Мгновенно поделитесь с клиентами
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-2.5 md:p-3">
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">

                            {{-- Поле с обрезанной ссылкой (Домен + Путь) --}}
                            <div class="flex-1 min-w-0 bg-transparent px-1">
                                <span
                                    class="text-sm text-slate-700 dark:text-slate-300 font-mono block truncate selection:bg-green-100"
                                    title="{{ $fullUrl }}">
                                    {{ $displayUrl }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Кнопка "Перейти" (иконка) --}}
                                <a href="{{ $fullUrl }}" target="_blank" rel="noopener"
                                    class="p-2.5 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors shrink-0 shadow-sm"
                                    title="Открыть ссылку">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>

                                {{-- Кнопка "Копировать" --}}
                                <button type="button" @click="copyUrl()" aria-live="polite"
                                    :class="copied ? 'bg-green-600' : 'bg-indigo-600'"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white rounded-lg transition-all active:scale-95 min-w-[130px] shadow-md hover:shadow-lg focus:outline-hidden focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <i :class="copied ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="text-xs"></i>
                                    <span x-text="copied ? 'Готово!' : 'Копировать'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Основные разделы -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Основные разделы
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                <!-- Карточка: Локации -->
                <a href="{{ route('settings.locations') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-location-dot text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Локации
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            <span
                                class="font-semibold text-slate-900 dark:text-white">{{ $business->locations->count() }}</span>
                            <span
                                class="ml-1">{{ $business->locations->count() === 1 ? 'локация' : ($business->locations->count() < 5 ? 'локации' : 'локаций') }}</span>
                        </p>
                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Карточка: Услуги -->
                <a href="{{ route('services.index') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-scissors text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Услуги
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            <span
                                class="font-semibold text-slate-900 dark:text-white">{{ $business->services->count() }}</span>
                            <span
                                class="ml-1">{{ $business->services->count() === 1 ? 'услуга' : ($business->services->count() < 5 ? 'услуги' : 'услуг') }}</span>
                        </p>
                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Карточка: Мастера -->
                <a href="{{ route('settings.masters') }}"
                    class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all active:scale-[0.98]">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 transition-colors">
                            <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400 text-lg"></i>
                        </div>
                        <h3
                            class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Мастера
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            <span
                                class="font-semibold text-slate-900 dark:text-white">{{ $business->masters->count() }}</span>
                            <span
                                class="ml-1">{{ $business->masters->count() === 1 ? 'мастер' : ($business->masters->count() < 5 ? 'мастера' : 'мастеров') }}</span>
                        </p>
                        <div
                            class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>


        <!-- Информационные подсказки -->
        @if ($business->locations->count() === 0 || $business->services->count() === 0 || $business->masters->count() === 0)
            <div
                class="bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-lg p-4 md:p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    {{-- Иконка предупреждения --}}
                    <div
                        class="h-10 w-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-300"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1.5">
                            Начните настройку бизнеса
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                            Для полноценной работы системы рекомендуется настроить следующие разделы:
                        </p>

                        {{-- Список недостающих элементов --}}
                        <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                            @if ($business->locations->count() === 0)
                                <li class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                    <span>Добавьте хотя бы одну локацию</span>
                                </li>
                            @endif
                            @if ($business->services->count() === 0)
                                <li class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                    <span>Создайте услуги для записи</span>
                                </li>
                            @endif
                            @if ($business->masters->count() === 0)
                                <li class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                    <span>Добавьте мастеров</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection
