@extends('layouts.user')

@section('title', 'Пользователи бизнеса - Cliently')
@section('page-title', 'Пользователи бизнеса')
@section('page-description', 'Управление пользователями и их ролями')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Пользователи', 'url' => null]]" />
@endpush

@section('content')

@php
    $roleLabels = [
        'owner' => 'Владелец',
        'admin' => 'Администратор',
        'master' => 'Мастер',
    ];
    $roleBadgeClasses = [
        'owner' => 'text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300',
        'admin' => 'text-indigo-700 bg-indigo-100 dark:bg-indigo-500/20 dark:text-indigo-300',
        'master' => 'text-purple-700 bg-purple-100 dark:bg-purple-500/20 dark:text-purple-300',
    ];
    $roleIcons = [
        'owner' => 'fa-crown',
        'admin' => 'fa-user-shield',
        'master' => 'fa-user',
    ];
    $getRoleLabel = fn($role) => $roleLabels[$role] ?? ucfirst($role);
    $getRoleBadgeClass = fn($role) => $roleBadgeClasses[$role] ?? 'text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300';
    $getRoleIcon = fn($role) => $roleIcons[$role] ?? 'fa-user';
@endphp

<div x-data="{
    showDeleteModal: false,
    userToDelete: null,
    userName: '',
    openDeleteModal(userId, userName) {
        this.userToDelete = userId;
        this.userName = userName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.userToDelete = null;
        this.userName = '';
    },
    confirmDelete() {
        if (this.userToDelete) {
            const form = document.getElementById('delete-form-' + this.userToDelete);
            if (form) {
                form.submit();
            }
        }
    }
}">
    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Пользователи бизнеса</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управление пользователями и их ролями в бизнесе</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('settings.users.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить пользователя</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Список пользователей -->
    @if($users->count() > 0 || $invitations->count() > 0)
        <!-- Таблица для больших экранов -->
        <div class="hidden md:block">
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Роль</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($users as $user)
                            @php
                                $pivot = $business->users()->where('user_id', $user->id)->first();
                                $role = $pivot->pivot->role ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                     alt="{{ $user->name }}" 
                                                     class="w-full h-full rounded-full object-cover">
                                            @else
                                                {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($role)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $getRoleBadgeClass($role) }}">
                                            <i class="fa-solid {{ $getRoleIcon($role) }} text-xs"></i>
                                            {{ $getRoleLabel($role) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 rounded-full">
                                        <i class="fa-solid fa-check-circle text-xs"></i>
                                        Активен
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('settings.users.edit', $user) }}" 
                                            class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                            title="Редактировать">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @if($role !== 'owner' || $business->users()->wherePivot('role', 'owner')->count() > 1)
                                            <form method="POST" action="{{ route('settings.users.destroy', $user) }}"
                                                id="delete-form-{{ $user->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button"
                                                @click="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                                title="Удалить">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Приглашения -->
                        @foreach($invitations as $invitation)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors bg-amber-50/50 dark:bg-amber-900/10">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                                            <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $invitation->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $invitation->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $getRoleBadgeClass($invitation->role) }}">
                                        <i class="fa-solid {{ $getRoleIcon($invitation->role) }} text-xs"></i>
                                        {{ $getRoleLabel($invitation->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300 rounded-full">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                        Ожидает
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('settings.users.resend', $invitation) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                            class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                            title="Отправить повторно">
                                            <i class="fa-solid fa-paper-plane text-sm"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Карточки для мобильных -->
        <div class="md:hidden grid grid-cols-1 gap-4">
            @foreach($users as $user)
                @php
                    $pivot = $business->users()->where('user_id', $user->id)->first();
                    $role = $pivot->pivot->role ?? null;
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-full h-full rounded-full object-cover">
                                @else
                                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    {{ $user->name }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($role)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $getRoleBadgeClass($role) }}">
                                    {{ $getRoleLabel($role) }}
                                </span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('settings.users.edit', $user) }}"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <i class="fa-solid fa-pencil text-xs"></i>
                                <span>Редактировать</span>
                            </a>
                            @if($role !== 'owner' || $business->users()->wherePivot('role', 'owner')->count() > 1)
                                <form method="POST" action="{{ route('settings.users.destroy', $user) }}"
                                    id="delete-form-{{ $user->id }}" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button"
                                    @click="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-white dark:bg-slate-800 border border-rose-300 dark:border-rose-700/50 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Удалить</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($invitations as $invitation)
                <div class="bg-amber-50/50 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-12 w-12 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    {{ $invitation->email }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Ожидает принятия
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $getRoleBadgeClass($invitation->role) }}">
                                {{ $getRoleLabel($invitation->role) }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('settings.users.resend', $invitation) }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-700 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Отправить повторно</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Пустое состояние -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="max-w-sm mx-auto">
                <div class="h-16 w-16 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Пользователи не добавлены
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Начните работу, добавив пользователей в ваш бизнес
                </p>
                <a href="{{ route('settings.users.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить пользователя</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" 
         @click.away="closeDeleteModal()"
         @keydown.escape.window="closeDeleteModal()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         style="display: none;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 max-w-sm w-full overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                <button @click="closeDeleteModal()"
                    class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-6">
                    Вы уверены, что хотите удалить пользователя <span class="font-semibold" x-text="userName"></span> из бизнеса? Это действие нельзя отменить.
                </p>
                <div class="flex gap-3">
                    <button @click="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </button>
                    <button @click="confirmDelete()"
                        class="flex-1 px-4 py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium transition-colors">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
