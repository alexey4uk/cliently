@extends('layouts.user')

@section('title', 'Профиль клиента - Cliently')
@section('page-title', 'Профиль клиента')
@section('page-description', 'Информация о клиенте')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => $client->full_name, 'url' => null],
    ]" />
@endpush

@section('content')

    <div x-data="{
        showPhoneModal: false,
        phone: '',
        phoneDisplay: '',
        client: '',
        showDeleteModal: false,
        openPhoneModal(phone, phoneDisplay, client) {
            this.phone = phone;
            this.phoneDisplay = phoneDisplay;
            this.client = client;
            this.showPhoneModal = true;
        },
        closePhoneModal() {
            this.showPhoneModal = false;
        },
        openDeleteModal() {
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
        },
        confirmDelete() {
            const form = document.getElementById('delete-form');
            if (form) {
                form.submit();
            }
        }
    }" class="space-y-4 md:space-y-6">

        <!-- Заголовок страницы -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 md:gap-4">
                <!-- Аватар -->
                <div
                    class="h-16 w-16 md:h-20 md:w-20 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-xl md:text-2xl font-bold text-white shadow-lg flex-shrink-0">
                    {{ $client->initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white mb-1 truncate">
                        {{ $client->full_name }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <i class="fa-solid fa-calendar text-xs"></i>
                        <span>Клиент с {{ $client->created_at->format('d.m.Y') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-2">
                <!-- Кнопки действий -->
                <div class="flex gap-2">
                    <a href="{{ route('appointments.create', ['client_id' => $client->id]) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/30 transition-colors">
                        <i class="fa-solid fa-calendar-plus text-xs"></i>
                        <span>Записать</span>
                    </a>

                    <a href="{{ route('clients.edit', $client) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-700/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-500/30 transition-colors">
                        <i class="fa-solid fa-pencil text-xs"></i>
                        <span>Редактировать</span>
                    </a>

                    <button @click="openDeleteModal()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/30 transition-colors">
                        <i class="fa-solid fa-trash text-xs"></i>
                        <span>Удалить</span>
                    </button>

                    <form method="POST" action="{{ route('clients.destroy', $client) }}" id="delete-form" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        <!-- Статистика записей -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-emerald-600 dark:text-emerald-400 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Статистика записей
                    </h2>
                </div>
            </div>
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-1">
                            {{ $totalAppointments }}
                        </div>
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Всего записей
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400 mb-1">
                            {{ $completedAppointments }}
                        </div>
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Завершено
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                            {{ $upcomingAppointments }}
                        </div>
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Предстоящих
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Информация о клиенте -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Информация о клиенте
                    </h2>
                </div>
            </div>
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Имя
                        </label>
                        <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                            {{ $client->first_name }}
                        </p>
                    </div>

                    @if ($client->last_name)
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                Фамилия
                            </label>
                            <p class="text-sm md:text-base font-semibold text-slate-900 dark:text-white">
                                {{ $client->last_name }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Телефон
                        </label>
                        <button
                            @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                            class="text-sm md:text-base font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-phone text-xs text-slate-400"></i>
                            <span>{{ $client->phone }}</span>
                        </button>
                    </div>

                    @if ($client->email)
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                Email
                            </label>
                            <p
                                class="text-sm md:text-base font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                                <span class="break-all">{{ $client->email }}</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Системная информация -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <i class="fa-solid fa-info-circle text-slate-600 dark:text-slate-400 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Системная информация
                    </h2>
                </div>
            </div>
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Дата добавления
                        </label>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $client->created_at->format('d.m.Y') }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $client->created_at->format('H:i') }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Последнее обновление
                        </label>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $client->updated_at->format('d.m.Y') }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $client->updated_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для номера телефона -->
        <div x-show="showPhoneModal" @click.away="closePhoneModal()" @keydown.escape.window="closePhoneModal()"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
                <div
                    class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                    <button @click="closePhoneModal()"
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-4 md:px-6 py-4 md:py-5">
                    <div class="mb-4">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">
                            Клиент</p>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                            </div>
                            <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                        </div>
                    </div>
                    <div class="mb-6">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">
                            Телефон</p>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-300"></i>
                            </div>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" x-text="phoneDisplay"></p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <a :href="`tel:${phone}`"
                            class="md:hidden w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors">
                            <i class="fa-solid fa-phone text-sm"></i>
                            <span>Позвонить</span>
                        </a>
                        <button @click="navigator.clipboard.writeText(phone); closePhoneModal();"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 md:bg-slate-100 md:dark:bg-slate-800 px-4 py-3 text-sm font-medium text-white md:text-slate-700 md:dark:text-slate-300 hover:bg-indigo-700 md:hover:bg-slate-200 md:dark:hover:bg-slate-700 active:bg-indigo-800 transition-colors">
                            <i class="fa-regular fa-copy text-sm"></i>
                            <span>Копировать номер</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <div x-show="showDeleteModal" @click.away="closeDeleteModal()" @keydown.escape.window="closeDeleteModal()"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            style="display: none;">
            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
                <div
                    class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Подтверждение удаления
                    </h3>
                    <button @click="closeDeleteModal()"
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="px-4 md:px-6 py-4 md:py-5">
                    <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 mb-6">
                        Вы уверены, что хотите удалить клиента <span
                            class="font-semibold">{{ $client->full_name }}</span>? Это действие нельзя отменить.
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
