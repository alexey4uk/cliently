@extends('layouts.panel')

@section('title', 'Создание роли')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-slate-500 dark:text-slate-400">
                <li>
                    <a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Главная
                    </a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li>
                    <a href="{{ route('panel.roles') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Роли
                    </a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="text-slate-900 dark:text-white font-medium">
                    Создание роли
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Создание роли</h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">Создайте новую роль и назначьте ей права доступа</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="POST" action="{{ route('panel.roles.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Role Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Название роли *
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        placeholder="Например: moderator"
                    />
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                        Используйте латинские буквы и цифры, без пробелов
                    </p>
                </div>

                <!-- Permissions -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                        Права доступа
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 mt-0.5"
                                    {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $permission->name }}</p>
                                    @if($permission->description)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $permission->description }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Создать роль
                    </button>
                    <a
                        href="{{ route('panel.roles') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium transition-colors">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
