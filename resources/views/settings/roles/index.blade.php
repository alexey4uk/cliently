@extends('layouts.user')

@section('title', 'Права ролей - Cliently')
@section('page-title', 'Права ролей')
@section('page-description', 'Управление правами для ролей в бизнесе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Права ролей', 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Права ролей</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Настройте права доступа для ролей в вашем бизнесе. Вы можете переопределить базовые права.
            </p>
        </div>
    </div>

    <!-- Список ролей -->
    <div class="grid md:grid-cols-3 gap-6">
        @foreach(['owner' => 'Владелец', 'admin' => 'Администратор', 'master' => 'Мастер'] as $roleKey => $roleName)
            <a href="{{ route('settings.roles.show', $roleKey) }}" 
               class="group bg-white dark:bg-slate-900 rounded-xl border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-600 shadow-sm hover:shadow-md p-6 transition-all">
                <div class="h-16 w-16 rounded-xl 
                    @if($roleKey === 'owner') bg-gradient-to-br from-amber-500 to-amber-600
                    @elseif($roleKey === 'admin') bg-gradient-to-br from-indigo-500 to-indigo-600
                    @else bg-gradient-to-br from-purple-500 to-purple-600
                    @endif flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid 
                        @if($roleKey === 'owner') fa-crown
                        @elseif($roleKey === 'admin') fa-user-shield
                        @else fa-user
                        @endif text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $roleName }}
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    @if($roleKey === 'owner')
                        Полный доступ ко всем функциям бизнеса
                    @elseif($roleKey === 'admin')
                        Расширенный доступ к управлению бизнесом
                    @else
                        Ограниченный доступ для работы с клиентами
                    @endif
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        {{ count($roles[$roleKey] ?? []) }} прав
                    </span>
                    <i class="fa-solid fa-arrow-right text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Информационная подсказка -->
    <div class="mt-8 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                    О правах ролей
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    Базовые права для ролей устанавливаются системным администратором. Вы можете переопределить их для вашего бизнеса.
                </p>
                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Изменения применяются только к вашему бизнесу</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Базовые права можно восстановить в любой момент</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
