@extends('layouts.user')

@section('title', 'Настройки бизнеса - Cliently')
@section('page-title', 'Настройки бизнеса')
@section('page-description', 'Управление данными вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
    ]" />
@endpush

@section('content')

<div class="space-y-4 md:space-y-6 w-full overflow-x-hidden">
    <!-- Приветственный блок с информацией о бизнесе -->
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 rounded-lg border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm overflow-hidden">
        <div class="p-4 md:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="h-16 w-16 md:h-20 md:w-20 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i class="fa-solid fa-building text-white text-2xl md:text-3xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white mb-1.5 truncate">
                    {{ $business->name }}
                </h2>
                    <p class="text-sm md:text-base text-slate-600 dark:text-slate-400 mb-3">
                    Управляйте настройками вашего бизнеса
                </p>
                    @if($business->phone || $business->email)
                        <div class="flex flex-wrap items-center gap-3 md:gap-4 text-xs md:text-sm">
                            @if($business->phone)
                                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                    <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                                    <span>{{ $business->phone }}</span>
                                </div>
                            @endif
                            @if($business->email)
                                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                    <i class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400"></i>
                                    <span class="truncate max-w-[200px]">{{ $business->email }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <a href="{{ route('settings.business.edit') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-800 rounded-lg border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors flex-shrink-0">
                    <i class="fa-solid fa-pencil text-xs"></i>
                    <span>Редактировать</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Публичная ссылка -->
    @if($business->slug)
    <div x-data="{ 
        copied: false,
        publicUrl: '{{ url('/') }}/book/{{ $business->slug }}',
        async copyUrl() {
            try {
                await navigator.clipboard.writeText(this.publicUrl);
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (err) {
                // Fallback для старых браузеров
                const textArea = document.createElement('textarea');
                textArea.value = this.publicUrl;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.copied = true;
                    setTimeout(() => {
                        this.copied = false;
                    }, 2000);
                } catch (fallbackErr) {
                    console.error('Ошибка копирования:', fallbackErr);
                }
                document.body.removeChild(textArea);
            }
        }
    }" class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-950/30 dark:to-indigo-900/30 rounded-lg border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm overflow-hidden">
        <div class="p-4 md:p-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-link text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-0.5">
                            Публичная ссылка на запись
            </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Поделитесь с клиентами для онлайн-записи
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-indigo-200 dark:border-indigo-800/50 p-3 md:p-4">
                <div class="flex flex-col sm:flex-row gap-2 sm:items-center mb-3">
                    <div class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2.5 overflow-hidden">
                        <span class="text-sm text-slate-700 dark:text-slate-300 font-mono break-all break-words min-w-0 block" style="word-break: break-word; overflow-wrap: anywhere;" x-text="publicUrl"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="publicUrl" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors flex-shrink-0"
                           title="Открыть ссылку">
                            <i class="fa-solid fa-external-link text-xs"></i>
                            <span class="hidden sm:inline">Открыть</span>
                        </a>
                <button type="button" 
                                @click="copyUrl()"
                                :class="copied ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-medium text-white active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex-shrink-0"
                        title="Копировать ссылку">
                            <i :class="copied ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="text-xs"></i>
                            <span class="hidden sm:inline" x-text="copied ? 'Скопировано!' : 'Копировать'"></span>
                </button>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-info-circle text-indigo-500 dark:text-indigo-400"></i>
                    <span>Клиенты смогут записаться онлайн по этой ссылке</span>
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
        <!-- Локации -->
        <a href="{{ route('settings.locations') }}" 
               class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700 transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-100 dark:bg-emerald-500/10 rounded-bl-full -mr-10 -mt-10 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/20 transition-colors"></div>
                <div class="relative">
                    <div class="h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-4 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/30 transition-colors">
                    <i class="fa-solid fa-location-dot text-emerald-600 dark:text-emerald-400 text-lg"></i>
                </div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Локации
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $business->locations->count() }}</span>
                        <span class="ml-1">{{ $business->locations->count() === 1 ? 'локация' : ($business->locations->count() < 5 ? 'локации' : 'локаций') }}</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                        <span>Управлять</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Услуги -->
        <a href="{{ route('services.index') }}" 
               class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-purple-300 dark:hover:border-purple-700 transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-purple-100 dark:bg-purple-500/10 rounded-bl-full -mr-10 -mt-10 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/20 transition-colors"></div>
                <div class="relative">
                    <div class="h-12 w-12 rounded-xl bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center mb-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                    <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400 text-lg"></i>
                </div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                        Услуги
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $business->services->count() }}</span>
                        <span class="ml-1">{{ $business->services->count() === 1 ? 'услуга' : ($business->services->count() < 5 ? 'услуги' : 'услуг') }}</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-purple-600 dark:text-purple-400 font-medium">
                        <span>Управлять</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Мастера -->
        <a href="{{ route('settings.masters') }}" 
               class="group relative bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md hover:border-amber-300 dark:hover:border-amber-700 transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-amber-100 dark:bg-amber-500/10 rounded-bl-full -mr-10 -mt-10 group-hover:bg-amber-200 dark:group-hover:bg-amber-500/20 transition-colors"></div>
                <div class="relative">
                    <div class="h-12 w-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mb-4 group-hover:bg-amber-200 dark:group-hover:bg-amber-500/30 transition-colors">
                    <i class="fa-solid fa-user-tie text-amber-600 dark:text-amber-400 text-lg"></i>
                </div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Мастера
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $business->masters->count() }}</span>
                        <span class="ml-1">{{ $business->masters->count() === 1 ? 'мастер' : ($business->masters->count() < 5 ? 'мастера' : 'мастеров') }}</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-amber-600 dark:text-amber-400 font-medium">
                        <span>Управлять</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Информационные подсказки -->
    @if($business->locations->count() === 0 || $business->services->count() === 0 || $business->masters->count() === 0)
    <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-lg p-4 md:p-5">
        <div class="flex items-start gap-3">
            <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-lightbulb text-amber-600 dark:text-amber-400"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">
                    Начните настройку
                </h4>
                <p class="text-xs text-amber-700 dark:text-amber-300 mb-3">
                    Для полноценной работы системы рекомендуется настроить:
                </p>
                <ul class="space-y-1.5 text-xs text-amber-700 dark:text-amber-300">
                    @if($business->locations->count() === 0)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-circle text-[4px]"></i>
                            <span>Добавьте хотя бы одну локацию</span>
                        </li>
                    @endif
                    @if($business->services->count() === 0)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-circle text-[4px]"></i>
                            <span>Создайте услуги для записи</span>
                        </li>
                    @endif
                    @if($business->masters->count() === 0)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-circle text-[4px]"></i>
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

