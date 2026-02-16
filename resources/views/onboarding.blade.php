@extends('layouts.user')

@section('title', 'Добро пожаловать - Cliently')
@section('page-title', 'Добро пожаловать')
@section('page-description', 'Начните работу с системой')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
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

<div class="max-w-4xl mx-auto">
    <!-- Заголовок -->
    <div class="text-center mb-8">
        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fa-solid fa-rocket text-white text-3xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Добро пожаловать в Cliently!</h1>
        <p class="text-lg text-slate-600 dark:text-slate-400">
            Начните работу с системой управления клиентами
        </p>
    </div>

    <!-- Приглашения -->
    @if($invitations->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-envelope-open text-indigo-600 dark:text-indigo-400"></i>
                <span>У вас есть приглашения</span>
            </h2>
            <div class="space-y-4">
                @foreach($invitations as $invitation)
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                                    {{ $invitation->business->name }}
                                </h3>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $getRoleBadgeClass($invitation->businessRole?->slug ?? '') }}">
                                        <i class="fa-solid {{ $getRoleIcon($invitation->businessRole?->slug ?? '') }} text-xs"></i>
                                        {{ $invitation->businessRole?->name ?? $getRoleLabel($invitation->businessRole?->slug ?? '') }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Приглашение от: {{ $invitation->creator->name ?? 'Система' }}
                                </p>
                            </div>
                            <a href="{{ route('invite.accept', ['token' => $invitation->token]) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors whitespace-nowrap">
                                <span>Принять</span>
                                <i class="fa-solid fa-arrow-right text-sm"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Уже есть бизнесы — выбор текущего -->
    @if($userBusinesses->isNotEmpty())
        <div class="mb-6 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                У вас уже есть бизнесы. Выберите, с каким работать, или создайте новый.
            </p>
            <a href="{{ route('settings.businesses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-list"></i>
                <span>Бизнесы</span>
            </a>
        </div>
    @endif

    <!-- Варианты действий -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Создать бизнес -->
        <a href="{{ route('settings.business.create') }}" 
           class="group bg-white dark:bg-slate-900 rounded-xl border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-600 shadow-sm hover:shadow-md p-8 transition-all">
            <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-building text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                Создать свой бизнес
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Создайте новый бизнес и начните управлять клиентами, записями и услугами
            </p>
            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-medium">
                <span>Создать</span>
                <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Принять приглашение -->
        @if($invitations->count() === 0)
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-800 p-8">
                <div class="h-16 w-16 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-envelope text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    Принять приглашение
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    У вас пока нет активных приглашений. Если вы получили приглашение, перейдите по ссылке из письма.
                </p>
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-8">
                <div class="h-16 w-16 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    Принять приглашение
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Выберите приглашение выше, чтобы присоединиться к бизнесу
                </p>
            </div>
        @endif
    </div>

    <!-- Информационная подсказка -->
    <div class="mt-8 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-2">
                    Что дальше?
                </h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                    После создания бизнеса или принятия приглашения вы сможете:
                </p>
                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Управлять клиентами и записями</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Настраивать услуги и локации</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                        <span>Использовать онлайн-запись</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
