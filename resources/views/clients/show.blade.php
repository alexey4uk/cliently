@extends('layouts.user')

@section('title', 'Профиль клиента - Cliently')
@section('page-title', 'Профиль клиента')
@section('page-description', 'Информация о клиенте')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => $client->full_name, 'url' => null]
    ]" />
@endpush

@section('content')
<div class="space-y-6">
    <!-- Заголовок страницы -->
    <div class="flex flex-col gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Аватар -->
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    {{ $client->initials }}
                </div>
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $client->full_name }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <i class="fa-solid fa-calendar text-xs"></i>
                        <span>Клиент с {{ $client->created_at->format('d.m.Y') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('clients.edit', $client) }}"
                   class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-150 flex items-center gap-1.5">
                    <i class="fa-solid fa-edit text-xs"></i>
                    <span>Редактировать</span>
                </a>
                <form method="POST" action="{{ route('clients.destroy', $client) }}" 
                      onsubmit="return confirm('Вы уверены, что хотите удалить этого клиента?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center gap-1.5">
                        <i class="fa-solid fa-trash text-xs"></i>
                        <span>Удалить</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Основная информация</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Имя
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $client->first_name }}
                    </p>
                </div>

                @if($client->last_name)
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Фамилия
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $client->last_name }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Контактная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Контактная информация</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Телефон
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-phone text-xs text-slate-400"></i>
                        <span>{{ $client->phone }}</span>
                    </p>
                </div>

                @if($client->email)
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Email
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                        <span>{{ $client->email }}</span>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <span>Системная информация</span>
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Дата добавления
                    </label>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $client->created_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $client->created_at->format('H:i') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Последнее обновление
                    </label>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $client->updated_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $client->updated_at->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
