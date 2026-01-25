@extends('layouts.panel')

@section('title', 'Профиль пользователя - Cliently')
@section('page-title', 'Профиль')
@section('page-description', 'Управление личными данными')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Профиль', 'url' => null]
    ]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto">
    <div x-data="profileData" class="space-y-6">
    
    <!-- Карточка профиля с аватаром -->
    <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 sm:p-10">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Аватар -->
                <div class="relative flex-shrink-0 group">
                    <label for="avatar" class="cursor-pointer block" :class="isUploading ? 'pointer-events-none' : ''">
                        <div 
                            id="avatarPreview"
                            class="relative h-32 w-32 sm:h-40 sm:w-40 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-4xl sm:text-5xl font-bold text-white overflow-hidden border-4 border-white/30 shadow-2xl transition-all hover:border-white/60 hover:scale-105">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" alt="Avatar Preview" class="w-full h-full object-cover" />
                            </template>
                            <template x-if="!avatarPreview && !hasAvatar">
                                <span x-text="userInitials"></span>
                            </template>
                            <template x-if="!avatarPreview && hasAvatar">
                                <img src="{{ $user->getAvatarUrl() ?? '' }}" alt="{{ $user->name }}" class="w-full h-full object-cover" referrerpolicy="no-referrer" />
                            </template>
                            
                            <!-- Индикатор загрузки -->
                            <div x-show="isUploading" 
                                 x-transition
                                 class="absolute inset-0 bg-black/60 flex items-center justify-center rounded-full z-20">
                                <i class="fa-solid fa-spinner fa-spin text-white text-2xl"></i>
                            </div>
                            
                            <!-- Подсказка при наведении -->
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-full z-10 pointer-events-none">
                                <div class="text-center px-4">
                                    <i class="fa-solid fa-camera text-white text-2xl mb-1"></i>
                                    <p class="text-white text-xs font-medium">Изменить фото</p>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="hidden" @change="handleAvatarChange($event)" />
                    </label>
                    
                    <!-- Кнопка удаления -->
                    <template x-if="hasAvatar || avatarPreview">
                        <button 
                            type="button"
                            @click.stop="showRemoveConfirm = true"
                            :disabled="isUploading"
                            class="absolute -top-2 -right-2 h-10 w-10 rounded-full bg-rose-500 hover:bg-rose-600 flex items-center justify-center text-white shadow-lg border-2 border-white transition-all hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed z-30">
                            <i class="fa-solid fa-trash-alt"></i>
                        </button>
                    </template>
                    
                    <!-- Сообщение об ошибке -->
                    <div x-show="avatarError" 
                         x-transition
                         class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap z-30">
                        <div class="bg-rose-500 text-white text-xs px-3 py-2 rounded-lg shadow-lg">
                            <span x-text="avatarError"></span>
                        </div>
                    </div>
                </div>

                <!-- Информация пользователя -->
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ $user->name }}</h1>
                    <p class="text-white/90 text-sm sm:text-base mb-3">{{ $user->email }}</p>
                    @if($user->phone)
                        <p class="text-white/80 text-sm mb-4">
                            <i class="fa-solid fa-phone mr-2"></i>{{ $user->phone }}
                        </p>
                    @endif
                    
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                            <i class="fa-solid fa-shield-halved mr-1.5"></i>
                            Аккаунт активен
                        </span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/80 text-white backdrop-blur-sm">
                                <i class="fa-solid fa-check-circle mr-1.5"></i>
                                Email подтвержден
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 px-6 py-4">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white flex items-center">
                <i class="fa-solid fa-user-circle mr-2 text-indigo-500"></i>
                Основная информация
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управляйте вашими личными данными</p>
        </div>
        
        <form 
            method="POST" 
            action="{{ route('panel.profile.update') }}" 
            enctype="multipart/form-data" 
            @submit.prevent="submitProfileForm"
            class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Скрытое поле для удаления аватара -->
                <input type="hidden" name="remove_avatar" id="remove_avatar" :value="removeAvatar ? '1' : '0'">

            <!-- Подсказки по аватару -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-info-circle text-blue-500 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Требования к фото профиля</h4>
                        <ul class="space-y-1.5 text-xs text-blue-700 dark:text-blue-300">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-blue-500"></i>
                                <span>Форматы: <strong>JPG, PNG, GIF, WEBP</strong></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-blue-500"></i>
                                <span>Максимальный размер: <strong>5 МБ</strong></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-blue-500"></i>
                                <span>Рекомендуемое разрешение: <strong>400×400px</strong></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Имя -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center">
                        <i class="fa-solid fa-user mr-2 text-slate-400"></i>
                        Имя <span class="text-rose-500 ml-1">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required 
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                        placeholder="Введите ваше имя" />
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center">
                            <i class="fa-solid fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center">
                        <i class="fa-solid fa-envelope mr-2 text-slate-400"></i>
                        Email <span class="text-rose-500 ml-1">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-3 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                        placeholder="your@email.com" />
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center">
                            <i class="fa-solid fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            @php
                $phoneCountry = $user->primaryPhone?->country ?? ($countries->first());
                $phoneNational = '';
                if ($user->primaryPhone && $phoneCountry) {
                    $codeDig = preg_replace('/\D/', '', $phoneCountry->calling_code);
                    $phoneDig = preg_replace('/\D/', '', $user->primaryPhone->phone);
                    $phoneNational = $codeDig && str_starts_with($phoneDig, $codeDig) ? substr($phoneDig, strlen($codeDig)) : $phoneDig;
                }
            @endphp
            <div id="profilePhoneBlock"
                data-countries="{{ json_encode($countries->map(fn ($c) => ['id' => $c->id, 'code' => $c->calling_code, 'name' => $c->name])->values()) }}"
                data-old-phone="{{ old('phone', $user->phone) }}"
                data-old-country="{{ old('phone_country_id', $user->primaryPhone?->country_id) }}">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center">
                    <i class="fa-solid fa-phone mr-2 text-slate-400"></i>
                    Телефон
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="sm:w-48">
                        <select id="phone_country_id" name="phone_country_id"
                            class="w-full px-4 py-3 border {{ $errors->has('phone_country_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all">
                            <option value="">—</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" data-code="{{ $c->calling_code }}" {{ old('phone_country_id', $user->primaryPhone?->country_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->calling_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 relative">
                        <span id="profilePhonePrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm pointer-events-none"></span>
                        <input type="tel" id="phone_national" inputmode="numeric" maxlength="15"
                            value="{{ old('phone_national', $phoneNational) }}"
                            class="w-full pl-14 pr-4 py-3 border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                            placeholder="291234567">
                        <input type="hidden" name="phone" id="profilePhone" value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>
                @error('phone_country_id')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center"><i class="fa-solid fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
                @error('phone')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center"><i class="fa-solid fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center">
                    <i class="fa-solid fa-info-circle mr-1.5"></i>
                    Формат: код страны + номер. Необязательно.
                </p>
            </div>

            <!-- Кнопка сохранения -->
            <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-xl transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <template x-if="!isSubmitting">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            Сохранить изменения
                        </span>
                    </template>
                    <template x-if="isSubmitting">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            Сохранение...
                        </span>
                    </template>
                </button>
            </div>
            </form>
        </div>

    <!-- Безопасность -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 px-6 py-4">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white flex items-center">
                <i class="fa-solid fa-shield-halved mr-2 text-emerald-500"></i>
                Безопасность
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Измените пароль для защиты вашего аккаунта</p>
        </div>
        
        <form 
            method="POST" 
            action="{{ route('panel.profile.password.update') }}" 
            @submit.prevent="submitPasswordForm"
            class="p-6 space-y-6">
                @csrf
                @method('PATCH')

            <!-- Текущий пароль -->
            <div>
                <label for="current_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center">
                    <i class="fa-solid fa-key mr-2 text-slate-400"></i>
                    Текущий пароль <span class="text-rose-500 ml-1">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password"
                        x-ref="currentPassword"
                        class="w-full px-4 py-3 pr-12 border {{ $errors->has('current_password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                        placeholder="Введите текущий пароль" />
                    <button 
                        type="button"
                        @click="togglePasswordVisibility('current_password')"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-eye" x-show="!passwordVisible.current_password"></i>
                        <i class="fa-solid fa-eye-slash" x-show="passwordVisible.current_password"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center">
                        <i class="fa-solid fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Новый пароль -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center">
                    <i class="fa-solid fa-lock mr-2 text-slate-400"></i>
                    Новый пароль <span class="text-rose-500 ml-1">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        x-ref="password"
                        @input="checkPasswordStrength"
                        class="w-full px-4 py-3 pr-12 border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                        placeholder="Введите новый пароль" />
                    <button 
                        type="button"
                        @click="togglePasswordVisibility('password')"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-eye" x-show="!passwordVisible.password"></i>
                        <i class="fa-solid fa-eye-slash" x-show="passwordVisible.password"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center">
                        <i class="fa-solid fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
                
                <!-- Индикатор сложности пароля -->
                <div x-show="passwordStrength.show" x-transition class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-1 h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div 
                                class="h-full transition-all duration-300 rounded-full"
                                :class="passwordStrength.color"
                                :style="'width: ' + passwordStrength.width + '%'">
                            </div>
                        </div>
                        <span class="text-sm font-semibold whitespace-nowrap" :class="passwordStrength.textColor" x-text="passwordStrength.text"></span>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400">
                        <i class="fa-solid fa-lightbulb text-yellow-500 mr-1"></i>
                        Используйте комбинацию букв, цифр и специальных символов
                    </div>
                </div>
            </div>

            <!-- Подтверждение нового пароля -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center">
                    <i class="fa-solid fa-check-circle mr-2 text-slate-400"></i>
                    Подтверждение нового пароля <span class="text-rose-500 ml-1">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        x-ref="passwordConfirmation"
                        class="w-full px-4 py-3 pr-12 border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 rounded-xl focus:outline-none focus:ring-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                        placeholder="Повторите новый пароль" />
                    <button 
                        type="button"
                        @click="togglePasswordVisibility('password_confirmation')"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-eye" x-show="!passwordVisible.password_confirmation"></i>
                        <i class="fa-solid fa-eye-slash" x-show="passwordVisible.password_confirmation"></i>
                    </button>
                </div>
            </div>

            <!-- Кнопка сохранения -->
            <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                <button 
                    type="submit"
                    :disabled="isSubmittingPassword"
                    class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <template x-if="!isSubmittingPassword">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved"></i>
                            Изменить пароль
                        </span>
                    </template>
                    <template x-if="isSubmittingPassword">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            Изменение...
                        </span>
                    </template>
                </button>
            </div>
            </form>
        </div>

    <!-- Модальное окно подтверждения удаления аватара -->
    <div 
        x-show="showRemoveConfirm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md p-4"
        @click.self="showRemoveConfirm = false"
        style="display: none;">
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
            class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 max-w-md w-full">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-rose-500 to-pink-500 mb-5 shadow-lg">
                    <i class="fa-solid fa-trash-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-3">
                    Удалить аватар?
                </h3>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                    Вы уверены, что хотите удалить фото профиля? После удаления будут отображаться инициалы вашего имени.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        @click="showRemoveConfirm = false"
                        class="flex-1 px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-xmark mr-2"></i>
                        Отмена
                    </button>
                    <button 
                        @click="confirmRemoveAvatar"
                        class="flex-1 px-5 py-3 text-sm font-semibold text-white bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 rounded-xl transition-all shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-trash-alt mr-2"></i>
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileData', () => ({
            avatarPreview: null,
            showRemoveConfirm: false,
            removeAvatar: false,
            isSubmitting: false,
            isSubmittingPassword: false,
            isUploading: false,
            avatarError: null,
            hasAvatar: {{ $user->avatar ? 'true' : 'false' }},
            userInitials: '{{ strtoupper(mb_substr($user->name, 0, 2)) }}',
            profileUpdateUrl: '{{ route('panel.profile.update') }}',
            passwordVisible: {
                current_password: false,
                password: false,
                password_confirmation: false
            },
            passwordStrength: {
                show: false,
                width: 0,
                text: '',
                color: 'bg-slate-300',
                textColor: 'text-slate-500'
            },

            handleAvatarChange(event) {
                const file = event.target.files[0];
                
                // Сбрасываем предыдущую ошибку
                this.avatarError = null;
                
                if (!file) {
                    return;
                }

                // Валидация типа файла
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    this.avatarError = 'Недопустимый формат файла';
                    event.target.value = '';
                    setTimeout(() => {
                        this.avatarError = null;
                    }, 3000);
                    return;
                }

                // Валидация размера файла
                const maxSize = 5 * 1024 * 1024; // 5 МБ
                if (file.size > maxSize) {
                    this.avatarError = 'Размер файла превышает 5 МБ';
                    event.target.value = '';
                    setTimeout(() => {
                        this.avatarError = null;
                    }, 3000);
                    return;
                }

                // Показываем индикатор загрузки
                this.isUploading = true;

                // Читаем файл
                const reader = new FileReader();
                const self = this;

                reader.onload = function(e) {
                    self.avatarPreview = e.target.result;
                    self.hasAvatar = true;
                    self.isUploading = false;
                };

                reader.onerror = function() {
                    self.avatarError = 'Ошибка при чтении файла';
                    self.isUploading = false;
                    event.target.value = '';
                    setTimeout(() => {
                        self.avatarError = null;
                    }, 3000);
                };

                reader.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percentLoaded = Math.round((e.loaded / e.total) * 100);
                        // Можно добавить прогресс-бар в будущем
                    }
                };

                reader.readAsDataURL(file);
            },

            confirmRemoveAvatar() {
                this.removeAvatar = true;
                this.avatarPreview = null;
                this.hasAvatar = false;
                this.showRemoveConfirm = false;
                
                // Автоматически отправляем форму
                const form = document.querySelector('form[action="' + this.profileUpdateUrl + '"]');
                if (form) {
                    const hiddenInput = document.getElementById('remove_avatar');
                    if (hiddenInput) {
                        hiddenInput.value = '1';
                    }
                    form.submit();
                }
            },

            submitProfileForm(event) {
                this.isSubmitting = true;
                event.target.submit();
            },

            submitPasswordForm(event) {
                this.isSubmittingPassword = true;
                event.target.submit();
            },

            togglePasswordVisibility(field) {
                this.passwordVisible[field] = !this.passwordVisible[field];
                const input = this.$refs[field];
                if (input) {
                    input.type = this.passwordVisible[field] ? 'text' : 'password';
                }
            },

            checkPasswordStrength() {
                const password = this.$refs.password.value;
                if (!password) {
                    this.passwordStrength.show = false;
                    return;
                }

                this.passwordStrength.show = true;
                let strength = 0;
                
                if (password.length >= 8) strength += 20;
                if (password.length >= 12) strength += 10;
                if (/[A-Z]/.test(password)) strength += 20;
                if (/[a-z]/.test(password)) strength += 20;
                if (/[0-9]/.test(password)) strength += 15;
                if (/[^A-Za-z0-9]/.test(password)) strength += 15;

                this.passwordStrength.width = Math.min(strength, 100);

                if (strength < 40) {
                    this.passwordStrength.color = 'bg-red-500';
                    this.passwordStrength.text = 'Слабый';
                    this.passwordStrength.textColor = 'text-red-600 dark:text-red-400';
                } else if (strength < 70) {
                    this.passwordStrength.color = 'bg-yellow-500';
                    this.passwordStrength.text = 'Средний';
                    this.passwordStrength.textColor = 'text-yellow-600 dark:text-yellow-400';
                } else {
                    this.passwordStrength.color = 'bg-green-500';
                    this.passwordStrength.text = 'Надежный';
                    this.passwordStrength.textColor = 'text-green-600 dark:text-green-400';
                }
            },

            showToast(type, message) {
                // Используем существующую систему уведомлений
                console.log(type, message);
            }
        }));
    });

    (function() {
        const block = document.getElementById('profilePhoneBlock');
        const sel = document.getElementById('phone_country_id');
        const national = document.getElementById('phone_national');
        const hidden = document.getElementById('profilePhone');
        const prefix = document.getElementById('profilePhonePrefix');
        function updatePhone() {
            const opt = sel && sel.options[sel.selectedIndex];
            const code = opt && opt.value ? (opt.dataset.code || '').replace(/\D/g, '') : '';
            const digits = national && national.value ? national.value.replace(/\D/g, '') : '';
            const full = code && digits ? '+' + code + digits : '';
            if (hidden) hidden.value = full;
            if (prefix) prefix.textContent = opt && opt.value ? opt.dataset.code || '' : '';
        }
        if (sel) sel.addEventListener('change', function() { updatePhone(); if (national) national.placeholder = (this.selectedIndex && this.options[this.selectedIndex].dataset.code === '+375') ? '291234567' : '9123456789'; });
        if (national) national.addEventListener('input', function() { this.value = this.value.replace(/\D/g, '').slice(0, 15); updatePhone(); });
        if (sel && sel.options.length) {
            const opt = sel.options[sel.selectedIndex];
            if (prefix) prefix.textContent = opt && opt.value ? opt.dataset.code || '' : '';
            if (national) national.placeholder = (opt && opt.value && opt.dataset.code === '+375') ? '291234567' : '9123456789';
            const op = block && block.dataset.oldPhone ? block.dataset.oldPhone : '', oc = block && block.dataset.oldCountry ? String(block.dataset.oldCountry) : '';
            if (op && oc && sel.value === oc && opt) { const codeDigits = (opt.dataset.code || '').replace(/\D/g, ''), phoneDigits = op.replace(/\D/g, ''); if (phoneDigits.startsWith(codeDigits)) national.value = phoneDigits.slice(codeDigits.length); }
            updatePhone();
        }
        const form = block && block.closest('form');
        if (form) form.addEventListener('submit', updatePhone);
    })();
</script>
@endpush
