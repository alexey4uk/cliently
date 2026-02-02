@extends('layouts.user')

@section('title', 'Права ролей - Cliently')
@section('page-title', 'Права ролей')
@section('page-description', 'Управление правами для ролей в бизнесе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Права ролей', 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Строка: заголовок + действие -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <h1 class="text-lg font-semibold text-slate-900 dark:text-white">Права ролей</h1>
        <div class="flex items-center gap-2 shrink-0">
            @if($canManageRoles)
            <a href="{{ route('settings.roles.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Создать роль</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Список ролей -->
    @php
        $roleIcons = [
            'owner' => 'fa-crown',
            'admin' => 'fa-user-shield',
            'master' => 'fa-user',
        ];
        $roleGradients = [
            'owner' => 'from-amber-500 to-amber-600',
            'admin' => 'from-indigo-500 to-indigo-600',
            'master' => 'from-purple-500 to-purple-600',
        ];
    @endphp
    <div class="hidden md:block bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Роль</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Описание</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Права</th>
                    @if($hasAnyRoleAction)
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($roles as $item)
                    @php
                        $role = $item['role'];
                        $permissions = $item['permissions'];
                        $roleName = $role->name ?? ucfirst($role->slug);
                        $roleDescription = $role->description ?? 'Пользовательская роль с настраиваемыми правами';
                        $roleIcon = $roleIcons[$role->slug] ?? 'fa-user-gear';
                        $roleGradient = $roleGradients[$role->slug] ?? 'from-slate-500 to-slate-600';
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-linear-to-br {{ $roleGradient }} flex items-center justify-center">
                                    <i class="fa-solid {{ $roleIcon }} text-white text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                        {{ $roleName }}
                                    </div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">
                                        {{ $role->slug }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $roleDescription }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 rounded-full">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                                {{ count($permissions ?? []) }} прав
                            </span>
                        </td>
                        @if($hasAnyRoleAction)
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('settings.roles.show', $role->id) }}"
                                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                                    <span>Настроить</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="md:hidden space-y-4">
                @foreach($roles as $item)
            @php
                        $role = $item['role'];
                        $permissions = $item['permissions'];
                        $roleName = $role->name ?? ucfirst($role->slug);
                        $roleDescription = $role->description ?? 'Пользовательская роль с настраиваемыми правами';
                        $roleIcon = $roleIcons[$role->slug] ?? 'fa-user-gear';
                        $roleGradient = $roleGradients[$role->slug] ?? 'from-slate-500 to-slate-600';
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-linear-to-br {{ $roleGradient }} flex items-center justify-center">
                        <i class="fa-solid {{ $roleIcon }} text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $roleName }}
                        </div>
                        <div class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $role->slug }}
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 rounded-full">
                        {{ count($permissions ?? []) }} прав
                    </span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-3">
                    {{ $roleDescription }}
                </p>
                @if($hasAnyRoleAction)
                    <a href="{{ route('settings.roles.show', $role->id) }}"
                       class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        <span>Настроить</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Информационная подсказка -->
    <div class="mt-8 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                    О правах ролей
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    Права ролей задаются системным администратором и применяются ко всем бизнесам.
                </p>
                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Изменения применяются ко всем бизнесам</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Права ролей можно обновлять в любой момент</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
