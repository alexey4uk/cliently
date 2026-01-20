@extends('layouts.panel')

@section('title', 'Роли')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Роли</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление ролями и правами доступа</p>
            </div>
            <a href="{{ route('panel.roles.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                Создать роль
            </a>
        </div>

        <!-- Список ролей -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Права</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Пользователей</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($roles as $role)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $role->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(5) as $permission)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 5)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full">
                                                +{{ $role->permissions->count() - 5 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $role->users->count() }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('panel.roles.edit', $role) }}" 
                                           class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                            Редактировать
                                        </a>
                                        @if(!in_array($role->name, ['admin', 'manager', 'support', 'user']))
                                            <form method="POST" action="{{ route('panel.roles.destroy', $role) }}" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить эту роль?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 text-sm font-medium">
                                                    Удалить
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($roles->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-slate-500 dark:text-slate-400">Роли не найдены</p>
                </div>
            @endif
        </div>

        <!-- Пагинация -->
        @if($roles->hasPages())
            <div class="flex justify-center">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
@endsection
