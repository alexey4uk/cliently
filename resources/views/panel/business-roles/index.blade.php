@extends('layouts.panel')

@section('title', 'Базовые права ролей - Cliently')

@section('content')
<div class="space-y-6">
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Роли бизнеса</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Управление глобальными ролями и их правами</p>
        </div>
        <a href="{{ route('panel.business-roles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Создать роль</span>
        </a>
    </div>

    <!-- Список ролей -->
    @php
        $roleLabels = [
            'owner' => 'Владелец',
            'admin' => 'Администратор',
            'master' => 'Мастер',
        ];
        $roleDescriptions = [
            'owner' => 'Полный доступ ко всем функциям бизнеса',
            'admin' => 'Расширенный доступ к управлению бизнесом',
            'master' => 'Ограниченный доступ для работы с клиентами',
        ];
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
    <div class="grid md:grid-cols-3 gap-6">
        @foreach($roles as $item)
            @php
                $role = $item['role'];
                $permissions = $item['permissions'];
                $roleName = $role->name ?? ($roleLabels[$role->slug] ?? ucfirst($role->slug));
                $roleDescription = $role->description ?? ($roleDescriptions[$role->slug] ?? 'Пользовательская роль с базовыми правами');
                $roleIcon = $roleIcons[$role->slug] ?? 'fa-user-gear';
                $roleGradient = $roleGradients[$role->slug] ?? 'from-slate-500 to-slate-600';
            @endphp
            <a href="{{ route('panel.business-roles.show', $role->id) }}" 
               class="group bg-white dark:bg-slate-900 rounded-xl border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-600 shadow-sm hover:shadow-md p-6 transition-all">
                <div class="h-16 w-16 rounded-xl 
                    bg-linear-to-br {{ $roleGradient }} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $roleIcon }} text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $roleName }}
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    {{ $roleDescription }}
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        {{ count($permissions ?? []) }} прав
                    </span>
                    <i class="fa-solid fa-arrow-right text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Информационная подсказка -->
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                    О базовых правах
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Роли и их права применяются ко всем бизнесам в системе. Изменения доступны только системным администраторам.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
