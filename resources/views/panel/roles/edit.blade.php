@extends('layouts.panel')

@section('title', 'Редактирование роли')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Редактирование роли</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $role->name }}</p>
            </div>
            <a href="{{ route('panel.roles') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('panel.roles.update', $role) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Название -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Название</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $role->name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Права доступа -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Права доступа</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->name }}"
                                           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 mt-0.5">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $permission->name }}</p>
                                        @if($permission->description)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $permission->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-between pt-4">
                        <div class="flex items-center gap-3">
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fa-solid fa-save"></i>
                                <span>Сохранить</span>
                            </button>
                            <a href="{{ route('panel.roles') }}" 
                               class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors">
                                <span>Отмена</span>
                            </a>
                        </div>
                        @if(!in_array($role->name, ['admin', 'manager', 'support', 'user']))
                            <form method="POST" action="{{ route('panel.roles.destroy', $role) }}" 
                                  onsubmit="return confirm('Вы уверены, что хотите удалить эту роль?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Удалить</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
