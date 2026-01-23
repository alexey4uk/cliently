@extends('layouts.auth')

@section('title', 'Принятие приглашения')

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
    $roleLabel = $roleLabels[$invitation->role] ?? ucfirst($invitation->role);
    $roleBadge = $roleBadgeClasses[$invitation->role] ?? 'text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300';
    $roleIcon = $roleIcons[$invitation->role] ?? 'fa-user';
@endphp

<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg p-6 md:p-8">
        <!-- Заголовок -->
        <div class="text-center mb-6">
            <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-envelope-open text-indigo-600 dark:text-indigo-400 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Приглашение в бизнес</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Вас пригласили присоединиться к бизнесу
            </p>
        </div>

        <!-- Информация о приглашении -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 mb-6">
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Бизнес</p>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $invitation->business->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Роль</p>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $roleBadge }}">
                            <i class="fa-solid {{ $roleIcon }} text-xs"></i>
                            {{ $roleLabel }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Email</p>
                    <p class="text-sm text-slate-900 dark:text-white">{{ $invitation->email }}</p>
                </div>
            </div>
        </div>

        @if($userExists)
            <!-- Для существующего пользователя -->
            <div class="mb-6">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 text-center">
                    У вас уже есть аккаунт. Войдите, чтобы принять приглашение.
                </p>
                <form method="POST" action="{{ route('invite.store', ['token' => $invitation->token]) }}">
                    @csrf
                    <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors">
                        <span>Принять приглашение</span>
                        <i class="fa-solid fa-check text-sm"></i>
                    </button>
                </form>
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        Войти в другой аккаунт
                    </a>
                </div>
            </div>
        @else
            <!-- Для нового пользователя -->
            <div class="mb-6">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 text-center">
                    Создайте аккаунт, чтобы присоединиться к бизнесу.
                </p>
                <form method="POST" action="{{ route('invite.activate', ['token' => $invitation->token]) }}" class="space-y-4">
                    @csrf
                    
                    <!-- Пароль -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Пароль
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-rose-500 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Подтверждение пароля -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Подтверждение пароля
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors">
                        <span>Создать аккаунт и присоединиться</span>
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </button>
                </form>
            </div>
        @endif

        <!-- Сообщения об ошибках -->
        @if(session('error'))
            <div class="mt-4 p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg">
                <p class="text-sm text-rose-600 dark:text-rose-400">{{ session('error') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
