@extends('layouts.panel')

@section('title', 'Создать роль бизнеса - Cliently')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Создать роль</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Роль будет доступна во всех бизнесах</p>
        </div>
        <a href="{{ route('panel.business-roles.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            <span>Назад</span>
        </a>
    </div>

    <form method="POST" action="{{ route('panel.business-roles.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
            <div>
                <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Код роли <span class="text-rose-500">*</span>
                </label>
                <input id="slug"
                       name="slug"
                       type="text"
                       value="{{ old('slug') }}"
                       placeholder="например: manager"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('slug') ? 'border-rose-500' : 'border-slate-300' }} dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('slug')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
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
                       value="{{ old('description') }}"
                       placeholder="например: Управляет клиентами и записями"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('description') ? 'border-rose-500' : 'border-slate-300' }} dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Права роли</h3>
            @error('permissions')
                <p class="mb-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($allPermissions as $permission)
                    <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission }}"
                               {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
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

        <div class="flex gap-3">
            <a href="{{ route('panel.business-roles.index') }}" 
               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit" 
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <span>Создать роль</span>
                <i class="fa-solid fa-check text-sm"></i>
            </button>
        </div>
    </form>
</div>
@endsection
