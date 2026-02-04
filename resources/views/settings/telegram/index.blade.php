@extends('layouts.user')

@section('title', 'Telegram - Cliently')
@section('page-title', 'Telegram')
@section('page-description', 'Уведомления в Telegram и запись для клиентов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Telegram', 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto" 
     x-data="{
         disconnecting: false,
         async disconnectTelegram() {
             if (!confirm('Отвязать Telegram? Уведомления в Telegram перестанут приходить.')) return;
             this.disconnecting = true;
             try {
                 const res = await fetch('{{ route('settings.notifications.telegram.disconnect') }}', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                         'Accept': 'application/json',
                         'Content-Type': 'application/json',
                     },
                 });
                 if (res.ok) {
                     window.location.reload();
                 } else {
                     alert('Не удалось отвязать. Попробуйте ещё раз.');
                 }
             } catch (e) {
                 alert('Ошибка. Попробуйте ещё раз.');
             } finally {
                 this.disconnecting = false;
             }
         }
     }">

    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center shrink-0">
                <i class="fa-brands fa-telegram text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Telegram</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Уведомления в мессенджере и запись клиентов через бота</p>
            </div>
        </div>
    </div>

    <!-- Уведомления в Telegram: привязка + ссылка на полные настройки -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-bell text-indigo-500"></i>
            Уведомления в Telegram
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">
            Чтобы получать уведомления о записях, тикетах и других событиях в Telegram, привяжите свой аккаунт. Каналы доставки (Email / Telegram) по типам событий настраиваются ниже.
        </p>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 mb-5">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                    <i class="fa-brands fa-telegram text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    @if($user->isTelegramConnected())
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Telegram привязан</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Уведомления приходят в мессенджер</p>
                    @else
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Telegram не привязан</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Привяжите для получения в Telegram</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if($user->isTelegramConnected())
                    <button type="button"
                            @click="disconnectTelegram()"
                            :disabled="disconnecting"
                            class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="disconnecting ? 'Отвязка...' : 'Отвязать'"></span>
                    </button>
                @elseif($telegramLink ?? null)
                    <a href="{{ $telegramLink }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fa-brands fa-telegram"></i>
                        Привязать
                    </a>
                @else
                    <span class="text-sm text-slate-400">Бот не настроен</span>
                @endif
            </div>
        </div>

        <div class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg border border-indigo-200 dark:border-indigo-600/20">
            <i class="fa-solid fa-sliders text-indigo-600 dark:text-indigo-400 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-slate-900 dark:text-white mb-1">Полные настройки уведомлений</p>
                <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                    Выбор каналов (Email / Telegram) по типам событий: записи, тикеты, подписка и др.
                </p>
                <a href="{{ route('settings.notifications.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    <span>Открыть настройки уведомлений</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    @if ($botState !== 'no-bot')
        <!-- Запись для клиентов -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-start gap-4 p-4 bg-sky-50 dark:bg-sky-500/10 rounded-xl border border-sky-200 dark:border-sky-600/20">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calendar-check text-sky-600 dark:text-sky-400"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">Ссылка и QR для записи клиентов</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        Ссылку на бота для клиентов и QR-код настройте в разделе «Онлайн-запись» — там же веб-ссылка и переключатель записи.
                    </p>
                    <a href="{{ route('settings.online-booking') }}"
                       class="inline-flex items-center gap-2 text-sm font-medium text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300">
                        <span>Перейти в настройки онлайн-записи</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="text-center py-8">
                <div class="h-14 w-14 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-robot text-amber-600 dark:text-amber-400 text-2xl"></i>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Запись клиентов через Telegram настраивается в разделе «Онлайн-запись», когда бот будет доступен.</p>
                <a href="{{ route('settings.online-booking') }}" class="inline-flex items-center gap-2 text-sm font-medium text-sky-600 dark:text-sky-400 mt-3">
                    Онлайн-запись
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    @endif

    <!-- Преимущества Telegram -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                <i class="fa-solid fa-star text-amber-600 dark:text-amber-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Зачем привязывать Telegram</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-bolt text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Мгновенные уведомления</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">Узнавайте о новых записях сразу</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-mobile-screen text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Удобство для клиентов</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">Запись прямо в мессенджере</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-robot text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Автоматизация</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">Бот отвечает на вопросы клиентов</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-shield-halved text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Надежность</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">Стабильная работа и безопасность</p>
            </div>
        </div>
    </div>
</div>

@endsection
