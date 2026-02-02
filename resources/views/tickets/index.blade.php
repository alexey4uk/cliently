@extends('layouts.user')

@section('title', 'Тикеты - Cliently')
@section('page-title', 'Тикеты')
@section('page-description', 'Управление тикетами')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Тикеты', 'url' => null]]" />
@endpush

@section('content')

@php
    // Получаем бизнес и роль для проверки прав доступа
    $user = Auth::user();
    $currentBusiness = null;
    $currentBusinessRole = null;
    $currentBusinessRoleId = null;
    $permissionService = null;
    if ($user) {
        $user->load('businesses');
        $currentBusiness = $user->businesses->first();
        if ($currentBusiness) {
            $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
            $currentBusinessRole = $pivot?->pivot->role_id ? \App\Models\BusinessRole::find($pivot->pivot->role_id)?->slug : null;
            $currentBusinessRoleId = $pivot?->pivot->role_id;
            if ($currentBusinessRoleId) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
            }
        }
    }

    // Функция для проверки бизнес-прав
    $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
        if (!$currentBusinessRoleId || !$permissionService) {
            return false;
        }
        return $permissionService->hasPermission($currentBusinessRoleId, $permission);
    };
@endphp

    <div class="max-w-[1400px] mx-auto">
        <div class="space-y-6">
            <!-- Заголовок с кнопкой создания -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Мои тикеты</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление вашими обращениями в поддержку</p>
            </div>
            @if($hasBusinessPermission('client.tickets.create'))
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-plus text-sm"></i>
                    Создать тикет
                </a>
            @endif
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-search mr-2 text-slate-400"></i>Поиск
                        </label>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Поиск по теме или описанию..."
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-filter mr-2 text-slate-400"></i>Статус
                        </label>
                        <select name="status"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Все статусы</option>
                            <option value="new" {{ $status === 'new' ? 'selected' : '' }}>Новый</option>
                            <option value="open" {{ $status === 'open' ? 'selected' : '' }}>В работе</option>
                            <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Решен</option>
                            <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Закрыт</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fa-solid fa-filter mr-2"></i>Применить фильтры
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Список тикетов -->
        @if($tickets->count() > 0)
            <div class="grid gap-4">
                @foreach($tickets as $ticket)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">#{{ $ticket->id }}</span>
                                    <a href="{{ route('tickets.show', $ticket->id) }}" 
                                       class="text-lg font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        {{ $ticket->title }}
                                    </a>
                                </div>
                                
                                <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400 mb-3">
                                    @if($ticket->category)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-tag text-xs"></i>
                                            {{ $ticket->category->name }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                        {{ $ticket->created_at->format('d.m.Y H:i') }}
                                    </span>
                                    @if($ticket->comments_count > 0)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-comments text-xs"></i>
                                            {{ $ticket->comments_count }} {{ $ticket->comments_count === 1 ? 'комментарий' : 'комментариев' }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
                                    {{ Str::limit($ticket->description, 150) }}
                                </p>
                            </div>

                            <div class="ml-4 flex flex-col items-end gap-3">
                                <span class="px-3 py-1.5 text-xs font-medium rounded-full whitespace-nowrap
                                    {{ $ticket->status === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' : '' }}
                                    {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : '' }}
                                    {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : '' }}
                                    {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' : '' }}">
                                    {{ $ticket->status === 'new' ? 'Новый' : ($ticket->status === 'open' ? 'В работе' : ($ticket->status === 'resolved' ? 'Решен' : 'Закрыт')) }}
                                </span>
                                
                                <a href="{{ route('tickets.show', $ticket->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    Открыть
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if($tickets->hasPages())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4">
                    {{ $tickets->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <i class="fa-solid fa-ticket text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Тикеты не найдены</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                        @if($search || $status)
                            Попробуйте изменить параметры фильтрации
                        @else
                            Создайте свой первый тикет для обращения в поддержку
                        @endif
                    </p>
                    @if(!$search && !$status)
                        <a href="{{ route('tickets.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            Создать тикет
                        </a>
                    @endif
                </div>
            </div>
        @endif
        </div>
    </div>
@endsection
