@extends('layouts.panel')

@section('title', 'Создание роли')

@push('breadcrumbs')
    <x-breadcrumbs :base="['title' => 'Главная', 'url' => route('panel.index')]" :items="[['title' => 'Роли и доступы', 'url' => null], ['title' => 'Роли', 'url' => route('panel.roles')], ['title' => 'Создать роль', 'url' => null]]" />
@endpush

@section('content')
    <div x-data="{
        searchPermission: '',
        selectedPermissions: {{ json_encode(old('permissions', [])) }},
        permissionDescriptions: {{ json_encode($permissions->pluck('description', 'name')->toArray()) }},
        collapsedCircuits: {},
        collapsedSubgroups: {},
        toggleCircuit(circuit) {
            this.collapsedCircuits = { ...this.collapsedCircuits, [circuit]: this.collapsedCircuits[circuit] === false };
        },
        isCircuitCollapsed(circuit) {
            return this.collapsedCircuits[circuit] === true;
        },
        toggleSubgroup(circuit, key) {
            const k = circuit + '.' + key;
            this.collapsedSubgroups = { ...this.collapsedSubgroups, [k]: this.collapsedSubgroups[k] === false };
        },
        isSubgroupCollapsed(circuit, key) {
            return this.collapsedSubgroups[circuit + '.' + key] !== false;
        },
        togglePermission(permissionName) {
            const index = this.selectedPermissions.indexOf(permissionName);
            if (index > -1) {
                this.selectedPermissions.splice(index, 1);
            } else {
                this.selectedPermissions.push(permissionName);
            }
            const checkbox = document.querySelector(`input[name='permissions[]'][value='${permissionName}']`);
            if (checkbox) checkbox.checked = !checkbox.checked;
        },
        getGroupPermissions(circuit, subgroup) {
            const all = {{ json_encode($permissions->pluck('name')->toArray()) }};
            return all.filter(p => {
                const parts = p.split('.');
                const c = parts[0] || '';
                const s = parts.length > 1 ? parts[1] : (parts[0] || 'other');
                return c === circuit && s === subgroup;
            });
        },
        isGroupFullySelected(circuit, subgroup) {
            const perms = this.getGroupPermissions(circuit, subgroup);
            return perms.length > 0 && perms.every(p => this.selectedPermissions.includes(p));
        },
        isGroupPartiallySelected(circuit, subgroup) {
            const perms = this.getGroupPermissions(circuit, subgroup);
            const sel = perms.filter(p => this.selectedPermissions.includes(p));
            return sel.length > 0 && sel.length < perms.length;
        },
        selectAllInSubgroup(circuit, subgroup) {
            const perms = this.getGroupPermissions(circuit, subgroup);
            const allSelected = perms.every(p => this.selectedPermissions.includes(p));
            perms.forEach(perm => {
                const checkbox = document.querySelector(`input[name='permissions[]'][value='${perm}']`);
                if (checkbox) {
                    if (allSelected) {
                        const i = this.selectedPermissions.indexOf(perm);
                        if (i > -1) this.selectedPermissions.splice(i, 1);
                        checkbox.checked = false;
                    } else {
                        if (!this.selectedPermissions.includes(perm)) this.selectedPermissions.push(perm);
                        checkbox.checked = true;
                    }
                }
            });
        },
        filteredPermissions() {
            if (!this.searchPermission || !this.searchPermission.trim()) {
                return {{ json_encode($permissions->pluck('name')->toArray()) }};
            }
            const search = this.searchPermission.toLowerCase().trim();
            const all = {{ json_encode($permissions->pluck('name')->toArray()) }};
            return all.filter(p => {
                if (p.toLowerCase().includes(search)) return true;
                const d = this.permissionDescriptions[p];
                return d && typeof d === 'string' && d.toLowerCase().includes(search);
            });
        },
        getPermissionGroups() {
            const circuits = { panel: [], client: [] };
            const order = ['panel', 'client'];
            this.filteredPermissions().forEach(p => {
                const parts = p.split('.');
                const c = parts[0] || 'other';
                if (order.includes(c)) circuits[c].push(p);
            });
            const result = [];
            const circuitLabels = { panel: 'Панель админа', client: 'Клиентский контур' };
            const subgroupLabels = {{ json_encode([
                'users' => 'Пользователи', 'roles' => 'Роли', 'permissions' => 'Права доступа',
                'businesses' => 'Бизнесы', 'appointments' => 'Записи', 'clients' => 'Клиенты',
                'services' => 'Услуги', 'locations' => 'Локации', 'masters' => 'Мастера',
                'analytics' => 'Аналитика', 'support' => 'Поддержка', 'broadcasts' => 'Рассылки',
                'tickets' => 'Тикеты', 'plans' => 'Тарифы', 'payments' => 'Платежи',
                'access' => 'Доступ', 'telegram' => 'Telegram', 'business' => 'Бизнес', 'subscription' => 'Подписка',
            ]) }};
            order.forEach(circuit => {
                const perms = circuits[circuit];
                if (perms.length === 0) return;
                const subgroups = {};
                perms.forEach(p => {
                    const parts = p.split('.');
                    const sub = parts.length > 1 ? parts[1] : (parts[0] || 'other');
                    if (!subgroups[sub]) subgroups[sub] = [];
                    subgroups[sub].push(p);
                });
                const subKeys = Object.keys(subgroups).sort();
                result.push({
                    circuit,
                    circuitLabel: circuitLabels[circuit] || circuit,
                    subgroups: subKeys.map(s => ({ key: s, label: subgroupLabels[s] || (s.charAt(0).toUpperCase() + s.slice(1)), permissions: subgroups[s] }))
                });
            });
            return result;
        }
    }" class="max-w-5xl mx-auto space-y-6">
        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-white text-base sm:text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Создание роли</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">Создайте новую роль и назначьте ей права доступа. Разделы: панель админа и клиентский контур.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('panel.roles.store') }}" class="space-y-6">
            @csrf

            <!-- Основная информация -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
                    Основная информация
                </h2>
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Название роли <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Например: moderator"
                           class="w-full px-3 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-colors" />
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Латиница и цифры, без пробелов</p>
                </div>
            </div>

            <!-- Права доступа -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 sm:mb-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-key text-indigo-600 dark:text-indigo-400"></i>
                        Права доступа
                    </h2>
                    <span class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                        Выбрано: <span class="font-bold text-slate-900 dark:text-white" x-text="selectedPermissions.length"></span> из {{ $permissions->count() }}
                    </span>
                </div>

                @error('permissions')
                    <p class="mb-4 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror

                <!-- Поиск -->
                <div class="mb-4 sm:mb-6">
                    <label for="permission-search" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Поиск прав доступа</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" id="permission-search" x-model="searchPermission"
                               placeholder="Поиск по названию или описанию права..."
                               class="w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors text-sm" />
                    </div>
                </div>

                <!-- Контуры → подгруппы (как в редактировании) -->
                <div class="space-y-6 sm:space-y-8" x-show="getPermissionGroups().length > 0">
                    <template x-for="circuitBlock in getPermissionGroups()" :key="circuitBlock.circuit">
                        <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <button type="button"
                                    @click="toggleCircuit(circuitBlock.circuit)"
                                    class="w-full px-4 sm:px-6 py-3 sm:py-4 flex items-center gap-3 text-left cursor-pointer hover:opacity-90 transition-opacity"
                                    :class="circuitBlock.circuit === 'panel' ? 'bg-indigo-50 dark:bg-indigo-500/10 border-b border-indigo-100 dark:border-indigo-500/20' : 'bg-emerald-50 dark:bg-emerald-500/10 border-b border-emerald-100 dark:border-emerald-500/20'">
                                <i class="fa-solid fa-chevron-down text-slate-400 dark:text-slate-500 text-xs transition-transform duration-200 flex-shrink-0"
                                   :class="{ '-rotate-90': isCircuitCollapsed(circuitBlock.circuit) }"></i>
                                <div class="h-9 w-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                     :class="circuitBlock.circuit === 'panel' ? 'bg-indigo-100 dark:bg-indigo-500/20' : 'bg-emerald-100 dark:bg-emerald-500/20'">
                                    <i class="fa-solid text-sm"
                                       :class="circuitBlock.circuit === 'panel' ? 'fa-display text-indigo-600 dark:text-indigo-400' : 'fa-store text-emerald-600 dark:text-emerald-400'"></i>
                                </div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex-1" x-text="circuitBlock.circuitLabel"></h2>
                                <span class="text-xs text-slate-500 dark:text-slate-400" x-text="isCircuitCollapsed(circuitBlock.circuit) ? 'развернуть' : 'свернуть'"></span>
                            </button>

                            <div class="divide-y divide-slate-200 dark:divide-slate-700"
                                 x-show="!isCircuitCollapsed(circuitBlock.circuit)"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1">
                                <template x-for="sub in circuitBlock.subgroups" :key="circuitBlock.circuit + '.' + sub.key">
                                    <div class="bg-white dark:bg-slate-900">
                                        <div class="bg-slate-50 dark:bg-slate-800/50 px-4 sm:px-5 py-2.5 sm:py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between flex-wrap gap-2">
                                            <button type="button"
                                                    @click="toggleSubgroup(circuitBlock.circuit, sub.key)"
                                                    class="flex items-center gap-2 text-left cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-lg px-1 -mx-1 py-1 transition-colors min-w-0 flex-1">
                                                <i class="fa-solid fa-chevron-down text-slate-400 dark:text-slate-500 text-xs transition-transform duration-200 flex-shrink-0"
                                                   :class="{ '-rotate-90': isSubgroupCollapsed(circuitBlock.circuit, sub.key) }"></i>
                                                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="sub.label"></h3>
                                                <span class="text-xs text-slate-500 dark:text-slate-400 flex-shrink-0" x-text="`(${sub.permissions.length})`"></span>
                                            </button>
                                            <button type="button"
                                                    @click.stop="selectAllInSubgroup(circuitBlock.circuit, sub.key)"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg transition-colors flex-shrink-0"
                                                    :class="isGroupFullySelected(circuitBlock.circuit, sub.key) 
                                                        ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600/30' 
                                                        : isGroupPartiallySelected(circuitBlock.circuit, sub.key)
                                                        ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-600/30'
                                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                                <i class="fa-solid" :class="isGroupFullySelected(circuitBlock.circuit, sub.key) ? 'fa-square-check' : 'fa-square'"></i>
                                                <span x-text="isGroupFullySelected(circuitBlock.circuit, sub.key) ? 'Снять все' : 'Выбрать все'"></span>
                                            </button>
                                        </div>

                                        <div class="p-4 sm:p-5"
                                             x-show="!isSubgroupCollapsed(circuitBlock.circuit, sub.key)"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                <template x-for="permission in sub.permissions" :key="permission">
                                                    <label class="flex items-start gap-3 p-3 sm:p-4 rounded-xl border transition-all duration-200 cursor-pointer group"
                                                           :class="selectedPermissions.includes(permission)
                                                               ? 'border-indigo-300 dark:border-indigo-600/50 bg-indigo-50 dark:bg-indigo-500/10 shadow-sm'
                                                               : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                                                        <input type="checkbox" name="permissions[]" :value="permission"
                                                               :checked="selectedPermissions.includes(permission)"
                                                               @change="togglePermission(permission)"
                                                               class="h-4 w-4 sm:h-5 sm:w-5 text-indigo-600 rounded border-slate-300 dark:border-slate-600 focus:ring-indigo-500 focus:ring-2 mt-0.5 cursor-pointer flex-shrink-0">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white break-words" x-text="permission"></p>
                                                            <template x-if="permissionDescriptions[permission]">
                                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="permissionDescriptions[permission]"></p>
                                                            </template>
                                                        </div>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="getPermissionGroups().length === 0" class="text-center py-12">
                    <div class="h-16 w-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white mb-2">Права не найдены</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Измените поисковый запрос</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('panel.roles') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium transition-colors">
                    Отмена
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-sm"></i>
                    Создать роль
                </button>
            </div>
        </form>
    </div>
@endsection
