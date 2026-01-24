@extends('layouts.user')

@section('title', 'Создать роль - Cliently')
@section('page-title', 'Создать роль')
@section('page-description', 'Создание новой роли с правами')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Права ролей', 'url' => route('settings.roles.index')], ['title' => 'Создать роль', 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-0">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2">Новая роль</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Укажите код и название роли и выберите права. Роль будет доступна всем бизнесам.
        </p>
    </div>

    <form method="POST" action="{{ route('settings.roles.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6 space-y-4">
            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Код роли <span class="text-rose-500">*</span>
                </label>
                <input id="role"
                       name="role"
                       type="text"
                       value="{{ old('role') }}"
                       placeholder="например: support_manager"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role') border-rose-500 @enderror">
                @error('role')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                    Только латиница, цифры и подчёркивания. Начинайте с буквы.
                </p>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Название роли <span class="text-rose-500">*</span>
                </label>
                <input id="name"
                       name="name"
                       type="text"
                       value="{{ old('name') }}"
                       placeholder="например: Менеджер"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Описание
                </label>
                <input id="description"
                       name="description"
                       type="text"
                       value="{{ old('description') }}"
                       placeholder="например: Управляет клиентами и записями"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description') border-rose-500 @enderror">
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Права роли <span class="text-rose-500">*</span>
                </label>
                @error('permissions')
                    <p class="mb-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <div class="border border-slate-200 dark:border-slate-800 rounded-lg max-h-[60vh] overflow-y-auto divide-y divide-slate-200 dark:divide-slate-800">
                    @php
                        // Получаем названия групп из описаний прав в БД
                        $groupLabels = [];
                        $permissionDescriptions = $permissionDescriptions ?? [];
                        
                        // Fallback на базовые названия групп
                        $fallbackLabels = [
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
                        
                        // Сначала собираем все группы из всех прав
                        $allGroups = [];
                        foreach ($allPermissions as $permission) {
                            $parts = explode('.', $permission);
                            if (count($parts) > 1) {
                                $groupName = $parts[1];
                                if (!in_array($groupName, $allGroups)) {
                                    $allGroups[] = $groupName;
                                }
                            }
                        }
                        
                        // Для каждой группы пытаемся получить название из описания любого права этой группы
                        foreach ($allGroups as $groupName) {
                            $groupLabel = null;
                            
                            // Ищем любое право этой группы в БД (предпочитаем .view, потом .create)
                            $preferredActions = ['view', 'create', 'manage', 'update'];
                            
                            foreach ($preferredActions as $action) {
                                $permissionName = 'client.' . $groupName . '.' . $action;
                                if (isset($permissionDescriptions[$permissionName]) && $permissionDescriptions[$permissionName]) {
                                    $description = $permissionDescriptions[$permissionName];
                                    
                                    // Если описание содержит двоеточие - берем часть до двоеточия как название группы
                                    if (strpos($description, ':') !== false) {
                                        $descParts = explode(':', $description);
                                        $groupLabel = trim($descParts[0]);
                                        break;
                                    }
                                    // Если двоеточия нет - используем fallback на название группы из имени права
                                    // Не используем описание целиком, так как оно может быть "Просмотр аналитики"
                                }
                            }
                            
                            // Если не нашли в описаниях с двоеточием, ищем в БД напрямую
                            if (!$groupLabel) {
                                $firstPermission = \Spatie\Permission\Models\Permission::where('name', 'like', 'client.' . $groupName . '.%')
                                    ->whereNotNull('description')
                                    ->orderByRaw("CASE WHEN name LIKE '%.view' THEN 1 WHEN name LIKE '%.create' THEN 2 ELSE 3 END")
                                    ->first();
                                
                                if ($firstPermission && $firstPermission->description) {
                                    // Проверяем, есть ли двоеточие в описании
                                    if (strpos($firstPermission->description, ':') !== false) {
                                        $descParts = explode(':', $firstPermission->description);
                                        $groupLabel = trim($descParts[0]);
                                    }
                                    // Если двоеточия нет - не используем описание, используем fallback
                                }
                            }
                            
                            // Используем fallback на базовые названия (из имени права или из массива)
                            if (!$groupLabel) {
                                $groupLabel = $fallbackLabels[$groupName] ?? ucfirst($groupName);
                            }
                            
                            $groupLabels[$groupName] = $groupLabel;
                        }
                        
                        // Функция для получения названия права из БД или форматирования
                        $formatPermissionLabel = function (string $permission) use ($permissionDescriptions, $groupLabels): string {
                            $parts = explode('.', $permission);
                            $isOwn = end($parts) === 'own';
                            $isWildcard = end($parts) === '*';
                            
                            // Для wildcard прав: используем название группы + "Все действия"
                            if ($isWildcard) {
                                $resource = count($parts) > 1 ? $parts[1] : ($parts[0] ?? $permission);
                                $resourceLabel = $groupLabels[$resource] ?? ucfirst($resource);
                                return $resourceLabel . ': Все действия';
                            }
                            
                            // Если есть описание в БД, используем его
                            if (isset($permissionDescriptions[$permission]) && $permissionDescriptions[$permission]) {
                                $label = $permissionDescriptions[$permission];
                                if ($isOwn) {
                                    $label .= ' (только свои)';
                                }
                                return $label;
                            }
                            
                            // Fallback: форматируем из имени права
                            if ($isOwn) {
                                array_pop($parts);
                            }
                            
                            $resource = count($parts) > 1 ? $parts[1] : ($parts[0] ?? $permission);
                            $action = count($parts) > 2 ? $parts[2] : (count($parts) > 1 ? $parts[1] : null);
                            
                            // Используем название группы из $groupLabels, если есть
                            $resourceLabel = $groupLabels[$resource] ?? ucfirst($resource);
                            
                            // Переводы действий
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
                            ];
                            
                            $actionLabel = $action ? ($actionLabels[$action] ?? ucfirst($action)) : null;
                            
                            $label = $resourceLabel;
                            if ($actionLabel) {
                                $label .= ': ' . $actionLabel;
                            }
                            if ($isOwn) {
                                $label .= ' (только свои)';
                            }
                            
                            return $label;
                        };
                        
                        $groupedPermissions = collect($allPermissions)
                            ->groupBy(function($permission) {
                                $parts = explode('.', $permission);
                                // Для прав вида client.xxx.yyy группируем по xxx (второй элемент)
                                // Для прав вида panel.xxx.yyy группируем по xxx (второй элемент)
                                return count($parts) > 1 ? $parts[1] : $parts[0];
                            })
                            ->sortKeys();
                    @endphp

                    @foreach($groupedPermissions as $groupName => $permissions)
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            {{ $groupLabels[$groupName] ?? ucfirst($groupName) }}
                        </div>
                        @foreach($permissions as $permission)
                            <label class="flex items-start gap-3 px-4 py-3 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $permission }}"
                                       {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                       class="mt-1 w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $formatPermissionLabel($permission) }}
                                    </div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500 break-all sm:break-normal">
                                        {{ $permission }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('settings.roles.index') }}"
               class="w-full sm:flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <span>Создать роль</span>
                <i class="fa-solid fa-check text-sm"></i>
            </button>
        </div>
    </form>
</div>

@endsection
