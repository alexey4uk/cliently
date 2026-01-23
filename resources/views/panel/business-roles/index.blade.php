@extends('layouts.panel')

@section('title', 'Базовые права ролей - Cliently')

@section('content')
<div class="space-y-6">
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Базовые права ролей</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Управление базовыми правами для ролей бизнеса</p>
        </div>
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
        @foreach($roles as $roleKey => $permissions)
            @php
                $roleName = $roleLabels[$roleKey] ?? ucfirst($roleKey);
                $roleDescription = $roleDescriptions[$roleKey] ?? 'Пользовательская роль с базовыми правами';
                $roleIcon = $roleIcons[$roleKey] ?? 'fa-user-gear';
                $roleGradient = $roleGradients[$roleKey] ?? 'from-slate-500 to-slate-600';
            @endphp
            <a href="{{ route('panel.business-roles.show', $roleKey) }}" 
               class="group bg-white dark:bg-slate-900 rounded-xl border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-600 shadow-sm hover:shadow-md p-6 transition-all">
                <div class="h-16 w-16 rounded-xl 
                    bg-gradient-to-br {{ $roleGradient }} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
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
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                    О базовых правах
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Базовые права применяются ко всем бизнесам по умолчанию. Владельцы бизнесов могут переопределить эти права для своих бизнесов.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
