@extends('layouts.user')

@section('title', 'Редактировать пользователя - Cliently')
@section('page-title', 'Редактировать пользователя')
@section('page-description', 'Изменить роль пользователя')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Пользователи', 'url' => route('settings.users.index')], ['title' => 'Редактировать', 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-2xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="w-full h-full rounded-full object-cover">
                @else
                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                @endif
            </div>
            @php
                $roleLabels = [
                    'owner' => 'Владелец',
                    'admin' => 'Администратор',
                    'master' => 'Мастер',
                ];
            @endphp

            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <!-- Форма редактирования -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <form method="POST" action="{{ route('settings.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Роль в бизнесе <span class="text-rose-500">*</span>
                </label>
                <select id="role" 
                        name="role_id" 
                        required
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role_id') border-rose-500 @enderror">
                    @foreach($availableRoles as $roleKey)
                        <option value="{{ $roleKey->id }}"
                                {{ $currentRole?->id === $roleKey->id ? 'selected' : '' }}
                                {{ $roleKey->slug === 'owner' && $currentRole?->slug !== 'owner' ? 'disabled' : '' }}>
                            {{ $roleKey->name ?? ($roleLabels[$roleKey->slug] ?? ucfirst($roleKey->slug)) }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                    Текущая роль: 
                    <span class="font-medium">
                        {{ $currentRole?->name ?? ($roleLabels[$currentRole?->slug] ?? ucfirst($currentRole?->slug ?? '')) }}
                    </span>
                </p>
                @if($currentRole?->slug === 'owner')
                    <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Роль владельца нельзя изменить
                    </p>
                @endif
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('settings.users.index') }}" 
                   class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Отмена
                </a>
                <button type="submit" 
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <span>Сохранить изменения</span>
                    <i class="fa-solid fa-check text-sm"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
