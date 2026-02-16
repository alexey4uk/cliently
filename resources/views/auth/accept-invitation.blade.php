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
    $roleSlug = $invitation->businessRole?->slug ?? '';
    $roleLabel = $invitation->businessRole?->name ?? ($roleLabels[$roleSlug] ?? ucfirst($roleSlug));
    $roleBadge = $roleBadgeClasses[$roleSlug] ?? 'text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300';
    $roleIcon = $roleIcons[$roleSlug] ?? 'fa-user';
@endphp

    <!-- Основной контейнер с улучшенным дизайном -->
    <div class="max-w-sm w-full mx-auto">
        <!-- Заголовок страницы -->
        <div class="text-center mb-6">
            <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-envelope-open text-indigo-600 dark:text-indigo-400 text-2xl"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white mb-2">Приглашение в бизнес</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">
                Вас пригласили присоединиться к бизнесу
            </p>
        </div>

        <!-- Карточка формы с улучшенным дизайном -->
        <div class="rounded-xl border border-slate-200 bg-white/80 p-5 md:p-6 shadow-md dark:border-slate-800 dark:bg-slate-900/80 animate-fade-in-up">
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
                <!-- Для существующего пользователя: войти или принять, если уже вошли -->
                <div class="mb-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 text-center">
                        @if($canAcceptAsAuth ?? false)
                            Принять приглашение в бизнес «{{ $invitation->business->name }}»?
                        @else
                            У вас уже есть аккаунт. Войдите, чтобы принять приглашение.
                        @endif
                    </p>
                    @if($canAcceptAsAuth ?? false)
                        <form method="POST" action="{{ route('invite.store', ['token' => $invitation->token]) }}">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transform hover:scale-[1.01] active:scale-[0.99]">
                                <span>Принять приглашение</span>
                                <i class="fa-solid fa-check text-sm"></i>
                            </button>
                        </form>
                    @endif
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors font-medium underline underline-offset-2">
                            {{ ($canAcceptAsAuth ?? false) ? 'Войти в другой аккаунт' : 'Войти' }}
                        </a>
                    </div>
                </div>
            @else
                <!-- Для нового пользователя -->
                <div class="mb-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 text-center">
                        Создайте аккаунт, чтобы присоединиться к бизнесу.
                    </p>
                    <form method="POST" action="{{ route('invite.activate', ['token' => $invitation->token]) }}" class="space-y-4">
                        @csrf

                        <!-- Пароль -->
                        <div class="space-y-2">
                            <label for="password" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                                <x-icon name="lock-closed" size="sm" class="text-indigo-500 dark:text-indigo-400" />
                                <span>Пароль</span>
                                <span class="text-rose-500 dark:text-rose-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       required
                                       autocomplete="new-password"
                                       class="w-full px-3 py-2 text-sm rounded-lg border-2 {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200"
                                       placeholder="•••••••• (минимум 8 символов)">
                                @error('password')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <x-icon name="exclamation-circle" size="sm" class="text-rose-500" />
                                    </div>
                                @enderror
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Подтверждение пароля -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                                <x-icon name="lock-closed" size="sm" class="text-indigo-500 dark:text-indigo-400" />
                                <span>Подтверждение пароля</span>
                                <span class="text-rose-500 dark:text-rose-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required
                                       autocomplete="new-password"
                                       class="w-full px-3 py-2 text-sm rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transform hover:scale-[1.01] active:scale-[0.99]">
                            <span>Создать аккаунт и присоединиться</span>
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
