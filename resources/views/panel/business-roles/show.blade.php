@extends('layouts.panel')

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

@section('title', 'Базовые права роли: ' . $roleLabel . ' - Cliently')

@section('content')
<div class="space-y-6">
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                Базовые права роли:
                <span class="{{ $roleTitleClass }}">
                    {{ $roleLabel }}
                </span>
            </h2>
            <p class="text-slate-600 dark:text-slate-400">
                Настройте базовые права доступа для этой роли. Эти права будут применяться ко всем бизнесам по умолчанию.
            </p>
        </div>
        <a href="{{ route('panel.business-roles.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            <span>Назад</span>
        </a>
    </div>

    <!-- Форма редактирования прав -->
    <form method="POST" action="{{ route('panel.business-roles.update', $role) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Текущие базовые права -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                Базовые права
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Отметьте права, которые должны быть доступны для этой роли по умолчанию во всех бизнесах.
            </p>

            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($allPermissions as $permission)
                    @php
                        $isDefault = in_array($permission, $defaultPermissions);
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission }}"
                               {{ $isDefault ? 'checked' : '' }}
                               class="mt-1 w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $permission }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex gap-3">
            <a href="{{ route('panel.business-roles.index') }}" 
               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit" 
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <span>Сохранить изменения</span>
                <i class="fa-solid fa-check text-sm"></i>
            </button>
        </div>
    </form>
</div>
@endsection
