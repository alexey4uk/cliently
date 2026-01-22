@extends('layouts.user')

@section('title', 'Настройки тикетов')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Хлебные крошки -->
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-slate-600 dark:text-slate-400">
                <li>
                    <a href="{{ route('panel.tickets') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        Тикеты
                    </a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                </li>
                <li class="text-slate-900 dark:text-slate-200 font-medium">
                    Настройки
                </li>
            </ol>
        </nav>

        <!-- Заголовок -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
                Настройки тикет-системы
            </h1>
            <p class="text-slate-600 dark:text-slate-400">
                Управляйте параметрами системы тикетов для вашего бизнеса
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 rounded-lg flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                <span class="text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 rounded-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    <span class="font-medium text-red-800 dark:text-red-300">Ошибки валидации</span>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('panel.tickets.settings.update') }}" id="ticketSettingsForm">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <!-- Основные настройки -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-sliders-h text-indigo-600 dark:text-indigo-400"></i>
                            Основные настройки
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Включить систему тикетов -->
                        <div class="flex items-start justify-between gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Включить систему тикетов
                                </label>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Разрешить создание и обработку тикетов в системе
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enabled" value="1" {{ $settings->enabled ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- SLA время ответа -->
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                            <label for="sla_response_time" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                                SLA время ответа
                                <span class="text-slate-500 dark:text-slate-400 font-normal">(минуты)</span>
                            </label>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                                Максимальное время ответа на тикет в минутах
                            </p>
                            <div class="max-w-xs">
                                <div class="relative">
                                    <input type="number" 
                                        id="sla_response_time" 
                                        name="sla_response_time" 
                                        value="{{ old('sla_response_time', $settings->sla_response_time ?? 60) }}" 
                                        min="0"
                                        step="1"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fa-solid fa-clock text-slate-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email-уведомления -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-indigo-600 dark:text-indigo-400"></i>
                            Email-уведомления
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Включить email-уведомления -->
                        <div class="flex items-start justify-between gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Включить email-уведомления
                                </label>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Отправлять уведомления о новых тикетах и обновлениях на email
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications_enabled" value="1" {{ $settings->email_notifications_enabled ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Получатели уведомлений -->
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                            <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                                Получатели уведомлений
                            </label>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                                Email адреса, на которые будут отправляться уведомления о тикетах
                            </p>
                            <div id="emailRecipientsContainer" class="space-y-2">
                                @php
                                    $recipients = old('email_notification_recipients', $settings->email_notification_recipients ?? []);
                                @endphp
                                @if(count($recipients) > 0)
                                    @foreach($recipients as $index => $email)
                                        <div class="flex items-center gap-2 email-recipient-item">
                                            <input type="email" 
                                                name="email_notification_recipients[]" 
                                                value="{{ $email }}"
                                                placeholder="email@example.com"
                                                class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                            <button type="button" 
                                                onclick="removeEmailRecipient(this)"
                                                class="p-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/20 rounded-lg transition-colors">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center gap-2 email-recipient-item">
                                        <input type="email" 
                                            name="email_notification_recipients[]" 
                                            value=""
                                            placeholder="email@example.com"
                                            class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <button type="button" 
                                            onclick="removeEmailRecipient(this)"
                                            class="p-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/20 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" 
                                onclick="addEmailRecipient()"
                                class="mt-3 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/20 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 rounded-lg transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Добавить email</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.tickets') }}" 
                        class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit" 
                        class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-save"></i>
                        <span>Сохранить настройки</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function addEmailRecipient() {
            const container = document.getElementById('emailRecipientsContainer');
            const newItem = document.createElement('div');
            newItem.className = 'flex items-center gap-2 email-recipient-item';
            newItem.innerHTML = `
                <input type="email" 
                    name="email_notification_recipients[]" 
                    value=""
                    placeholder="email@example.com"
                    class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                <button type="button" 
                    onclick="removeEmailRecipient(this)"
                    class="p-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/20 rounded-lg transition-colors">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            container.appendChild(newItem);
            newItem.querySelector('input').focus();
        }

        function removeEmailRecipient(button) {
            const container = document.getElementById('emailRecipientsContainer');
            const items = container.querySelectorAll('.email-recipient-item');
            if (items.length > 1) {
                button.closest('.email-recipient-item').remove();
            } else {
                // Если остался один элемент, просто очищаем его
                button.closest('.email-recipient-item').querySelector('input').value = '';
            }
        }
    </script>
    @endpush
@endsection
