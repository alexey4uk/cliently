@extends('layouts.user')

@section('title', 'Добавить пользователя - Cliently')
@section('page-title', 'Добавить пользователя')
@section('page-description', 'Пригласить или создать пользователя')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Пользователи', 'url' => route('settings.users.index')], ['title' => 'Добавить', 'url' => null]]" />
@endpush

@section('content')

<div class="max-w-2xl mx-auto">
    <!-- Заголовок -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Добавить пользователя</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Пригласите пользователя по email или создайте вручную</p>
    </div>

    <!-- Вкладки -->
    <div x-data="{ activeTab: 'invitation' }" class="space-y-6">
        <!-- Переключатель вкладок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-1">
            <div class="flex gap-2">
                <button @click="activeTab = 'invitation'" 
                    :class="activeTab === 'invitation' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-envelope mr-2"></i>
                    Пригласить по email
                </button>
                <button @click="activeTab = 'manual'" 
                    :class="activeTab === 'manual' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-user-plus mr-2"></i>
                    Создать вручную
                </button>
            </div>
        </div>

        <!-- Вкладка: Приглашение -->
        <div x-show="activeTab === 'invitation'" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <form method="POST" action="{{ route('settings.users.invite') }}" class="space-y-5">
                @csrf

                @php
                    $roleLabels = [
                        'owner' => 'Владелец',
                        'admin' => 'Администратор',
                        'master' => 'Мастер',
                    ];
                @endphp

                <div>
                    <label for="invite_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Email адрес <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="invite_email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-rose-500 @enderror"
                           placeholder="user@example.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invite_role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Роль <span class="text-rose-500">*</span>
                    </label>
                    <select id="invite_role" 
                            name="role_id" 
                            required
                            x-on:change="$dispatch('role-changed', { roleId: $event.target.value })"
                            class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role_id') border-rose-500 @enderror">
                        <option value="">Выберите роль</option>
                        @foreach($availableRoles as $roleKey)
                            <option value="{{ $roleKey->id }}" {{ old('role_id') == $roleKey->id ? 'selected' : '' }}>
                                {{ $roleKey->name ?? ($roleLabels[$roleKey->slug] ?? ucfirst($roleKey->slug)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                        Пользователь получит приглашение на указанный email
                    </p>
                </div>

                <!-- Поле выбора мастера для приглашения (показывается только для роли "мастер") -->
                <div x-data="{ 
                    selectedRoleId: '{{ old('role_id', '') }}',
                    isMasterRole: false,
                    init() {
                        this.checkRole();
                        this.$watch('selectedRoleId', () => this.checkRole());
                        this.$watch('isMasterRole', (val) => {
                            if (!val) {
                                document.getElementById('invite_master_id').value = '';
                            }
                        });
                    },
                    checkRole() {
                        const roleSelect = document.getElementById('invite_role');
                        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                        const roleSlug = selectedOption ? selectedOption.text.toLowerCase() : '';
                        this.isMasterRole = roleSlug.includes('мастер') || roleSlug.includes('master');
                    }
                }" 
                x-on:role-changed.window="selectedRoleId = $event.detail.roleId; checkRole();"
                x-show="isMasterRole" 
                x-transition
                class="space-y-3">
                    <div>
                        <label for="invite_master_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Мастер
                        </label>
                        <select id="invite_master_id" 
                                name="master_id" 
                                class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('master_id') border-rose-500 @enderror">
                            <option value="">Создать нового мастера при принятии приглашения</option>
                            @foreach($masters ?? [] as $master)
                                <option value="{{ $master->id }}" {{ old('master_id') == $master->id ? 'selected' : '' }}>
                                    {{ $master->first_name }} {{ $master->last_name }} ({{ $master->specialization }})
                                </option>
                            @endforeach
                        </select>
                        @error('master_id')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            Выберите существующего мастера или оставьте пустым для создания нового при принятии приглашения
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('settings.users.index') }}" 
                       class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <span>Отправить приглашение</span>
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Вкладка: Ручное создание -->
        <div x-show="activeTab === 'manual'" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <form method="POST" action="{{ route('settings.users.manual') }}" class="space-y-5">
                @csrf

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label for="manual_first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Имя <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="manual_first_name" 
                               name="first_name" 
                               value="{{ old('first_name') }}"
                               required
                               class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('first_name') border-rose-500 @enderror">
                        @error('first_name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="manual_last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Фамилия
                        </label>
                        <input type="text" 
                               id="manual_last_name" 
                               name="last_name" 
                               value="{{ old('last_name') }}"
                               class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('last_name') border-rose-500 @enderror">
                        @error('last_name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="manual_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Email адрес <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="manual_email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-rose-500 @enderror"
                           placeholder="user@example.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                        Если пользователь с таким email уже существует, он будет добавлен в бизнес. Иначе будет создан новый аккаунт.
                    </p>
                </div>

                <div>
                    <label for="manual_role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Роль <span class="text-rose-500">*</span>
                    </label>
                    <select id="manual_role" 
                            name="role_id" 
                            required
                            x-on:change="$dispatch('role-changed', { roleId: $event.target.value })"
                            class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role_id') border-rose-500 @enderror">
                        <option value="">Выберите роль</option>
                        @foreach($availableRoles as $roleKey)
                            <option value="{{ $roleKey->id }}" {{ old('role_id') == $roleKey->id ? 'selected' : '' }}>
                                {{ $roleKey->name ?? ($roleLabels[$roleKey->slug] ?? ucfirst($roleKey->slug)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Новому пользователю будет создан временный пароль, который нужно будет сменить при первом входе.
                    </p>
                </div>

                <!-- Поле выбора мастера (показывается только для роли "мастер") -->
                <div x-data="{ 
                    selectedRoleId: '{{ old('role_id', '') }}',
                    isMasterRole: false,
                    init() {
                        this.checkRole();
                        this.$watch('selectedRoleId', () => this.checkRole());
                        this.$watch('isMasterRole', (val) => {
                            if (!val) {
                                document.getElementById('manual_master_id').value = '';
                            }
                        });
                    },
                    checkRole() {
                        const roleSelect = document.getElementById('manual_role');
                        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                        const roleSlug = selectedOption ? selectedOption.text.toLowerCase() : '';
                        this.isMasterRole = roleSlug.includes('мастер') || roleSlug.includes('master');
                    }
                }" 
                x-on:role-changed.window="selectedRoleId = $event.detail.roleId; checkRole();"
                x-show="isMasterRole" 
                x-transition
                class="space-y-3">
                    <div>
                        <label for="manual_master_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Мастер
                        </label>
                        <select id="manual_master_id" 
                                name="master_id" 
                                class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('master_id') border-rose-500 @enderror">
                            <option value="">Создать нового мастера</option>
                            @foreach($masters ?? [] as $master)
                                <option value="{{ $master->id }}" {{ old('master_id') == $master->id ? 'selected' : '' }}>
                                    {{ $master->first_name }} {{ $master->last_name }} ({{ $master->specialization }})
                                </option>
                            @endforeach
                        </select>
                        @error('master_id')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            Выберите существующего мастера или оставьте пустым для создания нового
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('settings.users.index') }}" 
                       class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </a>
                    <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <span>Создать пользователя</span>
                        <i class="fa-solid fa-check text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
