@extends('layouts.user')

@section('title', 'Дашборд - Cliently')
@section('page-title', 'Дашборд')
@section('page-description', 'Обзор вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
@endpush

@section('content')

<div x-data="{ 
    period: 'today',
    showModal: false, 
    phone: '', 
    phoneDisplay: '', 
    client: '',
    stats: {
        total: 12,
        confirmed: 8,
        pending: 3,
        completed: 10,
        revenue: 25000,
        clients: 45
    },
    todayAppointments: [
        {
            id: 1,
            time: '09:00',
            service: 'Стрижка мужская',
            client: 'Алексей Иванов',
            clientPhone: '+79991234567',
            clientPhoneDisplay: '+7 (999) 123-45-67',
            duration: '45 мин',
            master: 'Иван Петров',
            status: 'confirmed'
        },
        {
            id: 2,
            time: '11:00',
            service: 'Стрижка женская',
            client: 'Мария Смирнова',
            clientPhone: '+79992345678',
            clientPhoneDisplay: '+7 (999) 234-56-78',
            duration: '1 ч 30 мин',
            master: 'Анна Козлова',
            status: 'confirmed'
        },
        {
            id: 3,
            time: '14:00',
            service: 'Окрашивание',
            client: 'Ольга Волкова',
            clientPhone: '+79993456789',
            clientPhoneDisplay: '+7 (999) 345-67-89',
            duration: '2 ч',
            master: 'Анна Козлова',
            status: 'confirmed'
        },
        {
            id: 4,
            time: '16:30',
            service: 'Оформление бороды',
            client: 'Сергей Новиков',
            clientPhone: '+79994567890',
            clientPhoneDisplay: '+7 (999) 456-78-90',
            duration: '30 мин',
            master: 'Иван Петров',
            status: 'confirmed'
        }
    ],
    pendingAppointments: [
        {
            id: 5,
            date: '2024-12-16',
            time: '10:30',
            service: 'Стрижка мужская',
            client: 'Иван Петров',
            clientPhone: '+79991112233',
            clientPhoneDisplay: '+7 (999) 111-22-33'
        },
        {
            id: 6,
            date: '2024-12-16',
            time: '12:00',
            service: 'Стрижка женская',
            client: 'Анна Смирнова',
            clientPhone: '+79992223344',
            clientPhoneDisplay: '+7 (999) 222-33-44'
        },
        {
            id: 7,
            date: '2024-12-16',
            time: '14:15',
            service: 'Оформление бороды',
            client: 'Сергей',
            clientPhone: '+79993334455',
            clientPhoneDisplay: '+7 (999) 333-44-55'
        }
    ],
    openModal(phone, phoneDisplay, client) {
        this.phone = phone;
        this.phoneDisplay = phoneDisplay;
        this.client = client;
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
    },
    handleCall(phone, phoneDisplay, client) {
        this.openModal(phone, phoneDisplay, client);
    },
    formatTime(time) {
        return time;
    },
    formatMoney(amount) {
        return new Intl.NumberFormat('ru-RU', { 
            style: 'currency', 
            currency: 'RUB',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    },
    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('ru-RU', { 
            day: 'numeric', 
            month: 'long' 
        }).format(date);
    }
}">
    <!-- Основной контент: 2 колонки на десктопе, вертикально на мобильных -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 md:gap-6">
        <!-- Левая колонка: Записи (основной контент) -->
        <div class="md:col-span-3 space-y-4 md:space-y-6">
            <!-- 1. Записи на сегодня (ГЛАВНОЕ) -->
            <section>
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <!-- Заголовок внутри карточки -->
                    <div class="flex items-center justify-between px-3 md:px-4 pt-3 md:pt-4 pb-2 md:pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-day text-indigo-600 dark:text-indigo-400"></i>
                            <span>Сегодня<span class="hidden sm:inline">, <span x-text="formatDate(new Date().toISOString().split('T')[0])"></span></span></span>
                        </h2>
                        <a href="#" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            Календарь
                        </a>
                    </div>

                    <!-- Временная шкала -->
                    <div>
                    <template x-if="todayAppointments.length === 0">
                        <div class="p-8 text-center">
                            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                            <p class="text-slate-500 dark:text-slate-400">Нет записей на сегодня</p>
                            <a href="#" class="mt-3 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                Создать запись
                            </a>
                        </div>
                    </template>

                    <template x-for="appointment in todayAppointments" :key="appointment.id">
                        <div class="p-3 md:p-4 border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-12 md:w-14 h-6 md:h-7 rounded bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-medium flex-shrink-0">
                                            <span x-text="formatTime(appointment.time)"></span>
                                        </span>
                                        <span class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="appointment.service"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button @click="handleCall(appointment.clientPhone, appointment.clientPhoneDisplay, appointment.client)"
                                        class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open"
                                            class="h-8 w-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                        </button>
                                        <div x-show="open" 
                                            @click.away="open = false" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg z-10"
                                            style="display: none;">
                                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                                <i class="fa-regular fa-eye w-4 inline-block"></i> Просмотр
                                            </a>
                                            <button class="w-full text-left px-4 py-2 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/20">
                                                <i class="fa-solid fa-check w-4 inline-block"></i> Выполнено
                                            </button>
                                            <button class="w-full text-left px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20">
                                                <i class="fa-solid fa-xmark w-4 inline-block"></i> Отменить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    </div>
                </div>
            </section>

            <!-- 2. Требующие внимания -->
            <section id="pending" x-show="pendingAppointments.length > 0">
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <!-- Заголовок внутри карточки -->
                    <div class="flex items-center justify-between px-3 md:px-4 pt-3 md:pt-4 pb-2 md:pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-exclamation-triangle text-amber-600 dark:text-amber-400"></i>
                            <span>Требуют внимания</span>
                        </h2>
                        <a href="#" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            Все записи
                        </a>
                    </div>

                    <!-- Контент -->
                    <template x-for="appointment in pendingAppointments" :key="appointment.id">
                        <div class="p-3 md:p-4 border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-start justify-between gap-2 md:gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 md:gap-3 mb-1.5 md:mb-2">
                                        <span class="inline-flex items-center justify-center w-14 md:w-16 h-7 md:h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-sm font-semibold flex-shrink-0">
                                            <span x-text="formatTime(appointment.time)"></span>
                                        </span>
                                        <span class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="appointment.service"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 md:gap-2 text-sm text-slate-600 dark:text-slate-400">
                                        <i class="fa-solid fa-user text-xs"></i>
                                        <span class="truncate" x-text="appointment.client"></span>
                                        <span class="text-slate-300 dark:text-slate-600 hidden sm:inline">•</span>
                                        <span class="hidden sm:inline" x-text="formatDate(appointment.date)"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 md:gap-2 flex-shrink-0">
                                    <button @click="handleCall(appointment.clientPhone, appointment.clientPhoneDisplay, appointment.client)"
                                        class="h-8 w-8 md:h-9 md:w-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors flex items-center justify-center">
                                        <i class="fa-solid fa-phone text-xs md:text-sm"></i>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open"
                                            class="h-8 w-8 md:h-9 md:w-9 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-ellipsis-vertical text-xs md:text-sm"></i>
                                        </button>
                                        <div x-show="open" 
                                            @click.away="open = false" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg z-10"
                                            style="display: none;">
                                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                                <i class="fa-regular fa-eye w-4 inline-block"></i> Просмотр
                                            </a>
                                            <button class="w-full text-left px-4 py-2 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/20">
                                                <i class="fa-solid fa-check w-4 inline-block"></i> Подтвердить
                                            </button>
                                            <button class="w-full text-left px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20">
                                                <i class="fa-solid fa-xmark w-4 inline-block"></i> Отменить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>

        <!-- Правая колонка: Быстрые действия и To-Do (сайдбар) - на мобильных показывается после записей -->
        <div class="md:col-span-2 space-y-4 md:space-y-6 order-2 md:order-2">
            <!-- 3. Быстрые действия -->
            <section>
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <!-- Заголовок внутри карточки -->
                    <div class="px-3 md:px-4 pt-3 md:pt-4 pb-2 md:pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-indigo-600 dark:text-indigo-400"></i>
                            <span>Быстрые действия</span>
                        </h3>
                    </div>
                    
                    <!-- Контент -->
                    <div class="p-3 md:p-4">
                        <div class="flex flex-row gap-2">
                            <a href="#" class="flex-1 inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Запись</span>
                            </a>
                            <a href="{{ route('clients.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                <i class="fa-solid fa-user-plus text-xs"></i>
                                <span>Клиент</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. Заметки -->
            <section>
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <!-- Заголовок внутри карточки -->
                    <div class="px-3 md:px-4 pt-3 md:pt-4 pb-2 md:pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-note-sticky text-indigo-600 dark:text-indigo-400"></i>
                            <span>Заметки</span>
                        </h3>
                    </div>
                    
                    <!-- Контент -->
                    <div class="p-3 md:p-4">
                        <!-- Форма добавления заметки -->
                        <form method="POST" action="#" class="mb-3">
                            @csrf
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    name="text"
                                    required
                                    class="flex-1 px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <button
                                    type="submit"
                                    class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors flex items-center justify-center">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Список заметок -->
                        @php
                            $notes = [
                                ['id' => 1, 'text' => 'Позвонить клиенту Иванову', 'completed' => false],
                                ['id' => 2, 'text' => 'Заказать краску для волос', 'completed' => false],
                                ['id' => 3, 'text' => 'Обновить прайс-лист', 'completed' => true],
                            ];
                        @endphp

                        <div class="space-y-2">
                            <!-- На мобильных показываем только первые 3 заметки, на десктопе все -->
                            @forelse($notes as $index => $note)
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group {{ $index >= 3 ? 'hidden md:flex' : '' }}">
                                    <!-- Форма для обновления статуса (PATCH) -->
                                    <form method="POST" action="#" class="flex-shrink-0 flex items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input 
                                            type="checkbox" 
                                            {{ $note['completed'] ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                                    </form>
                                    <span 
                                        class="flex-1 text-sm leading-5 {{ $note['completed'] ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $note['text'] }}
                                    </span>
                                    <!-- Форма для удаления (DELETE) -->
                                    <form method="POST" action="#" class="flex-shrink-0 flex items-center" onsubmit="return confirm('Удалить заметку?')">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="opacity-0 group-hover:opacity-100 h-5 w-5 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-all">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Нет заметок</p>
                                </div>
                            @endforelse
                            @if(count($notes) > 3)
                                <div class="md:hidden pt-2">
                                    <a href="#" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline text-center block">
                                        Показать все заметки ({{ count($notes) }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Модальное окно для номера телефона -->
    <div x-show="showModal" 
         @click.away="closeModal()"
         @keydown.escape.window="closeModal()"
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
            class="bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <!-- Заголовок -->
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                <button @click="closeModal()" 
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Контент -->
            <div class="px-4 md:px-6 py-4 md:py-5">
                <!-- Клиент -->
                <div class="mb-4">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Клиент</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                    </div>
                </div>

                <!-- Телефон -->
                <div class="mb-6">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Телефон</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-300"></i>
                        </div>
                        <p class="text-xl font-bold text-slate-900 dark:text-white" x-text="phoneDisplay"></p>
                    </div>
                </div>

                <!-- Действия -->
                <div class="space-y-2">
                    <a :href="`tel:${phone}`"
                        class="md:hidden w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors">
                        <i class="fa-solid fa-phone text-sm"></i>
                        <span>Позвонить</span>
                    </a>
                    <button @click="navigator.clipboard.writeText(phone); closeModal();"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 md:bg-slate-100 md:dark:bg-slate-800 px-4 py-3 text-sm font-medium text-white md:text-slate-700 md:dark:text-slate-300 hover:bg-indigo-700 md:hover:bg-slate-200 md:dark:hover:bg-slate-700 active:bg-indigo-800 transition-colors">
                        <i class="fa-regular fa-copy text-sm"></i>
                        <span>Копировать номер</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Автоматическое скрывание уведомлений через 5 секунд
        document.querySelectorAll('.toast-notification').forEach((notification) => {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        });
    </script>
@endpush
