@extends('layouts.panel')

@section('title', 'Права доступа')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Права доступа</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление правами доступа</p>
            </div>
            <a href="{{ route('panel.permissions.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                Создать право
            </a>
        </div>

        <!-- Список прав -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Описание</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Роли</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($permissions as $permission)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $permission->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $permission->description ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($permission->roles->take(5) as $role)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                        @if($permission->roles->count() > 5)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full">
                                                +{{ $permission->roles->count() - 5 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('panel.permissions.destroy', $permission) }}" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить это право?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 text-sm font-medium">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($permissions->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-slate-500 dark:text-slate-400">Права не найдены</p>
                </div>
            @endif
        </div>

        <!-- Пагинация -->
        @if($permissions->hasPages())
            <div class="flex justify-center">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
@endsection
