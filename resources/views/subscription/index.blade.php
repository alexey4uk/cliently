@extends('layouts.user')

@section('title', 'Тарифы - Cliently')
@section('page-title', 'Тарифы')
@section('page-description', null)

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Тарифы']]" />
@endpush

@section('content')

@php
    // Метрики из БД по типу: лимиты (integer) и флаги (boolean)
    $integerMetricsList = $metrics->where('type', 'integer')->values();
    $booleanMetricsList = $metrics->where('type', 'boolean')->values();
@endphp

@php
    $currentPlanPriceForModal = $currentPlan && $currentPlan->price !== null ? (float) $currentPlan->price : 0;
@endphp
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-2 sm:pt-4 pb-6 sm:pb-8" x-data="{
        showConfirmModal: false,
        selectedPlan: null,
        selectedForm: null,
        useTrialInput: null,
        useTrial: true,
        currentPlanPrice: {{ $currentPlanPriceForModal }},
        openConfirmModal(planName, planPrice, planInterval, trialDays, hasUsedTrial, form) {
            this.selectedPlan = {
                name: planName,
                price: planPrice ? parseFloat(planPrice) : 0,
                interval: planInterval,
                trialDays: trialDays ? parseInt(trialDays) : 0,
                hasUsedTrial: hasUsedTrial === true || hasUsedTrial === 'true'
            };
            this.selectedForm = form;
            if (form) {
                this.useTrialInput = form.querySelector('input[name=\'use_trial\']');
            } else {
                this.useTrialInput = null;
            }
            this.useTrial = this.selectedPlan.trialDays > 0 && !this.selectedPlan.hasUsedTrial;
            this.showConfirmModal = true;
        },
        closeConfirmModal() {
            this.showConfirmModal = false;
            this.selectedPlan = null;
            this.selectedForm = null;
            this.useTrialInput = null;
            this.useTrial = true;
        },
        confirmSubscription() {
            if (this.selectedForm) {
                if (this.useTrialInput) {
                    const shouldUseTrial = this.useTrial && this.selectedPlan.trialDays > 0 && !this.selectedPlan.hasUsedTrial;
                    this.useTrialInput.value = shouldUseTrial ? '1' : '0';
                }
                this.showConfirmModal = false;
                this.selectedForm.submit();
            }
        }
    }">

        @php
            $planCount = $plans->count();
            $gridClass = match ($planCount) {
                1 => 'grid grid-cols-1 max-w-md gap-3 sm:gap-4',
                2 => 'grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 max-w-4xl mx-auto',
                3 => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 max-w-5xl mx-auto',
                4 => 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4',
                5 => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 max-w-6xl mx-auto',
                default => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4',
            };
        @endphp
        <div id="plans-grid" class="{{ $gridClass }}">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                    $isPopular = $plan->slug === 'basic' && !$isCurrent;
                @endphp
                <x-subscription.plan-card
                    :plan="$plan"
                    :is-current="$isCurrent"
                    :is-popular="$isPopular"
                    :trial-usage="$trialUsage"
                    :integer-metrics-list="$integerMetricsList"
                    :boolean-metrics-list="$booleanMetricsList"
                    :has-active-paid-subscription="$hasActivePaidSubscription ?? false"
                />
            @endforeach
        </div>

        {{-- Confirm modal (paid with trial only) --}}
        <div x-show="showConfirmModal"
             @click.away="closeConfirmModal()"
             @keydown.escape.window="closeConfirmModal()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
             style="display: none;">
            <div @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-md w-full overflow-hidden my-auto max-h-[90vh] flex flex-col">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Подтверждение активации тарифа</h3>
                        <button @click="closeConfirmModal()"
                            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-6 overflow-y-auto flex-1 min-h-0">
                    <div x-show="selectedPlan" class="mb-6">
                        <div class="text-center mb-4">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-500/20 mb-3">
                                <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2" x-text="selectedPlan?.name"></h4>
                            <div class="flex items-baseline justify-center gap-2 mb-2">
                                <template x-if="selectedPlan && selectedPlan.price > 0">
                                    <div>
                                        <span class="text-3xl font-bold text-slate-900 dark:text-white" x-text="Math.round(selectedPlan.price).toLocaleString('ru-RU')"></span>
                                        <span class="text-lg text-slate-600 dark:text-slate-400 ml-1">BYN</span>
                                    </div>
                                </template>
                                <template x-if="!selectedPlan || !selectedPlan.price || selectedPlan.price === 0">
                                    <span class="text-3xl font-bold text-slate-900 dark:text-white">Бесплатно</span>
                                </template>
                            </div>
                            <template x-if="selectedPlan && selectedPlan.price > 0">
                                <p class="text-sm text-slate-600 dark:text-slate-400" x-text="'за ' + (selectedPlan.interval === 'monthly' ? 'месяц' : 'год')"></p>
                            </template>
                            <template x-if="selectedPlan && selectedPlan.trialDays > 0 && !selectedPlan.hasUsedTrial">
                                <div class="mt-3 p-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" x-model="useTrial" checked class="rounded">
                                        <div class="flex-1">
                                            <p class="text-sm text-green-600 dark:text-green-400 font-medium">
                                                <i class="fa-solid fa-gift mr-1"></i>
                                                <span x-text="selectedPlan.trialDays + ' ' + (selectedPlan.trialDays === 1 ? 'день' : (selectedPlan.trialDays < 5 ? 'дня' : 'дней')) + ' пробного периода'"></span>
                                            </p>
                                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">Пробный период начнется сразу после активации</p>
                                        </div>
                                    </label>
                                </div>
                            </template>
                            <template x-if="selectedPlan && selectedPlan.hasUsedTrial && selectedPlan.price > 0">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Пробный период уже использован
                                </p>
                            </template>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-indigo-900 dark:text-indigo-300 mb-1">Вы уверены, что хотите активировать этот тариф?</p>
                                    <template x-if="selectedPlan && selectedPlan.price > 0">
                                        <p class="text-xs text-indigo-700 dark:text-indigo-400">
                                            Тариф будет активирован сразу после подтверждения.
                                            <template x-if="currentPlanPrice > 0 && selectedPlan.price < currentPlanPrice">
                                                <span class="block mt-1 font-medium text-indigo-800 dark:text-indigo-300">Оплаченное время текущего тарифа сохранится до конца периода, затем применятся лимиты нового тарифа.</span>
                                            </template>
                                            <template x-if="selectedPlan.trialDays > 0 && !selectedPlan.hasUsedTrial">
                                                <span> Пробный период начнется сразу.</span>
                                            </template>
                                            <template x-if="selectedPlan.hasUsedTrial">
                                                <span> Пробный период для этого тарифа уже был использован ранее.</span>
                                            </template>
                                        </p>
                                    </template>
                                    <template x-if="!selectedPlan || !selectedPlan.price || selectedPlan.price === 0">
                                        <p class="text-xs text-indigo-700 dark:text-indigo-400">
                                            Переход на бесплатный тариф. Будут применены ограничения по лимитам. Тариф активируется сразу после подтверждения.
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-5 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 flex items-center justify-end gap-3 shrink-0">
                    <button @click="closeConfirmModal()"
                        class="px-4 py-2.5 min-h-[44px] text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                        Отмена
                    </button>
                    <button @click="confirmSubscription()"
                        class="px-6 py-2.5 min-h-[44px] text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-check-circle mr-2"></i>
                        Подтвердить активацию
                    </button>
                </div>
            </div>
        </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.subscription-form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const button = form.querySelector('.subscription-submit-btn');
            const btnText = button?.querySelector('.btn-text');
            if (button && btnText) {
                button.disabled = true;
                btnText.innerHTML = '<svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Обработка...';
            }
        });
    });
});
</script>
@endpush
@endsection
