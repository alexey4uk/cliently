@extends('layouts.panel')

@section('title', 'Редактирование права')

@push('breadcrumbs')
    <x-breadcrumbs :base="['title' => 'Главная', 'url' => route('panel.index')]" :items="[['title' => 'Роли и доступы', 'url' => null], ['title' => 'Права доступа', 'url' => route('panel.permissions')], ['title' => 'Редактирование', 'url' => null]]" />
@endpush

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="space-y-6">
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-key text-white text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Редактирование права</h1>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $permission->name }}</span>
                            </p>
                            @if($permission->roles->count() > 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-600/30">
                                    <i class="fa-solid fa-shield-halved text-xs"></i>
                                    {{ $permission->roles->count() }} {{ $permission->roles->count() === 1 ? 'роль' : ($permission->roles->count() < 5 ? 'роли' : 'ролей') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('panel.permissions') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-medium transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs sm:text-sm"></i>
                    <span>Назад к списку</span>
                </a>
            </div>
        </div>

        <!-- Форма -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
            <form method="POST" action="{{ route('panel.permissions.update', $permission) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-4 sm:space-y-6">
                    <!-- Название -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">
                            Название права *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-tag text-slate-400 text-sm"></i>
                            </div>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $permission->name) }}"
                                   placeholder="Например: reports.view"
                                   class="w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 rounded-xl border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors text-sm shadow-sm"
                                   required>
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            Используйте латинские буквы, цифры и точку, без пробелов
                        </p>
                    </div>

                    <!-- Описание -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">
                            Описание права
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-3 sm:left-4 flex items-start pointer-events-none">
                                <i class="fa-solid fa-file-lines text-slate-400 text-sm mt-0.5"></i>
                            </div>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Описание права доступа на русском языке..."
                                      class="w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 rounded-xl border {{ $errors->has('description') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors text-sm shadow-sm resize-none">{{ old('description', $permission->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            Рекомендуется указать описание на русском языке для удобства поиска
                        </p>
                    </div>

                    <!-- Информация о ролях -->
                    @if($permission->roles->count() > 0)
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-indigo-600 dark:text-indigo-400"></i>
                                Роли с этим правом ({{ $permission->roles->count() }})
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($permission->roles as $role)
                                    <a href="{{ route('panel.roles.edit', $role) }}" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 rounded-lg border border-indigo-200 dark:border-indigo-600/30 hover:bg-indigo-200 dark:hover:bg-indigo-500/30 transition-colors">
                                        <i class="fa-solid fa-shield-halved text-xs"></i>
                                        {{ ucfirst($role->name) }}
                                    </a>
                                @endforeach
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">
                                Изменение названия права может повлиять на работу системы. Убедитесь, что все роли обновлены.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Кнопки действий -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 mt-6 sm:mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm sm:text-base font-semibold rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fa-solid fa-save text-sm"></i>
                            <span>Сохранить изменения</span>
                        </button>
                        <a href="{{ route('panel.permissions') }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-3 sm:py-3.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm sm:text-base font-semibold rounded-xl transition-colors shadow-sm">
                            <span>Отмена</span>
                        </a>
                    </div>
                    @can('panel.permissions.delete')
                        <form method="POST" action="{{ route('panel.permissions.destroy', $permission) }}" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить право {{ addslashes($permission->name) }}? Это действие нельзя отменить.');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-3 sm:py-3.5 bg-rose-600 hover:bg-rose-700 text-white text-sm sm:text-base font-semibold rounded-xl transition-colors shadow-sm hover:shadow-md w-full sm:w-auto">
                                <i class="fa-solid fa-trash text-sm"></i>
                                <span>Удалить право</span>
                            </button>
                        </form>
                    @endcan
                </div>
            </form>
        </div>
        </div>
    </div>
@endsection
