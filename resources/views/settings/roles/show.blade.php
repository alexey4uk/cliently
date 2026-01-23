@extends('layouts.user')

@section('title', 'Права роли - Cliently')
@section('page-title', 'Права роли: ' . ($role === 'owner' ? 'Владелец' : ($role === 'admin' ? 'Администратор' : 'Мастер')))
@section('page-description', 'Настройка прав доступа')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Права ролей', 'url' => route('settings.roles.index')], ['title' => ucfirst($role), 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    Права роли: 
                    <span class="
                        @if($role === 'owner') text-amber-600 dark:text-amber-400
                        @elseif($role === 'admin') text-indigo-600 dark:text-indigo-400
                        @else text-purple-600 dark:text-purple-400
                        @endif">
                        @if($role === 'owner') Владелец
                        @elseif($role === 'admin') Администратор
                        @else Мастер
                        @endif
                    </span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Настройте права доступа для этой роли в вашем бизнесе
                </p>
            </div>
            <a href="{{ route('settings.roles.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i>
                <span>Назад</span>
            </a>
        </div>
    </div>

    <!-- Форма редактирования прав -->
    <form method="POST" action="{{ route('settings.roles.update', $role) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Базовые права -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-info-circle text-slate-500 dark:text-slate-400"></i>
                <span>Базовые права (устанавливаются системным администратором)</span>
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($defaultPermissions as $permission)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                        <i class="fa-solid fa-check text-xs"></i>
                        {{ $permission }}
                    </span>
                @endforeach
                @if(count($defaultPermissions) === 0)
                    <p class="text-sm text-slate-500 dark:text-slate-400">Нет базовых прав</p>
                @endif
            </div>
        </div>

        <!-- Текущие права (с переопределениями) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                Текущие права в вашем бизнесе
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Отметьте права, которые должны быть доступны для этой роли. Изменения применяются только к вашему бизнесу.
            </p>

            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($allPermissions as $permission)
                    @php
                        $isDefault = in_array($permission, $defaultPermissions);
                        $isCurrent = in_array($permission, $currentPermissions);
                        $hasOverride = isset($overrides[$permission]);
                        $overrideGranted = $hasOverride ? $overrides[$permission]->granted : null;
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission }}"
                               {{ $isCurrent ? 'checked' : '' }}
                               class="mt-1 w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $permission }}
                                </span>
                                @if($hasOverride)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                                        <i class="fa-solid fa-edit text-xs"></i>
                                        Переопределено
                                    </span>
                                @elseif($isDefault)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                                        <i class="fa-solid fa-check text-xs"></i>
                                        Базовое
                                    </span>
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex gap-3">
            <a href="{{ route('settings.roles.index') }}" 
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
