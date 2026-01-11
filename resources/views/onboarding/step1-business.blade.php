@extends('layouts.user')

@section('title', 'Создание бизнеса - Cliently')
@section('page-title', 'Создание бизнеса')
@section('page-description', 'Основная информация о вашем бизнесе')

@section('content')
    <!-- Индикатор прогресса -->
    <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 mb-6">

        <!-- Индикатор прогресса -->
        <div class="w-full md:w-auto">
            <div class="flex items-center w-full md:w-auto md:gap-1.5">
                @php
                    $steps = [
                        1 => 'Бизнес',
                        2 => 'Локация',
                        3 => 'Услуга',
                        4 => 'Мастер',
                    ];
                @endphp
                @for ($i = 1; $i <= 4; $i++)
                    <div class="flex items-center {{ $i < 4 ? 'flex-1 md:flex-none' : 'shrink-0' }}">
                        <div
                            class="flex items-center justify-center w-6 md:w-7 h-6 md:h-7 rounded-full text-xs font-semibold transition-colors shrink-0 {{ $i == 1 ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            {{ $i }}
                        </div>
                        @if ($i < 4)
                            <div
                                class="flex-1 md:w-6 md:flex-none h-0.5 mx-1 md:mx-0 {{ $i <= 1 ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('onboarding.business.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-6">
            <!-- Основная информация -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400"></i>
                            Основная информация
                        </h3>
                    </div>

                    <div>
                        <livewire:text-input name="name" id="name" label="Организация" required="true" />
                    </div>

                    <livewire:slug-checker />
                </div>
            </div>

            <!-- Информация о владельце -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                            Информация о владельце
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <livewire:text-input name="first_name" id="first_name" label="Имя" required="true" modifier="capitalize" />
                        </div>

                        <div>
                            <livewire:text-input name="last_name" id="last_name" label="Фамилия" modifier="capitalize" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Контактная информация -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                            Контактная информация
                        </h3>
                    </div>

                    <div>
                        <livewire:phone-input name="phone" label="Телефон" required="true" />
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-indigo-600 dark:text-indigo-400"></i>
                            Дополнительная информация
                        </h3>
                    </div>

                    <div>

                    <livewire:textarea-input 
                        name="description"
                        label="Описание"
                        placeholder="Краткое описание вашего бизнеса..."
                        :rows="3"
                        :maxlength="500"
                        :show-counter="true"
                    />

                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
            <button type="submit" id="submitButton"
                class="px-3 md:px-4 py-1.5 md:py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Продолжить <i class="fa-solid fa-arrow-right ml-1.5 md:ml-2"></i>
            </button>
        </div>
    </form>
@endsection
