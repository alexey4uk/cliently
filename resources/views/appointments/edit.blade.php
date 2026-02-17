@extends('layouts.user')

@section('title', 'Редактирование записи - Cliently')
@section('page-title', 'Редактирование записи')
@section('page-description', 'Изменение данных записи')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Записи', 'url' => route('appointments.index')],
        ['title' => 'Редактирование', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Редактировать запись</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    Запись #{{ $appointment->id }} от {{ $appointment->date->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full
                {{ $appointment->status === 'completed' ? 'text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-600' : '' }}
                {{ $appointment->status === 'cancelled' ? 'text-rose-700 bg-rose-100 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-600' : '' }}
                {{ $appointment->status === 'confirmed' ? 'text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200 dark:border-blue-600' : '' }}
                {{ $appointment->status === 'pending' ? 'text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-600' : '' }}">
                @if($appointment->status === 'completed')
                    <i class="fa-solid fa-check-circle text-xs"></i>
                    Завершено
                @elseif($appointment->status === 'cancelled')
                    <i class="fa-solid fa-xmark-circle text-xs"></i>
                    Отменено
                @elseif($appointment->status === 'confirmed')
                    <i class="fa-solid fa-circle-check text-xs"></i>
                    Подтверждено
                @else
                    <i class="fa-solid fa-clock text-xs"></i>
                    Ожидает подтверждения
                @endif
            </span>
        </div>
    </div>

    <!-- Form (не вкладывать вторую form внутрь — иначе браузер закрывает первую и «Сохранить» отправляет удаление) -->
    <form id="appointment-edit-form" method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-6">
        @csrf
        @method('PATCH')
        <input type="hidden" name="client_id" value="{{ old('client_id', $appointment->client_id) }}">

        <!-- Client Info (Read Only) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Клиент</h2>
            @if($appointment->client && !$appointment->client->trashed())
            <div class="flex items-center p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($appointment->client->full_name) }}&background=6366f1&color=fff&size=48" 
                    class="w-12 h-12 rounded-full" 
                    alt="{{ $appointment->client->full_name }}">
                <div class="ml-4 flex-1">
                    <a href="{{ route('clients.show', $appointment->client) }}" 
                        class="text-sm font-medium text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        {{ $appointment->client->full_name }}
                    </a>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $appointment->client->phone }}
                        @if($appointment->client->email)
                            · {{ $appointment->client->email }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('clients.show', $appointment->client) }}" 
                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium transition-colors">
                    Профиль клиента
                </a>
            </div>
            @elseif($appointment->client && $appointment->client->trashed())
            <div class="flex items-center p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-amber-200 dark:border-amber-800">
                <div class="w-12 h-12 rounded-full bg-slate-300 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user-slash text-slate-500 dark:text-slate-400"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">
                        Клиент удалён
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-500">
                        {{ $appointment->client->full_name }} (ID: {{ $appointment->client->id }})
                    </p>
                </div>
            </div>
            @else
            <p class="text-slate-500 dark:text-slate-400 py-2">Клиент удалён</p>
            @endif
        </div>

        <!-- Service Selection -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Услуга</h2>
            
            <div>
                <label for="service_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Выберите услугу <span class="text-rose-500">*</span>
                </label>
                <div x-data="{
                    open: false,
                    search: '',
                    selectedService: null,
                    dropdownPosition: { top: 0, left: 0, width: 0 },
                    services: @js($services->map(function($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => number_format($service->price, 0, ',', ' '),
                            'duration' => $service->duration
                        ];
                    })),
                    oldServiceId: {{ old('service_id', $appointment->service_id) }},
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
                }" 
                x-init="$watch('open', () => updatePosition())"
                @resize.window="updatePosition()"
                @scroll.window="updatePosition()"
                class="relative" 
                @click.away="open = false">
                    <input type="hidden" id="service_id" name="service_id" :value="selectedService ? selectedService.id : ''" required>
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <i class="fa-solid fa-scissors text-slate-400"></i>
                        </div>
                        <button type="button"
                                @click="toggleOpen()"
                                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border {{ $errors->has('service_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <span x-show="selectedService" x-cloak class="text-slate-900 dark:text-white">
                                <span x-text="selectedService ? selectedService.name : ''"></span> - <span x-text="selectedService ? selectedService.price : ''"></span> BYN (<span x-text="selectedService ? selectedService.duration : ''"></span> мин)
                            </span>
                            <span x-show="!selectedService" x-cloak class="text-slate-400 dark:text-slate-500">
                                Выберите услугу
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </div>
                    
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                         :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                         style="display: none;">
                        <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                </div>
                                <input type="text"
                                       x-model="search"
                                       @click.stop
                                       placeholder="Поиск услуги..."
                                       class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                            </div>
                        </div>
                        
                        <div class="max-h-80 overflow-y-auto">
                            <template x-if="filteredServices.length === 0">
                                <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Услуги не найдены
                                </div>
                            </template>
                            <template x-for="service in filteredServices" :key="service.id">
                                <button type="button"
                                        @click="selectService(service)"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3"
                                        :class="selectedService && selectedService.id === service.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white truncate mb-0.5" x-text="service.name"></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="`${service.price} BYN • ${service.duration} мин`"></div>
                                    </div>
                                    <i x-show="selectedService && selectedService.id === service.id" class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm shrink-0"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                @error('service_id')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
        </div>

        <!-- Master & Time -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Мастер и время</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="master_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Мастер</label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedMaster: null,
                        dropdownPosition: { top: 0, left: 0, width: 0 },
                        masters: @js($masters->map(function($master) {
                            return [
                                'id' => $master->id,
                                'first_name' => $master->first_name,
                                'last_name' => $master->last_name,
                                'full_name' => trim($master->first_name . ' ' . ($master->last_name ?? ''))
                            ];
                        })),
                        oldMasterId: {{ old('master_id', $appointment->master_id ?? 0) }},
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
                            const hiddenInput = document.getElementById('master_id');
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
                    }" 
                    x-init="$watch('open', () => updatePosition())"
                    @resize.window="updatePosition()"
                    @scroll.window="updatePosition()"
                    class="relative" 
                    @click.away="open = false">
                        <input type="hidden" id="master_id" name="master_id" :value="selectedMaster ? selectedMaster.id : ''">
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <i class="fa-solid fa-user-tie text-slate-400"></i>
                            </div>
                            <button type="button"
                                    @click="toggleOpen()"
                                    class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border {{ $errors->has('master_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <span x-show="selectedMaster" x-cloak class="text-slate-900 dark:text-white">
                                    <span x-text="selectedMaster ? selectedMaster.full_name : ''"></span>
                                </span>
                                <span x-show="!selectedMaster" x-cloak class="text-slate-400 dark:text-slate-500">
                                    Не выбран
                                </span>
                            </button>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </div>
                        </div>
                        
                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                             :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                             style="display: none;">
                            <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                    </div>
                                    <input type="text"
                                           x-model="search"
                                           @click.stop
                                           placeholder="Поиск мастера..."
                                           class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                                </div>
                            </div>
                            
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="filteredMasters.length === 0">
                                    <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                        Мастера не найдены
                                    </div>
                                </template>
                                <template x-for="master in filteredMasters" :key="master.id">
                                    <button type="button"
                                            @click="selectMaster(master)"
                                            class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3"
                                            :class="selectedMaster && selectedMaster.id === master.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="master.full_name"></div>
                                        </div>
                                        <i x-show="selectedMaster && selectedMaster.id === master.id" class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm shrink-0"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    @error('master_id')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="location_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Локация</label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedLocation: null,
                        dropdownPosition: { top: 0, left: 0, width: 0 },
                        locations: @js($locations->map(function($location) {
                            return [
                                'id' => $location->id,
                                'name' => $location->name,
                                'full_address' => $location->full_address,
                                'phone' => $location->phone
                            ];
                        })),
                        oldLocationId: {{ old('location_id', $appointment->location_id ?? 0) }},
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
                    }" 
                    x-init="$watch('open', () => updatePosition())"
                    @resize.window="updatePosition()"
                    @scroll.window="updatePosition()"
                    class="relative" 
                    @click.away="open = false">
                        <input type="hidden" id="location_id" name="location_id" :value="selectedLocation ? selectedLocation.id : ''">
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                            </div>
                            <button type="button"
                                    @click="toggleOpen()"
                                    class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border {{ $errors->has('location_id') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <span x-show="selectedLocation" x-cloak class="text-slate-900 dark:text-white">
                                    <span x-text="selectedLocation ? selectedLocation.name : ''"></span>
                                </span>
                                <span x-show="!selectedLocation" x-cloak class="text-slate-400 dark:text-slate-500">
                                    Не выбрана
                                </span>
                            </button>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </div>
                        </div>
                        
                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="fixed z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden"
                             :style="`top: ${dropdownPosition.top}px; left: ${dropdownPosition.left}px; width: ${dropdownPosition.width}px;`"
                             style="display: none;">
                            <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                                    </div>
                                    <input type="text"
                                           x-model="search"
                                           @click.stop
                                           placeholder="Поиск локации..."
                                           class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                                </div>
                            </div>
                            
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="filteredLocations.length === 0">
                                    <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                        Локации не найдены
                                    </div>
                                </template>
                                <template x-for="location in filteredLocations" :key="location.id">
                                    <button type="button"
                                            @click="selectLocation(location)"
                                            class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between gap-3"
                                            :class="selectedLocation && selectedLocation.id === location.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white truncate mb-0.5" x-text="location.name"></div>
                                            <div x-show="location.full_address" class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="location.full_address"></div>
                                        </div>
                                        <i x-show="selectedLocation && selectedLocation.id === location.id" class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 text-sm shrink-0"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    @error('location_id')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Дата <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                        </div>
                        <input type="date" id="date" name="date" required 
                               value="{{ old('date', $appointment->date->format('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border {{ $errors->has('date') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    @error('date')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="time" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Время <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-clock text-slate-400"></i>
                        </div>
                        <select id="time" name="time" required
                                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border {{ $errors->has('time') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors appearance-none cursor-pointer">
                            @php
                                $currentTime = old('time', $appointment->time);
                                if ($currentTime && strpos($currentTime, ':') !== false) {
                                    $timeParts = explode(':', $currentTime);
                                    if (count($timeParts) >= 2) {
                                        $currentTime = $timeParts[0] . ':' . $timeParts[1];
                                    }
                                }
                            @endphp
                            <option value="">{{ $currentTime ?: 'Загрузка...' }}</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                    <div id="time-loading" class="hidden mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                        <span>Загрузка доступных слотов...</span>
                    </div>
                    <div id="time-error" class="hidden mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span></span>
                    </div>
                    @error('time')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status & Notes -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Статус и заметки</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Статус</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-info-circle text-slate-400"></i>
                        </div>
                        <select id="status" name="status"
                                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border {{ $errors->has('status') ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors appearance-none cursor-pointer">
                            <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>Ожидает подтверждения</option>
                            <option value="confirmed" {{ old('status', $appointment->status) === 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                            <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>Завершено</option>
                            <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Отменено</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Заметки</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                              placeholder="Любые заметки к записи...">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-4 mt-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('appointments.index') }}" 
               class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit" form="appointment-edit-form" 
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                Сохранить изменения
            </button>
        </div>
    </div>
</div>

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

    if (!serviceSelect || !dateInput || !timeSelect || !timeLoading || !timeError) {
        console.error('Не найдены необходимые элементы для загрузки слотов');
        return;
    }

    let currentOldTime = '{{ old("time", $appointment->time) }}';
    if (currentOldTime && currentOldTime.includes(':')) {
        const timeParts = currentOldTime.split(':');
        if (timeParts.length === 3) {
            currentOldTime = timeParts[0] + ':' + timeParts[1];
        }
    }
    const appointmentId = {{ $appointment->id }};

    function loadAvailableSlots() {
        const serviceId = serviceSelect ? serviceSelect.value : '';
        const date = dateInput ? dateInput.value : '';
        const masterId = masterSelect ? (masterSelect.value || null) : null;
        const locationId = locationSelect ? (locationSelect.value || null) : null;

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

        const url = '{{ route("api.public.appointments.available-slots", $business->slug) }}';
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

        if (appointmentId) {
            params.append('appointment_id', appointmentId);
        }

        fetch(`${url}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => {
                    throw new Error(errData.message || `Ошибка ${response.status}: ${response.statusText}`);
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
                let currentTimeFound = false;
                
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    if (currentOldTime && currentOldTime === slot) {
                        option.selected = true;
                        currentTimeFound = true;
                    }
                    timeSelect.appendChild(option);
                });
                
                if (currentOldTime && !currentTimeFound) {
                    const currentOption = document.createElement('option');
                    currentOption.value = currentOldTime;
                    currentOption.textContent = currentOldTime + ' (текущее)';
                    currentOption.selected = true;
                    timeSelect.insertBefore(currentOption, timeSelect.children[1]);
                }
                timeError.classList.add('hidden');
            } else {
                if (currentOldTime) {
                    timeSelect.innerHTML = `<option value="${currentOldTime}" selected>${currentOldTime} (текущее)</option>`;
                } else {
                    timeSelect.innerHTML = '<option value="">Нет доступных слотов</option>';
                }
                
                let errorMessage = data.message || 'На выбранную дату нет доступных временных слотов.';
                
                const today = new Date().toISOString().split('T')[0];
                const isToday = date === today;
                
                if (!data.message) {
                    if (isToday) {
                        errorMessage = 'На сегодня нет доступных слотов. Пожалуйста, выберите другую дату.';
                    } else {
                        errorMessage = 'На выбранную дату нет доступных временных слотов. Пожалуйста, выберите другую дату или мастера.';
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
            if (currentOldTime) {
                timeSelect.innerHTML = `<option value="${currentOldTime}" selected>${currentOldTime} (текущее)</option>`;
            } else {
                timeSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
            }
            const errorMessage = error.message || 'Произошла ошибка при загрузке доступных слотов. Пожалуйста, обновите страницу.';
            timeError.textContent = errorMessage;
            timeError.classList.remove('hidden');
        });
    }

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

    // Скрытые поля service_id, location_id, master_id заполняются Alpine после init.
    // Пробуем загрузить слоты: сразу, с задержкой и по появлению значения в service_id.
    var initialLoadDone = false;
    function tryLoadSlotsWhenReady() {
        if (initialLoadDone) return;
        if (serviceSelect.value && dateInput.value) {
            initialLoadDone = true;
            loadAvailableSlots();
            return true;
        }
        return false;
    }
    if (tryLoadSlotsWhenReady()) {
        return;
    }
    setTimeout(function() {
        if (tryLoadSlotsWhenReady()) return;
        setTimeout(function() {
            tryLoadSlotsWhenReady();
        }, 250);
    }, 100);
    // На случай, если Alpine обновит hidden позже: следим за появлением value у service_id
    if (serviceSelect && dateInput && dateInput.value) {
        var observer = new MutationObserver(function() {
            if (tryLoadSlotsWhenReady() && observer) {
                observer.disconnect();
            }
        });
        observer.observe(serviceSelect, { attributes: true, attributeFilter: ['value'] });
        // Alpine может менять .value без смены атрибута — проверяем периодически первые 2 сек
        var checkCount = 0;
        var intervalId = setInterval(function() {
            checkCount++;
            if (tryLoadSlotsWhenReady() || checkCount > 20) {
                clearInterval(intervalId);
                if (observer) observer.disconnect();
            }
        }, 100);
    }
});
</script>
@endpush

@endsection
