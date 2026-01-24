@extends('layouts.panel')

@php
    $roleLabels = [
        'owner' => 'Владелец',
        'admin' => 'Администратор',
        'master' => 'Мастер',
    ];
    $roleSlug = $role->slug;
    $roleLabel = $role->name ?? ($roleLabels[$roleSlug] ?? ucfirst($roleSlug));
    $roleTitleClass = match ($roleSlug) {
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
    <form method="POST" action="{{ route('panel.business-roles.update', $role->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Данные роли</h3>
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Название роли <span class="text-rose-500">*</span>
                </label>
                <input id="name"
                       name="name"
                       type="text"
                       value="{{ old('name', $role->name) }}"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-500' : 'border-slate-300' }} dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                       value="{{ old('description', $role->description) }}"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('description') ? 'border-rose-500' : 'border-slate-300' }} dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">
                Код роли: <span class="font-medium text-slate-700 dark:text-slate-200">{{ $role->slug }}</span>
            </div>
        </div>

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

    @if(!$role->is_system)
        <form method="POST" action="{{ route('panel.business-roles.destroy', $role->id) }}"
              onsubmit="return confirm('Удалить роль {{ $roleLabel }}? Это действие нельзя отменить.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                <i class="fa-solid fa-trash text-sm"></i>
                <span>Удалить роль</span>
            </button>
        </form>
    @endif
</div>
@endsection
