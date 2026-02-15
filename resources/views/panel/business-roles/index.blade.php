@extends('layouts.panel')

@section('title', 'Роли бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :base="['title' => 'Главная', 'url' => route('panel.index')]" :items="[['title' => 'Роли и доступы', 'url' => null], ['title' => 'Роли бизнеса', 'url' => null]]" />
@endpush

@section('content')
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

<div class="max-w-5xl mx-auto">
    <div class="space-y-6">
    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-user-shield text-white text-base sm:text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Роли бизнеса</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">Управление глобальными ролями и их правами. Применяется ко всем бизнесам.</p>
                </div>
            </div>
            <a href="{{ route('panel.business-roles.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Создать роль</span>
            </a>
        </div>
    </div>

    <!-- Таблица (десктоп) -->
    <div class="hidden md:block bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Роль</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Описание</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Права</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($roles as $item)
                    @php
                        $role = $item['role'];
                        $permissions = $item['permissions'];
                        $roleName = $role->name ?? ($role->slug ? ucfirst($role->slug) : '—');
                        $roleDescription = $role->description ?? 'Пользовательская роль с настраиваемыми правами';
                        $roleIcon = $roleIcons[$role->slug] ?? 'fa-user-gear';
                        $roleGradient = $roleGradients[$role->slug] ?? 'from-slate-500 to-slate-600';
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-br {{ $roleGradient }} flex items-center justify-center">
                                    <i class="fa-solid {{ $roleIcon }} text-white text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">{{ $roleName }}</div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">{{ $role->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $roleDescription }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 rounded-full">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                                {{ count($permissions ?? []) }} прав
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('panel.business-roles.show', $role->id) }}"
                               class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                                <span>Настроить</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Карточки (мобильные) -->
    <div class="md:hidden space-y-4">
        @foreach($roles as $item)
            @php
                $role = $item['role'];
                $permissions = $item['permissions'];
                $roleName = $role->name ?? ($role->slug ? ucfirst($role->slug) : '—');
                $roleDescription = $role->description ?? 'Пользовательская роль';
                $roleIcon = $roleIcons[$role->slug] ?? 'fa-user-gear';
                $roleGradient = $roleGradients[$role->slug] ?? 'from-slate-500 to-slate-600';
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br {{ $roleGradient }} flex items-center justify-center">
                        <i class="fa-solid {{ $roleIcon }} text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-slate-900 dark:text-white">{{ $roleName }}</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500">{{ $role->slug }}</div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 rounded-full">
                        {{ count($permissions ?? []) }} прав
                    </span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-3">{{ $roleDescription }}</p>
                <a href="{{ route('panel.business-roles.show', $role->id) }}"
                   class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                    <span>Настроить</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Подсказка -->
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">О ролях бизнеса</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    Роли и их права применяются ко всем бизнесам. Системные роли (Владелец, Администратор, Мастер) задаются по умолчанию; вы можете добавлять свои и настраивать права.
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
</div>
@endsection
