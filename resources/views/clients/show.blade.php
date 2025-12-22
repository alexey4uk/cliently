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
    <!-- Заголовок с действиями -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <!-- Аватар -->
            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-semibold text-xl">
                {{ $client->initials }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $client->full_name }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Клиент с {{ $client->created_at->format('d.m.Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('clients.edit', $client) }}"
               class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-edit"></i>
                Редактировать
            </a>
            <form method="POST" action="{{ route('clients.destroy', $client) }}" 
                  onsubmit="return confirm('Вы уверены, что хотите удалить этого клиента?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    Удалить
                </button>
            </form>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                    Основная информация
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Имя
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $client->first_name }}
                    </p>
                </div>

                @if($client->last_name)
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Фамилия
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $client->last_name }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Контактная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                    Контактная информация
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Телефон
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $client->phone }}
                    </p>
                </div>

                @if($client->email)
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Email
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $client->email }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
        <div class="space-y-5">
            <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
                    Дополнительная информация
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Дата добавления
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $client->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Последнее обновление
                    </label>
                    <p class="mt-1 text-base text-slate-900 dark:text-white">
                        {{ $client->updated_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
