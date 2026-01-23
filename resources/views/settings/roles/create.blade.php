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
            Укажите код роли и выберите права. Роль будет доступна всем бизнесам.
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
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Права роли <span class="text-rose-500">*</span>
                </label>
                @error('permissions')
                    <p class="mb-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <div class="border border-slate-200 dark:border-slate-800 rounded-lg max-h-[60vh] overflow-y-auto divide-y divide-slate-200 dark:divide-slate-800">
                    @php
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
                        $groupedPermissions = collect($allPermissions)
                            ->groupBy(fn($permission) => explode('.', $permission)[0])
                            ->sortKeys();
                    @endphp

                    @foreach($groupedPermissions as $groupName => $permissions)
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            {{ $resourceLabels[$groupName] ?? ucfirst($groupName) }}
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
