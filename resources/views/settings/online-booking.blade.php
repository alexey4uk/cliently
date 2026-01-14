@extends('layouts.user')

@section('title', 'Онлайн-запись - Cliently')
@section('page-title', 'Онлайн-запись')
@section('page-description', 'Управление ссылками для онлайн-записи клиентов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Онлайн-запись', 'url' => null]]" />
@endpush

@section('content')
    <div class="space-y-4 md:space-y-6 w-full overflow-x-hidden">
        <!-- Способы записи клиентов -->
        @php
            $fullUrl = route('public.appointments.show', ['slug' => $business->slug]);
            $displayUrl = str_replace(['http://', 'https://', 'www.'], '', $fullUrl);
            $telegramUrl = $bot ? 'https://t.me/' . $bot->name . '?start=' . $business->slug : null;
        @endphp

        @if ($business->slug)
            <div x-data="{
                copiedWeb: false,
                copiedTelegram: false,
                publicUrl: '{{ $fullUrl }}',
                telegramUrl: '{{ $telegramUrl }}',
                async copyUrl(type) {
                    let url = type === 'web' ? this.publicUrl : this.telegramUrl;
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(url);
                        } else {
                            throw new Error();
                        }
                    } catch (err) {
                        const textArea = document.createElement('textarea');
                        textArea.value = url;
                        textArea.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0;';
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                    }
                    if (type === 'web') {
                        this.copiedWeb = true;
                        setTimeout(() => this.copiedWeb = false, 2000);
                    } else {
                        this.copiedTelegram = true;
                        setTimeout(() => this.copiedTelegram = false, 2000);
                    }
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
                                Способы записи клиентов
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Поделитесь ссылками для онлайн-записи
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        {{-- Веб-страница --}}
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-2.5 md:p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-globe text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Веб-страница</span>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                                <div class="flex-1 min-w-0 bg-transparent px-1">
                                    <span
                                        class="text-sm text-slate-700 dark:text-slate-300 font-mono block truncate selection:bg-green-100"
                                        title="{{ $fullUrl }}">
                                        {{ $displayUrl }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $fullUrl }}" target="_blank" rel="noopener"
                                        class="p-2.5 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors shrink-0 shadow-sm"
                                        title="Открыть ссылку">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                    <button type="button" @click="copyUrl('web')" aria-live="polite"
                                        :class="copiedWeb ? 'bg-green-600' : 'bg-indigo-600'"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white rounded-lg transition-all active:scale-95 min-w-[130px] shadow-md hover:shadow-lg focus:outline-hidden focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        <i :class="copiedWeb ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="text-xs"></i>
                                        <span x-text="copiedWeb ? 'Готово!' : 'Копировать'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Telegram бот --}}
                        @if ($bot)
                            <div class="bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-2.5 md:p-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fa-brands fa-telegram text-sky-600 dark:text-sky-400 text-sm"></i>
                                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Telegram бот</span>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                                    <div class="flex-1 min-w-0 bg-transparent px-1">
                                        <span
                                            class="text-sm text-slate-700 dark:text-slate-300 font-mono block truncate selection:bg-green-100"
                                            title="{{ $telegramUrl }}">
                                            t.me/{{ $bot->name }}?start={{ $business->slug }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ $telegramUrl }}" target="_blank" rel="noopener"
                                            class="p-2.5 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors shrink-0 shadow-sm"
                                            title="Открыть в Telegram">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </a>
                                        <button type="button" @click="copyUrl('telegram')" aria-live="polite"
                                            :class="copiedTelegram ? 'bg-green-600' : 'bg-indigo-600'"
                                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white rounded-lg transition-all active:scale-95 min-w-[130px] shadow-md hover:shadow-lg focus:outline-hidden focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            <i :class="copiedTelegram ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="text-xs"></i>
                                            <span x-text="copiedTelegram ? 'Готово!' : 'Копировать'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
