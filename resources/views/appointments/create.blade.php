@extends('layouts.user')

@section('title', 'Создание записи - Cliently')
@section('page-title', 'Создание записи')
@section('page-description', 'Новая запись клиента')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Записи', 'url' => route('appointments.index')],
        ['title' => 'Создание записи', 'url' => null]
    ]" />
@endpush

@section('content')

@if (!$business)
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <i class="fa-solid fa-briefcase text-2xl text-slate-400"></i>
            </div>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Бизнес не найден</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Сначала создайте бизнес или примите приглашение.</p>
        </div>
    </div>
@else

<div class="max-w-6xl mx-auto" x-data="{
    selectedService: null,
    selectedMaster: null,
    selectedLocation: null,
    selectedClient: null,
    date: '{{ old('date', date('Y-m-d')) }}',
    time: '',
    status: '{{ old('status', 'pending') }}',
    notes: '{{ old('notes', '') }}',

    services: {{ json_encode($services->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'price' => (int)$s->price,
        'duration' => $s->duration,
    ]), JSON_HEX_TAG) }},
    masters: {{ json_encode($masters->map(fn($m) => [
        'id' => $m->id,
        'full_name' => trim($m->first_name . ' ' . ($m->last_name ?? '')),
    ]), JSON_HEX_TAG) }},
    locations: {{ json_encode($locations->map(fn($l) => [
        'id' => $l->id,
        'name' => $l->name,
        'full_address' => $l->full_address ?? '',
    ]), JSON_HEX_TAG) }},
    clients: {{ json_encode($clients->map(fn($c) => [
        'id' => $c->id,
        'full_name' => $c->full_name,
        'phone' => $c->phone,
        'initials' => $c->initials,
    ]), JSON_HEX_TAG) }},

    clientSearch: '',
    showQuickForm: false,
    quickName: '',
    quickPhone: '',
    quickCreating: false,
    quickError: '',
    loadingSlots: false,
    slots: [],
    busySlots: [],
    slotsError: '',
    slotsLoaded: false,

    init() {
        const oldServiceId = {{ old('service_id', 0) }};
        if (oldServiceId) {
            const s = this.services.find(x => x.id === oldServiceId);
            if (s) this.selectedService = s;
        }
        const oldMasterId = {{ old('master_id', 0) }};
        if (oldMasterId) {
            const m = this.masters.find(x => x.id === oldMasterId);
            if (m) this.selectedMaster = m;
        }
        const oldLocationId = {{ old('location_id', 0) }};
        if (oldLocationId) {
            const l = this.locations.find(x => x.id === oldLocationId);
            if (l) this.selectedLocation = l;
        }
        const oldClientId = {{ old('client_id', $selectedClientId ?? 0) }};
        if (oldClientId) {
            const c = this.clients.find(x => x.id === oldClientId);
            if (c) this.selectedClient = c;
        }
        const oldTime = '{{ old('time', '') }}';
        if (oldTime) this.time = oldTime;

        if (this.locations.length === 1) {
            this.selectedLocation = this.locations[0];
        }

        if (this.selectedService && this.date) {
            this.$nextTick(() => this.loadSlots());
        }
    },

    get filteredClients() {
        if (!this.clientSearch) return [];
        const q = this.clientSearch.toLowerCase();
        return this.clients.filter(c =>
            c.full_name.toLowerCase().includes(q) || c.phone.includes(q)
        );
    },

    get canSubmit() {
        return this.selectedService && this.date && this.time && this.selectedClient;
    },

    selectService(service) {
        this.selectedService = service;
        this.slotsLoaded = false;
        this.time = '';
        this.slots = [];
        if (this.date) this.$nextTick(() => this.loadSlots());
    },

    selectMaster(master) {
        this.selectedMaster = master;
        this.slotsLoaded = false;
        this.time = '';
        this.slots = [];
        this.loadSlots();
    },

    selectLocation(location) {
        this.selectedLocation = location;
        this.slotsLoaded = false;
        this.time = '';
        this.slots = [];
        this.loadSlots();
    },

    onDateChange() {
        this.slotsLoaded = false;
        this.time = '';
        this.slots = [];
        if (this.selectedService) this.$nextTick(() => this.loadSlots());
    },

    loadSlots() {
        if (!this.selectedService || !this.date) return;
        this.loadingSlots = true;
        this.slotsError = '';
        this.slots = [];
        this.busySlots = [];

        const params = new URLSearchParams({
            service_id: this.selectedService.id,
            date: this.date,
        });
        if (this.selectedMaster) params.append('master_id', this.selectedMaster.id);
        if (this.selectedLocation) params.append('location_id', this.selectedLocation.id);

        const url = '{{ route("api.public.appointments.available-slots", $business->slug) }}';

        fetch(url + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(r => r.json())
        .then(data => {
            this.loadingSlots = false;
            this.slotsLoaded = true;
            if (data.success) {
                this.slots = data.slots || [];
                this.busySlots = data.busy_slots || [];
                if (this.slots.length === 0 && this.busySlots.length === 0) {
                    const today = new Date().toISOString().split('T')[0];
                    if (this.date === today) {
                        this.slotsError = 'На сегодня нет свободных окон. Выберите другую дату.';
                    } else {
                        this.slotsError = 'Нет свободных окон на эту дату. Попробуйте другую дату или мастера.';
                    }
                } else if (this.slots.length === 0 && this.busySlots.length > 0) {
                    this.slotsError = 'На эту дату все окна заняты.';
                }
            } else {
                const today = new Date().toISOString().split('T')[0];
                if (this.date === today) {
                    this.slotsError = 'На сегодня нет свободных окон. Выберите другую дату.';
                } else {
                    this.slotsError = 'Нет свободных окон на эту дату. Попробуйте другую дату или мастера.';
                }
            }
        })
        .catch(() => {
            this.loadingSlots = false;
            this.slotsLoaded = true;
            this.slotsError = 'Ошибка загрузки. Обновите страницу.';
        });
    },

    selectTime(time) {
        this.time = time;
    },

    quickCreate() {
        if (!this.quickName.trim() || !this.quickPhone.trim()) return;
        this.quickCreating = true;
        this.quickError = '';

        const formData = new FormData();
        formData.append('first_name', this.quickName.trim());
        formData.append('last_name', '');
        formData.append('phone', this.quickPhone.trim());
        formData.append('phone_country_code', 'BY');
        formData.append('email', '');

        const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');

        fetch('{{ route('clients.store') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token || '',
            },
            body: formData,
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            this.quickCreating = false;
            if (status === 201 && data.client) {
                const newClient = {
                    id: data.client.id,
                    full_name: data.client.full_name,
                    phone: data.client.phone,
                    initials: data.client.initials,
                };
                this.clients.push(newClient);
                this.selectedClient = newClient;
                this.showQuickForm = false;
                this.quickName = '';
                this.quickPhone = '';
                this.clientSearch = '';
            } else if (status === 422 && data.errors) {
                const firstError = Object.values(data.errors).flat()[0];
                this.quickError = firstError || 'Ошибка валидации';
            } else {
                this.quickError = data.message || 'Ошибка при создании клиента';
            }
        })
        .catch(() => {
            this.quickCreating = false;
            this.quickError = 'Ошибка соединения. Попробуйте снова.';
        });
    },

    submit() {
        if (!this.canSubmit) return;
        this.$refs.form.submit();
    }
}">

    <form method="POST" action="{{ route('appointments.store') }}" x-ref="form">
        @csrf
        <input type="hidden" name="service_id" :value="selectedService?.id || ''">
        <input type="hidden" name="master_id" :value="selectedMaster?.id || ''">
        <input type="hidden" name="location_id" :value="selectedLocation?.id || ''">
        <input type="hidden" name="client_id" :value="selectedClient?.id || ''">
        <input type="hidden" name="date" :value="date">
        <input type="hidden" name="time" :value="time">
        <input type="hidden" name="status" :value="status">
        <input type="hidden" name="notes" :value="notes">

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            <!-- Left Column: Service + Client + Extra -->
            <div class="lg:col-span-3 space-y-5">

                <!-- Service -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                            <i class="fa-solid fa-scissors text-sm"></i>
                        </div>
                        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Услуга</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-1.5 max-h-[260px] overflow-y-auto pr-1">
                        <template x-for="service in services" :key="service.id">
                            <button type="button" @click="selectService(service)"
                                class="w-full text-left p-3 rounded-xl border-2 transition-all duration-150"
                                :class="selectedService?.id === service.id
                                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10'
                                    : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-white truncate leading-tight" x-text="service.name"></div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400" x-text="service.price.toLocaleString('ru-RU') + ' BYN'"></span>
                                            <span class="text-xs text-slate-400">•</span>
                                            <span class="text-xs text-slate-500"><i class="fa-solid fa-clock"></i> <span x-text="service.duration + ' мин'"></span></span>
                                        </div>
                                    </div>
                                    <div class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="selectedService?.id === service.id
                                            ? 'border-indigo-500 bg-indigo-500'
                                            : 'border-slate-300 dark:border-slate-600'">
                                        <i x-show="selectedService?.id === service.id" class="fa-solid fa-check text-white text-[9px]"></i>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                    @error('service_id')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Client -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Клиент</h2>
                    </div>

                    <div class="relative" @click.away="clientSearch = ''">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                            </div>
                            <input type="text" x-model="clientSearch"
                                placeholder="Поиск клиента по имени или телефону..."
                                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div x-show="clientSearch.length > 0" x-cloak
                            class="mt-1 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 shadow-lg overflow-hidden max-h-56 overflow-y-auto absolute z-20 w-full">
                            <template x-if="filteredClients.length === 0">
                                <div class="p-4 text-center text-sm text-slate-500">Клиенты не найдены</div>
                            </template>
                            <template x-for="client in filteredClients" :key="client.id">
                                <button type="button" @click="selectedClient = client; clientSearch = ''; showQuickForm = false"
                                    class="w-full px-4 py-2.5 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors flex items-center justify-between gap-3"
                                    :class="selectedClient?.id === client.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 text-xs font-bold shrink-0"
                                            x-text="client.initials">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="client.full_name"></div>
                                            <div class="text-xs text-slate-500" x-text="client.phone"></div>
                                        </div>
                                    </div>
                                    <i x-show="selectedClient?.id === client.id" class="fa-solid fa-check text-indigo-600 text-xs shrink-0"></i>
                                </button>
                            </template>
                            <button type="button" @click="showQuickForm = true; quickName = clientSearch; clientSearch = ''; selectedClient = null"
                                class="w-full px-4 py-2.5 text-left hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors flex items-center gap-3 border-t border-slate-100 dark:border-slate-700">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </div>
                                <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                    Создать «<span x-text="clientSearch"></span>»
                                </div>
                            </button>
                        </div>

                        <!-- Quick-create form -->
                        <div x-show="showQuickForm" x-cloak class="mt-3 p-4 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-500/5">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">Новый клиент</span>
                            </div>

                            <div class="space-y-2.5">
                                <input type="text" x-model="quickName" placeholder="Имя *"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <input type="text" x-model="quickPhone" placeholder="Телефон * (+375...)"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                                <div x-show="quickError" x-cloak class="text-xs text-rose-600" x-text="quickError"></div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="showQuickForm = false; quickError = ''"
                                        class="px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                                        Отмена
                                    </button>
                                    <button type="button" @click="quickCreate"
                                        :disabled="!quickName.trim() || !quickPhone.trim() || quickCreating"
                                        class="px-4 py-1.5 text-xs font-medium text-white rounded-lg transition-all"
                                        :class="!quickName.trim() || !quickPhone.trim() || quickCreating
                                            ? 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed'
                                            : 'bg-indigo-600 hover:bg-indigo-700'">
                                        <span x-show="!quickCreating" x-cloak>Создать</span>
                                        <span x-show="quickCreating" x-cloak><i class="fa-solid fa-spinner fa-spin"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedClient && clientSearch.length === 0" x-cloak class="mt-3">
                            <div class="flex items-center gap-3 p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl border border-indigo-100 dark:border-indigo-500/20">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-500/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-bold shrink-0"
                                    x-text="selectedClient?.initials">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-indigo-900 dark:text-indigo-200 truncate" x-text="selectedClient?.full_name"></div>
                                    <div class="text-xs text-indigo-600/70 dark:text-indigo-400/70" x-text="selectedClient?.phone"></div>
                                </div>
                                <button type="button" @click="selectedClient = null; clientSearch = ''"
                                    class="shrink-0 w-7 h-7 rounded-full bg-indigo-200/50 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-500 hover:bg-indigo-200 dark:hover:bg-indigo-500/30 transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('client_id')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Master & Location -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 shrink-0">
                            <i class="fa-solid fa-sliders text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Дополнительно</h2>
                            <p class="text-xs text-slate-400">Мастер и локация — можно не выбирать</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Мастер</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="selectedMaster = null"
                                    class="px-3 py-1.5 rounded-lg border text-xs font-medium transition-all"
                                    :class="!selectedMaster
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                        : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-300'">
                                    Любой
                                </button>
                                <template x-for="master in masters" :key="master.id">
                                    <button type="button" @click="selectMaster(master)"
                                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition-all"
                                        :class="selectedMaster?.id === master.id
                                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                            : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-300'"
                                        x-text="master.full_name">
                                    </button>
                                </template>
                            </div>
                            @error('master_id')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Локация</label>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="location in locations" :key="location.id">
                                    <button type="button" @click="selectLocation(location)"
                                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition-all max-w-full text-left"
                                        :class="selectedLocation?.id === location.id
                                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300'
                                            : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-300'">
                                        <span x-text="location.name"></span>
                                    </button>
                                </template>
                            </div>
                            @error('location_id')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status & Notes -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Статус</label>
                            <select x-model="status"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
                                <option value="pending">Ожидает</option>
                                <option value="confirmed">Подтверждено</option>
                                <option value="completed">Завершено</option>
                                <option value="cancelled">Отменено</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Заметки</label>
                            <input type="text" x-model="notes" placeholder="Любые заметки..."
                                class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            @error('notes')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Date & Time Grid -->
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center text-violet-600 dark:text-violet-400 shrink-0">
                            <i class="fa-solid fa-calendar-clock text-sm"></i>
                        </div>
                        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Дата и время</h2>
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <input type="date" x-model="date" @change="onDateChange"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        @error('date')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time slots -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-slate-500">Доступное время</span>
                            <button type="button" @click="loadSlots" x-show="slotsLoaded && (slots.length > 0 || busySlots.length > 0)" x-cloak
                                class="text-[10px] font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                <i class="fa-solid fa-rotate mr-1"></i>Обновить
                            </button>
                        </div>

                        <!-- No service selected -->
                        <div x-show="!selectedService" class="py-8 text-center">
                            <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-hand-pointer text-base"></i>
                            </div>
                            <p class="text-sm text-slate-500">Сначала выберите услугу</p>
                        </div>

                        <!-- Loading -->
                        <div x-show="loadingSlots" x-cloak class="py-8 text-center">
                            <i class="fa-solid fa-spinner fa-spin text-xl text-indigo-500"></i>
                            <p class="text-xs text-slate-500 mt-2">Загрузка...</p>
                        </div>

                        <!-- Error / no slots -->
                        <div x-show="slotsError" x-cloak class="py-6 text-center">
                            <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500">
                                <i class="fa-solid fa-clock text-base"></i>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300" x-text="slotsError"></p>
                        </div>

                        <!-- Busy slots -->
                        <div x-show="busySlots.length > 0" x-cloak class="mb-3">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-lock text-[9px]"></i>
                                Занятые окна
                            </div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <template x-for="busy in busySlots" :key="busy.time">
                                    <div class="py-2 px-3 rounded-lg border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-center cursor-default">
                                        <div class="text-sm font-semibold text-slate-400 dark:text-slate-500 line-through" x-text="busy.time"></div>
                                        <div class="text-[10px] font-medium text-slate-500 dark:text-slate-400 truncate" x-text="busy.service_name"></div>
                                        <div class="text-[9px] text-slate-400 dark:text-slate-500 truncate" x-text="busy.client_name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Free slots grid -->
                        <div x-show="slots.length > 0" x-cloak>
                            <div x-show="busySlots.length > 0" class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-check text-[9px]"></i>
                                Свободно
                            </div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <template x-for="slot in slots" :key="slot">
                                    <button type="button" @click="selectTime(slot)"
                                        class="py-2.5 px-3 rounded-lg border-2 text-sm font-medium transition-all duration-100 text-center"
                                        :class="time === slot
                                            ? 'border-indigo-500 bg-indigo-500 text-white shadow-sm'
                                            : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10'">
                                        <span x-text="slot"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        @error('time')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Summary + Submit -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 sticky top-24">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Создаваемая запись</h3>

                    <div class="space-y-2.5 mb-5">
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-7 h-7 rounded-md bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                <i class="fa-solid fa-scissors text-[10px]"></i>
                            </div>
                            <span x-text="selectedService?.name || 'Не выбрана'" class="text-slate-700 dark:text-slate-300 truncate"></span>
                        </div>
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-7 h-7 rounded-md bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                                <i class="fa-solid fa-user text-[10px]"></i>
                            </div>
                            <span x-text="selectedClient?.full_name || 'Не выбран'" class="text-slate-700 dark:text-slate-300 truncate"></span>
                        </div>
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-7 h-7 rounded-md bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center text-violet-600 dark:text-violet-400 shrink-0">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                            </div>
                            <span x-show="date && time" x-cloak>
                                <span class="text-slate-700 dark:text-slate-300" x-text="new Date(date + 'T' + time).toLocaleDateString('ru-RU', { day: 'numeric', month: 'long' }) + ', ' + time"></span>
                            </span>
                            <span x-show="!date || !time" x-cloak class="text-slate-400">Не выбрано</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-7 h-7 rounded-md bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 shrink-0">
                                <i class="fa-solid fa-user-tie text-[10px]"></i>
                            </div>
                            <span x-text="selectedMaster?.full_name || 'Любой мастер'" class="text-slate-700 dark:text-slate-300 truncate"></span>
                        </div>
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-7 h-7 rounded-md bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 shrink-0">
                                <i class="fa-solid fa-location-dot text-[10px]"></i>
                            </div>
                            <span x-text="selectedLocation?.name || 'Не выбрана'" class="text-slate-700 dark:text-slate-300 truncate"></span>
                        </div>
                    </div>

                    <button type="button" @click="submit"
                        :disabled="!canSubmit"
                        class="w-full py-3 text-sm font-bold text-white rounded-xl transition-all flex items-center justify-center gap-2"
                        :class="canSubmit
                            ? 'bg-indigo-600 hover:bg-indigo-700 shadow-sm'
                            : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed'">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Создать запись
                    </button>

                    <p x-show="!canSubmit" x-cloak class="text-[10px] text-slate-400 text-center mt-2">
                        Заполните услугу, дату, время и клиента
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

@endif

@endsection
