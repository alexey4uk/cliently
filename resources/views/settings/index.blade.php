@extends('layouts.user')

@section('title', 'Настройки бизнеса - Cliently')
@section('page-title', 'Настройки бизнеса')
@section('page-description', 'Управление данными вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
@endpush

@section('content')

<div class="space-y-6">
    <!-- Приветственный блок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-building text-white text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-1">
                    {{ $business->name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Управляйте настройками вашего бизнеса
                </p>
            </div>
        </div>
    </div>

    <!-- Публичная ссылка -->
    @if($business->slug)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                Ссылка на запись
            </h3>
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-800 rounded-md border border-slate-200 dark:border-slate-700 px-3 py-2.5 overflow-hidden">
                    <span id="publicUrl" class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-mono break-all min-w-0 block">{{ url('/') }}/book/{{ $business->slug }}</span>
                </div>
                <button type="button" 
                        id="copyUrlBtn"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex-shrink-0"
                        title="Копировать ссылку">
                    <i id="copyUrlIcon" class="fa-solid fa-copy text-xs"></i>
                    <span id="copyUrlText">Копировать</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Карточки разделов -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Настройки бизнеса -->
        <a href="{{ route('settings.business.edit') }}" 
           class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-500/30 transition-colors">
                    <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        Данные бизнеса
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        Название, телефон, описание
                    </p>
                    <div class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                        <span>Изменить</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Локации -->
        <a href="{{ route('settings.locations') }}" 
           class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/30 transition-colors">
                    <i class="fa-solid fa-location-dot text-emerald-600 dark:text-emerald-400 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        Локации
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        {{ $business->locations->count() }} {{ $business->locations->count() === 1 ? 'локация' : ($business->locations->count() < 5 ? 'локации' : 'локаций') }}
                    </p>
                    <div class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                        <span>Управлять</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Услуги -->
        <a href="{{ route('services.index') }}" 
           class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                    <i class="fa-solid fa-scissors text-purple-600 dark:text-purple-400 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        Услуги
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        {{ $business->services->count() }} {{ $business->services->count() === 1 ? 'услуга' : ($business->services->count() < 5 ? 'услуги' : 'услуг') }}
                    </p>
                    <div class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                        <span>Управлять</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Мастера -->
        <a href="{{ route('settings.masters') }}" 
           class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 dark:group-hover:bg-amber-500/30 transition-colors">
                    <i class="fa-solid fa-user-tie text-amber-600 dark:text-amber-400 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        Мастера
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        {{ $business->masters->count() }} {{ $business->masters->count() === 1 ? 'мастер' : ($business->masters->count() < 5 ? 'мастера' : 'мастеров') }}
                    </p>
                    <div class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                        <span>Управлять</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyBtn = document.getElementById('copyUrlBtn');
        const copyIcon = document.getElementById('copyUrlIcon');
        const copyText = document.getElementById('copyUrlText');
        const publicUrl = document.getElementById('publicUrl');
        
        if (!copyBtn || !publicUrl) return;
        
        const fullUrl = publicUrl.textContent.trim();
        
        copyBtn.addEventListener('click', async function() {
            try {
                await navigator.clipboard.writeText(fullUrl);
                
                // Визуальная обратная связь
                const originalIcon = copyIcon.className;
                const originalText = copyText.textContent;
                
                copyIcon.className = 'fa-solid fa-check text-xs';
                copyText.textContent = 'Скопировано!';
                copyBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                copyBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                
                setTimeout(() => {
                    copyIcon.className = originalIcon;
                    copyText.textContent = originalText;
                    copyBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    copyBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                }, 2000);
            } catch (err) {
                console.error('Ошибка копирования:', err);
                // Fallback для старых браузеров
                const textArea = document.createElement('textarea');
                textArea.value = fullUrl;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    copyText.textContent = 'Скопировано!';
                    setTimeout(() => {
                        copyText.textContent = 'Копировать';
                    }, 2000);
                } catch (fallbackErr) {
                    copyText.textContent = 'Ошибка';
                    setTimeout(() => {
                        copyText.textContent = 'Копировать';
                    }, 2000);
                }
                document.body.removeChild(textArea);
            }
        });
    });
</script>
@endpush

