@extends('layouts.user')

@section('title', 'Создание записи - Cliently')
@section('page-title', 'Создание записи')
@section('page-description', 'Создание новой записи')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Записи', 'url' => route('appointments.index')],
        ['title' => 'Создание записи', 'url' => null],
    ]" />
@endpush

@section('content')

    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-4 md:space-y-6">
        @csrf

        <!-- Основная информация -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Основная информация
                    </h2>
                </div>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                    <div>
                        <label for="client_id"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                            Клиент <span class="text-rose-500">*</span>
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedClient: null,
                            dropdownPosition: { top: 0, left: 0, width: 0 },
                            clients: {{ json_encode($clients->map(function ($client) {
                                return [
                                    'id' => $client->id,
                                    'full_name' => $client->full_name,
                                    'phone' => $client->phone,
                                    'initials' => $client->initials,
                                ];
                            }), JSON_HEX_TAG) }},
                            oldClientId: {{ old('client_id', 0) }},
                            init() {
                                if (this.oldClientId) {
                                    const client = this.clients.find(c => c.id === this.oldClientId);
                                    if (client) {
                                        this.selectedClient = client;
                                    }
                                }
                            },
                            updatePosition() {
                                if (!this.open) return;
                                this.$nextTick(() => {
                                    const button = this.$el.querySelector('button');
                                    if (button) {
                                        const rect = button.getBoundingClientRect();
                                        this.dropdownPosition = {
                                            top: rect.bottom + 4,
                                            left: rect.left,
                                            width: rect.width
                                        };
                                    }
                                });
                            },
                            get filteredClients() {
                                if (!this.search) {
                                    return this.clients;
                                }
                                const query = this.search.toLowerCase();
                                return this.clients.filter(client =>
                                    client.full_name.toLowerCase().includes(query) ||
                                    client.phone.includes(query)
                                );
                            },
                            selectClient(client) {
                                this.selectedClient = client;
                                this.search = '';
                                this.open = false;
                            },
                            clearSelection() {
                                this.selectedClient = null;
                                this.search = '';
                            },
                            toggleOpen() {
                                this.open = !this.open;
                                if (this.open) {
                                    setTimeout(() => this.updatePosition(), 10);
                                }
                            }
                        }" x-init="$watch('open', () => updatePosition())" @resize.window="updatePosition()"
                            @scroll.window="updatePosition()" class="relative" @click.away="open = false">
                            <!-- Скрытый input для формы -->
                            <input type="hidden" name="client_id" :value="selectedClient ? selectedClient.id : ''"
                                required>

                            <!-- Поле выбора клиента -->
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fa-solid fa-user text-slate-400 text-sm"></i>
                                </div>
                                <button type="button" @click="toggleOpen()" :aria-expanded="open"
                                    :aria-controls="'client-dropdown'"
                                    class="w-full pl-9 sm:pl-10 pr-8 sm:pr-10 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('client_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <span x-show="selectedClient" x-cloak class="text-slate-900 dark:text-white">
                                        <span x-text="selectedClient ? selectedClient.full_name : ''"></span> (<span
                                            x-text="selectedClient ? selectedClient.phone : ''"></span>)
                                    </span>
                                    <span x-show="!selectedClient" x-cloak class="text-slate-400 dark:text-slate-500">
                                        Выберите клиента
                                    </span>
                                </button>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 sm:pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </div>
                            </div>

                            <!-- Выпадающий список -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                                :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                                style="display: none;" id="client-dropdown" role="listbox">
                                <!-- Поиск -->
                                <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                        </div>
                                        <input type="text" x-model="search" @click.stop placeholder="Поиск клиента..."
                                            class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                                    </div>
                                </div>

                                <!-- Список клиентов -->
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="filteredClients.length === 0">
                                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                            Клиенты не найдены
                                        </div>
                                    </template>
                                    <template x-for="client in filteredClients" :key="client.id">
                                        <button type="button" @click="selectClient(client)"
                                            role="option"
                                            :aria-selected="selectedClient && selectedClient.id === client.id"
                                            class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3 first:rounded-t-lg last:rounded-b-lg"
                                            :class="selectedClient && selectedClient.id === client.id ?
                                                'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate mb-0.5"
                                                    x-text="client.full_name"></div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate"
                                                    x-text="client.phone"></div>
                                            </div>
                                            <i x-show="selectedClient && selectedClient.id === client.id"
                                                class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm flex-shrink-0"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @error('client_id')
                            <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_id"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                            Услуга <span class="text-rose-500">*</span>
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedService: null,
                            dropdownPosition: { top: 0, left: 0, width: 0 },
                            services: {{ json_encode($services->map(function ($service) {
                                return [
                                    'id' => $service->id,
                                    'name' => $service->name,
                                    'price' => number_format($service->price, 0, ',', ' '),
                                    'duration' => $service->duration,
                                ];
                            }), JSON_HEX_TAG) }},
                            oldServiceId: {{ old('service_id', 0) }},
                            init() {
                                if (this.oldServiceId) {
                                    const service = this.services.find(s => s.id === this.oldServiceId);
                                    if (service) {
                                        this.selectedService = service;
                                    }
                                }
                            },
                            updatePosition() {
                                if (!this.open) return;
                                this.$nextTick(() => {
                                    const button = this.$el.querySelector('button');
                                    if (button) {
                                        const rect = button.getBoundingClientRect();
                                        this.dropdownPosition = {
                                            top: rect.bottom + 4,
                                            left: rect.left,
                                            width: rect.width
                                        };
                                    }
                                });
                            },
                            get filteredServices() {
                                if (!this.search) {
                                    return this.services;
                                }
                                const query = this.search.toLowerCase();
                                return this.services.filter(service =>
                                    service.name.toLowerCase().includes(query)
                                );
                            },
                            selectService(service) {
                                this.selectedService = service;
                                this.search = '';
                                this.open = false;
                                // Диспатчим событие изменения для скрипта загрузки слотов
                                const hiddenInput = document.getElementById('service_id');
                                if (hiddenInput) {
                                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            },
                            toggleOpen() {
                                this.open = !this.open;
                                if (this.open) {
                                    setTimeout(() => this.updatePosition(), 10);
                                }
                            }
                        }" x-init="$watch('open', () => updatePosition())" @resize.window="updatePosition()"
                            @scroll.window="updatePosition()" class="relative" @click.away="open = false">
                            <!-- Скрытый input для формы -->
                            <input type="hidden" id="service_id" name="service_id"
                                :value="selectedService ? selectedService.id : ''" required>

                            <!-- Поле выбора услуги -->
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fa-solid fa-scissors text-slate-400 text-sm"></i>
                                </div>
                                <button type="button" @click="toggleOpen()" :aria-expanded="open"
                                    :aria-controls="'service-dropdown'"
                                    class="w-full pl-9 sm:pl-10 pr-8 sm:pr-10 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('service_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <span x-show="selectedService" x-cloak class="text-slate-900 dark:text-white">
                                        <span x-text="selectedService ? selectedService.name : ''"></span> (<span
                                            x-text="selectedService ? selectedService.price : ''"></span> Br, <span
                                            x-text="selectedService ? selectedService.duration : ''"></span> мин)
                                    </span>
                                    <span x-show="!selectedService" x-cloak class="text-slate-400 dark:text-slate-500">
                                        Выберите услугу
                                    </span>
                                </button>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 sm:pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </div>
                            </div>

                            <!-- Выпадающий список -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                                :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                                style="display: none;" id="service-dropdown" role="listbox">
                                <!-- Поиск -->
                                <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                        </div>
                                        <input type="text" x-model="search" @click.stop placeholder="Поиск услуги..."
                                            class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                                    </div>
                                </div>

                                <!-- Список услуг -->
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="filteredServices.length === 0">
                                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                            Услуги не найдены
                                        </div>
                                    </template>
                                    <template x-for="service in filteredServices" :key="service.id">
                                        <button type="button" @click="selectService(service)"
                                            role="option"
                                            :aria-selected="selectedService && selectedService.id === service.id"
                                            class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3 first:rounded-t-lg last:rounded-b-lg"
                                            :class="selectedService && selectedService.id === service.id ?
                                                'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate mb-0.5"
                                                    x-text="service.name"></div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate"
                                                    x-text="`${service.price} Br • ${service.duration} мин`"></div>
                                            </div>
                                            <i x-show="selectedService && selectedService.id === service.id"
                                                class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm flex-shrink-0"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @error('service_id')
                            <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                    <div>
                        <label for="master_id"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                            Мастер
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedMaster: null,
                            dropdownPosition: { top: 0, left: 0, width: 0 },
                            masters: {{ json_encode($masters->map(function ($master) {
                                return [
                                    'id' => $master->id,
                                    'first_name' => $master->first_name,
                                    'last_name' => $master->last_name,
                                    'full_name' => trim($master->first_name . ' ' . ($master->last_name ?? '')),
                                ];
                            }), JSON_HEX_TAG) }},
                            oldMasterId: {{ old('master_id', 0) }},
                            init() {
                                if (this.oldMasterId) {
                                    const master = this.masters.find(m => m.id === this.oldMasterId);
                                    if (master) {
                                        this.selectedMaster = master;
                                    }
                                }
                            },
                            updatePosition() {
                                if (!this.open) return;
                                this.$nextTick(() => {
                                    const button = this.$el.querySelector('button');
                                    if (button) {
                                        const rect = button.getBoundingClientRect();
                                        this.dropdownPosition = {
                                            top: rect.bottom + 4,
                                            left: rect.left,
                                            width: rect.width
                                        };
                                    }
                                });
                            },
                            get filteredMasters() {
                                if (!this.search) {
                                    return this.masters;
                                }
                                const query = this.search.toLowerCase();
                                return this.masters.filter(master =>
                                    master.full_name.toLowerCase().includes(query) ||
                                    (master.first_name && master.first_name.toLowerCase().includes(query)) ||
                                    (master.last_name && master.last_name.toLowerCase().includes(query))
                                );
                            },
                            selectMaster(master) {
                                this.selectedMaster = master;
                                this.search = '';
                                this.open = false;
                                // Диспатчим событие изменения для скрипта загрузки слотов
                                const hiddenInput = document.getElementById('master_id');
                                if (hiddenInput) {
                                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            },
                            clearSelection() {
                                this.selectedMaster = null;
                                this.search = '';
                            },
                            toggleOpen() {
                                this.open = !this.open;
                                if (this.open) {
                                    setTimeout(() => this.updatePosition(), 10);
                                }
                            }
                        }" x-init="$watch('open', () => updatePosition())" @resize.window="updatePosition()"
                            @scroll.window="updatePosition()" class="relative" @click.away="open = false">
                            <!-- Скрытый input для формы -->
                            <input type="hidden" id="master_id" name="master_id"
                                :value="selectedMaster ? selectedMaster.id : ''">

                            <!-- Поле выбора мастера -->
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fa-solid fa-user-tie text-slate-400 text-sm"></i>
                                </div>
                                <button type="button" @click="toggleOpen()" :aria-expanded="open"
                                    :aria-controls="'master-dropdown'"
                                    class="w-full pl-9 sm:pl-10 pr-8 sm:pr-10 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('master_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <span x-show="selectedMaster" x-cloak class="text-slate-900 dark:text-white">
                                        <span x-text="selectedMaster ? selectedMaster.full_name : ''"></span>
                                    </span>
                                    <span x-show="!selectedMaster" x-cloak class="text-slate-400 dark:text-slate-500">
                                        Не выбран
                                    </span>
                                </button>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 sm:pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </div>
                            </div>

                            <!-- Выпадающий список -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                                :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                                style="display: none;" id="master-dropdown" role="listbox">
                                <!-- Поиск -->
                                <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                        </div>
                                        <input type="text" x-model="search" @click.stop placeholder="Поиск мастера..."
                                            class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                                    </div>
                                </div>

                                <!-- Список мастеров -->
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="filteredMasters.length === 0">
                                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                            Мастера не найдены
                                        </div>
                                    </template>
                                    <template x-for="master in filteredMasters" :key="master.id">
                                        <button type="button" @click="selectMaster(master)"
                                            role="option"
                                            :aria-selected="selectedMaster && selectedMaster.id === master.id"
                                            class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3 first:rounded-t-lg last:rounded-b-lg"
                                            :class="selectedMaster && selectedMaster.id === master.id ?
                                                'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate"
                                                    x-text="master.full_name"></div>
                                            </div>
                                            <i x-show="selectedMaster && selectedMaster.id === master.id"
                                                class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm flex-shrink-0"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @error('master_id')
                            <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="location_id"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                            Локация
                        </label>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedLocation: null,
                            dropdownPosition: { top: 0, left: 0, width: 0 },
                            locations: {{ json_encode($locations->map(function ($location) {
                                return [
                                    'id' => $location->id,
                                    'name' => $location->name,
                                    'full_address' => $location->full_address,
                                    'phone' => $location->phone,
                                ];
                            }), JSON_HEX_TAG) }},
                            oldLocationId: {{ old('location_id', 0) }},
                            init() {
                                if (this.oldLocationId) {
                                    const location = this.locations.find(l => l.id === this.oldLocationId);
                                    if (location) {
                                        this.selectedLocation = location;
                                    }
                                }
                            },
                            updatePosition() {
                                if (!this.open) return;
                                this.$nextTick(() => {
                                    const button = this.$el.querySelector('button');
                                    if (button) {
                                        const rect = button.getBoundingClientRect();
                                        this.dropdownPosition = {
                                            top: rect.bottom + 4,
                                            left: rect.left,
                                            width: rect.width
                                        };
                                    }
                                });
                            },
                            get filteredLocations() {
                                if (!this.search) {
                                    return this.locations;
                                }
                                const query = this.search.toLowerCase();
                                return this.locations.filter(location =>
                                    location.name.toLowerCase().includes(query) ||
                                    (location.full_address && location.full_address.toLowerCase().includes(query)) ||
                                    (location.phone && location.phone.includes(query))
                                );
                            },
                            selectLocation(location) {
                                this.selectedLocation = location;
                                this.search = '';
                                this.open = false;
                                // Диспатчим событие изменения для скрипта загрузки слотов
                                const hiddenInput = document.getElementById('location_id');
                                if (hiddenInput) {
                                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            },
                            clearSelection() {
                                this.selectedLocation = null;
                                this.search = '';
                                // Диспатчим событие изменения для скрипта загрузки слотов
                                const hiddenInput = document.getElementById('location_id');
                                if (hiddenInput) {
                                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            },
                            toggleOpen() {
                                this.open = !this.open;
                                if (this.open) {
                                    setTimeout(() => this.updatePosition(), 10);
                                }
                            }
                        }" x-init="$watch('open', () => updatePosition())" @resize.window="updatePosition()"
                            @scroll.window="updatePosition()" class="relative" @click.away="open = false">
                            <!-- Скрытый input для формы -->
                            <input type="hidden" id="location_id" name="location_id"
                                :value="selectedLocation ? selectedLocation.id : ''">

                            <!-- Поле выбора локации -->
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-sm"></i>
                                </div>
                                <button type="button" @click="toggleOpen()" :aria-expanded="open"
                                    :aria-controls="'location-dropdown'"
                                    class="w-full pl-9 sm:pl-10 pr-8 sm:pr-10 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('location_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <span x-show="selectedLocation" x-cloak class="text-slate-900 dark:text-white">
                                        <span x-text="selectedLocation ? selectedLocation.name : ''"></span>
                                        <span x-show="selectedLocation && selectedLocation.full_address" x-cloak
                                            class="text-slate-500 dark:text-slate-400 text-xs ml-1">
                                            (<span
                                                x-text="selectedLocation ? (selectedLocation.full_address || '') : ''"></span>)
                                        </span>
                                    </span>
                                    <span x-show="!selectedLocation" x-cloak class="text-slate-400 dark:text-slate-500">
                                        Не выбрана
                                    </span>
                                </button>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 sm:pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </div>
                            </div>

                            <!-- Выпадающий список -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                                :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                                style="display: none;" id="location-dropdown" role="listbox">
                                <!-- Поиск -->
                                <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                        </div>
                                        <input type="text" x-model="search" @click.stop placeholder="Поиск локации..."
                                            class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                                    </div>
                                </div>

                                <!-- Список локаций -->
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="filteredLocations.length === 0">
                                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                            Локации не найдены
                                        </div>
                                    </template>
                                    <template x-for="location in filteredLocations" :key="location.id">
                                        <button type="button" @click="selectLocation(location)"
                                            role="option"
                                            :aria-selected="selectedLocation && selectedLocation.id === location.id"
                                            class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3 first:rounded-t-lg last:rounded-b-lg"
                                            :class="selectedLocation && selectedLocation.id === location.id ?
                                                'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate mb-0.5"
                                                    x-text="location.name"></div>
                                                <div x-show="location.full_address"
                                                    class="text-xs text-slate-500 dark:text-slate-400 truncate"
                                                    x-text="location.full_address"></div>
                                            </div>
                                            <i x-show="selectedLocation && selectedLocation.id === location.id"
                                                class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm flex-shrink-0"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @error('location_id')
                            <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Дата и время -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Дата и время
                    </h2>
                </div>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                    <div>
                        <label for="date"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                            Дата <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-calendar text-slate-400 text-sm"></i>
                            </div>
                            <input type="date" id="date" name="date" required
                                value="{{ old('date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('date') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        </div>
                        @error('date')
                            <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="time"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                            Время <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-clock text-slate-400 text-sm"></i>
                            </div>
                            <select id="time" name="time" required
                                class="w-full pl-9 sm:pl-10 pr-8 sm:pr-10 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('time') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors appearance-none cursor-pointer">
                                <option value="">{{ old('time') ? old('time') : 'Сначала выберите услугу и дату' }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 sm:pr-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                            </div>
                        </div>
                        <div id="time-loading"
                            class="hidden mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                            <span>Загрузка доступных слотов...</span>
                        </div>
                        <div id="time-error"
                            class="hidden mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span></span>
                        </div>
                        @error('time')
                            <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div
            class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Дополнительная информация
                    </h2>
                </div>
            </div>
            <div class="p-4 md:p-6">
                <div>
                    <label for="notes"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Заметки
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Дополнительная информация о записи...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div
            class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('appointments.index') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Отмена</span>
            </a>
            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
                <i class="fa-solid fa-check text-xs"></i>
                <span>Создать запись</span>
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const serviceSelect = document.getElementById('service_id');
                const masterSelect = document.getElementById('master_id');
                const dateInput = document.getElementById('date');
                const timeSelect = document.getElementById('time');
                const timeLoading = document.getElementById('time-loading');
                const timeError = document.getElementById('time-error');
                const locationSelect = document.getElementById('location_id');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (!serviceSelect || !dateInput || !timeSelect || !timeLoading || !timeError) {
                    console.error('Не найдены необходимые элементы для загрузки слотов');
                    return;
                }

                let currentOldTime = {!! json_encode(old('time', ''), JSON_HEX_TAG) !!};

                function loadAvailableSlots() {
                    const serviceId = serviceSelect ? serviceSelect.value : '';
                    const date = dateInput ? dateInput.value : '';
                    const masterId = masterSelect ? (masterSelect.value || null) : null;
                    const locationId = locationSelect ? (locationSelect.value || null) : null;

                    // Очищаем предыдущие опции
                    timeSelect.innerHTML = '<option value="">Загрузка...</option>';
                    timeSelect.disabled = true;
                    timeLoading.classList.remove('hidden');
                    timeError.classList.add('hidden');

                    if (!serviceId || !date) {
                        timeSelect.innerHTML = '<option value="">Сначала выберите услугу и дату</option>';
                        timeSelect.disabled = false;
                        timeLoading.classList.add('hidden');
                        return;
                    }

                    // Формируем URL для API (используем публичный роут)
                    const url = '{{ route('api.public.appointments.available-slots', $business->slug) }}';
                    const params = new URLSearchParams({
                        service_id: serviceId,
                        date: date,
                    });

                    if (masterId) {
                        params.append('master_id', masterId);
                    }

                    if (locationId) {
                        params.append('location_id', locationId);
                    }

                    // Выполняем AJAX запрос
                    fetch(`${url}?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || ''
                            },
                            credentials: 'same-origin',
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errData => {
                                    throw new Error(errData.message ||
                                        `Ошибка ${response.status}: ${response.statusText}`);
                                }).catch(() => {
                                    throw new Error(`Ошибка ${response.status}: ${response.statusText}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            timeLoading.classList.add('hidden');
                            timeSelect.disabled = false;

                            if (data.success && data.slots && data.slots.length > 0) {
                                timeSelect.innerHTML = '<option value="">Выберите время</option>';
                                data.slots.forEach(slot => {
                                    const option = document.createElement('option');
                                    option.value = slot;
                                    option.textContent = slot;
                                    if (currentOldTime === slot) {
                                        option.selected = true;
                                    }
                                    timeSelect.appendChild(option);
                                });
                                timeError.classList.add('hidden');
                            } else {
                                timeSelect.innerHTML = '<option value="">Нет доступных слотов</option>';

                                // Более информативное сообщение об ошибке
                                let errorMessage = data.message ||
                                    'На выбранную дату нет доступных временных слотов.';

                                // Проверяем, сегодня ли это
                                const today = new Date().toISOString().split('T')[0];
                                const isToday = date === today;

                                if (!data.message) {
                                    if (isToday) {
                                        errorMessage =
                                            'На сегодня нет доступных слотов. Пожалуйста, выберите другую дату.';
                                    } else {
                                        errorMessage =
                                            'На выбранную дату нет доступных временных слотов. Пожалуйста, выберите другую дату или мастера.';
                                    }
                                }

                                timeError.textContent = errorMessage;
                                timeError.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка при загрузке слотов:', error);
                            timeLoading.classList.add('hidden');
                            timeSelect.disabled = false;
                            timeSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                            const errorMessage = error.message ||
                                'Произошла ошибка при загрузке доступных слотов. Пожалуйста, обновите страницу.';
                            timeError.textContent = errorMessage;
                            timeError.classList.remove('hidden');
                        });
                }

                // Обработчики событий
                if (serviceSelect) {
                    serviceSelect.addEventListener('change', loadAvailableSlots);
                }
                if (masterSelect) {
                    masterSelect.addEventListener('change', loadAvailableSlots);
                }
                if (dateInput) {
                    dateInput.addEventListener('change', loadAvailableSlots);
                }
                if (locationSelect) {
                    locationSelect.addEventListener('change', loadAvailableSlots);
                }

                // Загружаем слоты при загрузке страницы, если уже выбраны услуга и дата
                if (serviceSelect && serviceSelect.value && dateInput && dateInput.value) {
                    loadAvailableSlots();
                }
            });
        </script>
    @endpush

@endsection