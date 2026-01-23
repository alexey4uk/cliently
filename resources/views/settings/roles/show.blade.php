@extends('layouts.user')

@php
    $roleLabels = [
        'owner' => 'Владелец',
        'admin' => 'Администратор',
        'master' => 'Мастер',
    ];
    $roleLabel = $roleLabels[$role] ?? ucfirst($role);
    $roleTitleClass = match ($role) {
        'owner' => 'text-amber-600 dark:text-amber-400',
        'admin' => 'text-indigo-600 dark:text-indigo-400',
        'master' => 'text-purple-600 dark:text-purple-400',
        default => 'text-slate-600 dark:text-slate-300',
    };
@endphp

@section('title', 'Права роли - Cliently')
@section('page-title', 'Права роли: ' . $roleLabel)
@section('page-description', 'Настройка прав доступа')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Права ролей', 'url' => route('settings.roles.index')], ['title' => $roleLabel, 'url' => null]]" />
@endpush

@section('content')

@php
    $totalCount = count($allPermissions);
    $selectedCount = count($currentPermissions);
    $deniedCount = count($deniedPermissions ?? []);
    $ownCount = count(array_filter($allPermissions, fn($p) => str_ends_with($p, '.own')));
    $overrideCount = $overrides->count();
    $groupedPermissions = collect($allPermissions)
        ->groupBy(fn($permission) => explode('.', $permission)[0])
        ->sortKeys();
    $roleIcon = match ($role) {
        'owner' => 'fa-crown',
        'admin' => 'fa-user-shield',
        'master' => 'fa-user',
        default => 'fa-user-gear',
    };
    $roleBadge = match ($role) {
        'owner' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        'admin' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300',
        'master' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    $resourceLabels = [
        'clients' => 'Клиенты',
        'appointments' => 'Записи',
        'services' => 'Услуги',
        'locations' => 'Локации',
        'masters' => 'Мастера',
        'businesses' => 'Бизнес',
        'business' => 'Бизнес',
        'analytics' => 'Аналитика',
        'telegram' => 'Телеграм',
        'tickets' => 'Тикеты',
        'subscription' => 'Подписка',
    ];
    $actionLabels = [
        'view' => 'Просмотр',
        'create' => 'Создание',
        'update' => 'Редактирование',
        'delete' => 'Удаление',
        'export' => 'Экспорт',
        'manage' => 'Управление',
        'confirm' => 'Подтвердить',
        'cancel' => 'Отменить',
        'complete' => 'Завершить',
        'assign' => 'Назначить',
        'status' => 'Статус',
        'settings' => 'Настройки',
        '*' => 'Все действия',
    ];
    $formatPermissionLabel = function (string $permission) use ($resourceLabels, $actionLabels): string {
        $parts = explode('.', $permission);
        $resource = $parts[0] ?? $permission;
        $action = $parts[1] ?? null;
        $suffix = $parts[2] ?? null;

        $resourceLabel = $resourceLabels[$resource] ?? ucfirst($resource);
        $actionLabel = $action ? ($actionLabels[$action] ?? ucfirst($action)) : null;

        $label = $resourceLabel;
        if ($actionLabel) {
            $label .= ': ' . $actionLabel;
        }
        if ($suffix === 'own') {
            $label .= ' (только свои)';
        }

        return $label;
    };
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-0 space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <i class="fa-solid {{ $roleIcon }} text-slate-700 dark:text-slate-200 text-lg"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $roleLabel }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $roleBadge }}">
                            <span class="uppercase">{{ $role }}</span>
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Настройте доступы роли для вашего бизнеса. Изменения затронут только текущий бизнес.
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <a href="{{ route('settings.roles.index') }}" 
                   class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                    <span>К списку</span>
                </a>
                @if($role !== 'owner')
                    <form method="POST" action="{{ route('settings.roles.destroy', $role) }}" class="w-full sm:w-auto"
                          onsubmit="return confirm('Удалить роль {{ $roleLabel }}? Это действие нельзя отменить.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-trash text-sm"></i>
                            <span>Удалить роль</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-6">
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">Всего прав</p>
                <p class="text-lg font-semibold text-slate-900 dark:text-white" data-summary-total>{{ $totalCount }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">Выбрано</p>
                <p class="text-lg font-semibold text-slate-900 dark:text-white" data-summary-selected>{{ $selectedCount }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">Запрещено</p>
                <p class="text-lg font-semibold text-slate-900 dark:text-white" data-summary-denied>{{ $deniedCount }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">Переопределено</p>
                <p class="text-lg font-semibold text-slate-900 dark:text-white" data-summary-overrides>{{ $overrideCount }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">Только свои</p>
                <p class="text-lg font-semibold text-slate-900 dark:text-white" data-summary-own>{{ $ownCount }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.roles.update', $role) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <details class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-6">
            <summary class="flex items-center justify-between cursor-pointer list-none">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-slate-500 dark:text-slate-400"></i>
                    <span class="text-lg font-semibold text-slate-900 dark:text-white">
                        Базовые права (справочно)
                    </span>
                </div>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    {{ count($defaultPermissions) }} прав
                </span>
            </summary>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-3">
                Эти права устанавливает администратор системы. На этой странице вы задаёте только переопределения.
            </p>
            <div class="flex flex-wrap gap-2 mt-4">
                @foreach($defaultPermissions as $permission)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                        <i class="fa-solid fa-check text-xs"></i>
                        {{ $formatPermissionLabel($permission) }}
                        <span class="text-[10px] text-emerald-600/80 dark:text-emerald-200/80">
                            ({{ $permission }})
                        </span>
                    </span>
                @endforeach
                @if(count($defaultPermissions) === 0)
                    <p class="text-sm text-slate-500 dark:text-slate-400">Нет базовых прав</p>
                @endif
            </div>
        </details>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 space-y-4 order-2 lg:order-1">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
                    <label for="permissionSearch" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Поиск права
                    </label>
                    <div class="relative">
                        <input id="permissionSearch" type="text" placeholder="например: appointments.view"
                               class="w-full pl-10 pr-10 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <button type="button" id="clearPermissionSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Фильтры</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" data-permission-filter="all"
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-indigo-600 text-white whitespace-nowrap">
                            Все
                        </button>
                        <button type="button" data-permission-filter="selected"
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            Выбранные
                        </button>
                        <button type="button" data-permission-filter="overrides"
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            Переопределения
                        </button>
                        <button type="button" data-permission-filter="denied"
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            Запрещенные
                        </button>
                        <button type="button" data-permission-filter="own"
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            Только свои
                        </button>
                        <button type="button" data-permission-filter="default"
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            Базовые
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Разделы</p>
                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        @foreach($groupedPermissions as $groupName => $permissions)
                            <a href="#group-{{ $groupName }}"
                               data-group-nav="{{ $groupName }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                <span>{{ $resourceLabels[$groupName] ?? ucfirst($groupName) }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    <span data-group-nav-count="{{ $groupName }}">{{ $permissions->count() }}</span>
                                    • <span data-group-nav-selected="{{ $groupName }}">0</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-4">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-3">Легенда</p>
                    <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                <i class="fa-solid fa-check text-xs"></i> Базовое
                            </span>
                            <span>установлено системой</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                <i class="fa-solid fa-edit text-xs"></i> Переопределено
                            </span>
                            <span>изменено для бизнеса</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                <i class="fa-solid fa-user-lock text-xs"></i> Только свои
                            </span>
                            <span>ограничение на свои данные</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300">
                                <i class="fa-solid fa-ban text-xs"></i> Явно запрещено
                            </span>
                            <span>приоритет над wildcard</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-4 order-1 lg:order-2" id="permissionsList">
                @foreach($groupedPermissions as $groupName => $permissions)
                    <details id="group-{{ $groupName }}"
                             class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900"
                             data-permission-group="{{ $groupName }}" open>
                        <summary class="flex items-center justify-between px-4 py-3 cursor-pointer list-none">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $resourceLabels[$groupName] ?? ucfirst($groupName) }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400" data-group-summary="{{ $groupName }}">
                                    {{ $permissions->count() }} прав
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                раскрыть/свернуть
                            </span>
                        </summary>
                        <div class="border-t border-slate-200 dark:border-slate-800 divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($permissions as $permission)
                                @php
                                    $isDefault = in_array($permission, $defaultPermissions) || 
                                        (str_ends_with($permission, '.own') && in_array(str_replace('.own', '', $permission), $defaultPermissions));
                                    $isCurrent = in_array($permission, $currentPermissions);
                                    $hasOverride = isset($overrides[$permission]);
                                    $overrideGranted = $hasOverride ? $overrides[$permission]->granted : null;
                                    $isOwnPermission = str_ends_with($permission, '.own');
                                    $isDenied = in_array($permission, $deniedPermissions ?? []) || ($hasOverride && !$overrideGranted);
                                @endphp
                                <label class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                                       data-permission-row
                                       data-permission="{{ strtolower($permission) }}"
                                       data-group="{{ $groupName }}"
                                       data-selected="{{ $isCurrent ? '1' : '0' }}"
                                       data-denied="{{ $isDenied ? '1' : '0' }}"
                                       data-own="{{ $isOwnPermission ? '1' : '0' }}"
                                       data-override="{{ $hasOverride ? '1' : '0' }}"
                                       data-default="{{ $isDefault ? '1' : '0' }}">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission }}"
                                           {{ $isCurrent ? 'checked' : '' }}
                                           class="mt-1 w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 permission-checkbox">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-medium text-slate-900 dark:text-white {{ $isDenied ? 'line-through text-slate-500' : '' }}">
                                                {{ $formatPermissionLabel($permission) }}
                                            </span>
                                            <span class="text-xs text-slate-400 dark:text-slate-500 break-all sm:break-normal">
                                                {{ $permission }}
                                            </span>
                                            @if($isDenied)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-red-700 bg-red-100 dark:bg-red-500/20 dark:text-red-300 rounded-full">
                                                    <i class="fa-solid fa-ban text-xs"></i>
                                                    Явно запрещено
                                                </span>
                                            @elseif($isOwnPermission)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300 rounded-full">
                                                    <i class="fa-solid fa-user-lock text-xs"></i>
                                                    Только свои данные
                                                </span>
                                            @endif
                                            @if($hasOverride && !$isDenied)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                                                    <i class="fa-solid fa-edit text-xs"></i>
                                                    Переопределено
                                                </span>
                                            @elseif($isDefault && !$isDenied)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                    Базовое
                                                </span>
                                            @endif
                                        </div>
                                        @if($isOwnPermission)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                Пользователь будет видеть только свои данные (например, только свои записи или только своих клиентов)
                                            </p>
                                        @elseif($isDenied)
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                                Это право явно запрещено и имеет приоритет над wildcard правами. Отметьте чекбокс, чтобы разрешить доступ.
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('settings.roles.index') }}" 
               class="w-full sm:flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit" 
                class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <span>Сохранить изменения</span>
                <i class="fa-solid fa-check text-sm"></i>
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('permissionSearch');
        const clearSearch = document.getElementById('clearPermissionSearch');
        const filterButtons = document.querySelectorAll('[data-permission-filter]');
        const rows = Array.from(document.querySelectorAll('[data-permission-row]'));
        const groupContainers = Array.from(document.querySelectorAll('[data-permission-group]'));
        const summaryTotal = document.querySelector('[data-summary-total]');
        const summarySelected = document.querySelector('[data-summary-selected]');
        const summaryDenied = document.querySelector('[data-summary-denied]');
        const summaryOverrides = document.querySelector('[data-summary-overrides]');
        const summaryOwn = document.querySelector('[data-summary-own]');
        const groupNavItems = Array.from(document.querySelectorAll('[data-group-nav]'));

        let activeFilter = 'all';

        const updateRowState = (row) => {
            const checkbox = row.querySelector('.permission-checkbox');
            row.dataset.selected = checkbox.checked ? '1' : '0';
        };

        const updateSummary = () => {
            const visibleRows = rows.filter(row => !row.classList.contains('hidden'));
            const selectedVisible = visibleRows.filter(row => row.dataset.selected === '1').length;
            const deniedVisible = visibleRows.filter(row => row.dataset.denied === '1').length;
            const overridesVisible = visibleRows.filter(row => row.dataset.override === '1').length;
            const ownVisible = visibleRows.filter(row => row.dataset.own === '1').length;

            summaryTotal.textContent = `${visibleRows.length}`;
            summarySelected.textContent = `${selectedVisible}`;
            summaryDenied.textContent = `${deniedVisible}`;
            if (summaryOverrides) summaryOverrides.textContent = `${overridesVisible}`;
            if (summaryOwn) summaryOwn.textContent = `${ownVisible}`;
        };

        const updateGroupSummaries = () => {
            groupContainers.forEach(group => {
                const groupName = group.dataset.permissionGroup;
                const groupRows = rows.filter(row => row.dataset.group === groupName && !row.classList.contains('hidden'));
                const summary = group.querySelector(`[data-group-summary="${groupName}"]`);
                if (summary) {
                    const selected = groupRows.filter(row => row.dataset.selected === '1').length;
                    summary.textContent = `${groupRows.length} прав • выбрано ${selected}`;
                }
                group.classList.toggle('hidden', groupRows.length === 0);
            });

            groupNavItems.forEach(link => {
                const groupName = link.dataset.groupNav;
                const groupRows = rows.filter(row => row.dataset.group === groupName && !row.classList.contains('hidden'));
                const selected = groupRows.filter(row => row.dataset.selected === '1').length;
                const countEl = link.querySelector(`[data-group-nav-count="${groupName}"]`);
                const selectedEl = link.querySelector(`[data-group-nav-selected="${groupName}"]`);
                if (countEl) countEl.textContent = `${groupRows.length}`;
                if (selectedEl) selectedEl.textContent = `${selected}`;
                link.classList.toggle('hidden', groupRows.length === 0);
            });
        };

        const matchesFilter = (row) => {
            switch (activeFilter) {
                case 'selected':
                    return row.dataset.selected === '1';
                case 'overrides':
                    return row.dataset.override === '1';
                case 'denied':
                    return row.dataset.denied === '1';
                case 'own':
                    return row.dataset.own === '1';
                case 'default':
                    return row.dataset.default === '1';
                default:
                    return true;
            }
        };

        const applyFilters = () => {
            const query = searchInput.value.trim().toLowerCase();
            rows.forEach(row => {
                const matchesQuery = row.dataset.permission.includes(query);
                const matches = matchesQuery && matchesFilter(row);
                row.classList.toggle('hidden', !matches);
            });
            updateSummary();
            updateGroupSummaries();
        };

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                activeFilter = button.dataset.permissionFilter;
                filterButtons.forEach(btn => {
                    btn.classList.toggle('bg-indigo-600', btn === button);
                    btn.classList.toggle('text-white', btn === button);
                    btn.classList.toggle('bg-slate-100', btn !== button);
                    btn.classList.toggle('dark:bg-slate-800', btn !== button);
                    btn.classList.toggle('text-slate-700', btn !== button);
                    btn.classList.toggle('dark:text-slate-300', btn !== button);
                });
                applyFilters();
            });
        });

        rows.forEach(row => {
            const checkbox = row.querySelector('.permission-checkbox');
            checkbox.addEventListener('change', () => {
                updateRowState(row);
                applyFilters();
            });
        });

        searchInput.addEventListener('input', applyFilters);
        clearSearch.addEventListener('click', () => {
            searchInput.value = '';
            applyFilters();
        });

        applyFilters();
    });
</script>
@endpush
