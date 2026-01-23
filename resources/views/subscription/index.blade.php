@extends('layouts.user')

@section('title', 'Тарифы - Cliently')
@section('page-title', 'Выбор тарифа')
@section('page-description', 'Выберите подходящий тариф для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Тарифы']]" />
@endpush

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Заголовок -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Выберите тариф</h1>
        <p class="text-lg text-slate-600 dark:text-slate-400">Выберите подходящий тариф для вашего бизнеса</p>
    </div>

    <!-- Карточки тарифов -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($plans as $plan)
            @php
                $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                $planColor = match($plan->slug) {
                    'free' => 'gray',
                    'basic' => 'blue',
                    'pro' => 'amber',
                    default => 'indigo'
                };
                $isRecommended = $plan->slug === 'basic';
            @endphp
            <div class="relative bg-white dark:bg-slate-900 rounded-xl border-2 shadow-lg transition-all duration-200 hover:shadow-xl
                {{ $isCurrent ? 'border-indigo-500 dark:border-indigo-400' : 'border-slate-200 dark:border-slate-800' }}
                {{ $isRecommended ? 'ring-2 ring-blue-500 ring-opacity-50' : '' }}">
                
                @if($isCurrent)
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 dark:bg-indigo-500/20 dark:text-indigo-300 rounded-full">
                            <i class="fa-solid fa-check-circle"></i>
                            Текущий тариф
                        </span>
                    </div>
                @endif

                @if($isRecommended && !$isCurrent)
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300 rounded-full">
                            Рекомендуется
                        </span>
                    </div>
                @endif

                <div class="p-6">
                    <!-- Название и цена -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ $plan->name }}</h3>
                        <div class="flex items-baseline gap-2">
                            @if($plan->price)
                                <span class="text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                                <span class="text-slate-600 dark:text-slate-400">BYN/{{ $plan->interval === 'monthly' ? 'мес' : 'год' }}</span>
                            @else
                                <span class="text-4xl font-bold text-slate-900 dark:text-white">Бесплатно</span>
                            @endif
                        </div>
                        @if($plan->description)
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">{{ $plan->description }}</p>
                        @endif
                    </div>

                    <!-- Метрики -->
                    <div class="space-y-3 mb-6">
                        @foreach($metrics as $metric)
                            @php
                                $value = $plan->getFeatureValue($metric->key);
                                $displayValue = match(true) {
                                    $value === -1 => 'Безлимит',
                                    $value === true => 'Включено',
                                    $value === false => 'Отключено',
                                    is_numeric($value) => number_format($value, 0, ',', ' '),
                                    default => $value
                                };
                            @endphp
                            @if($value !== null)
                                <div class="flex items-center gap-3">
                                    <i class="{{ $metric->icon ?? 'fa-solid fa-circle' }} text-slate-500 dark:text-slate-400 w-5"></i>
                                    <span class="text-sm text-slate-700 dark:text-slate-300 flex-1">{{ $metric->label }}</span>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $displayValue }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Кнопка -->
                    <form action="{{ route('subscription.subscribe', $plan) }}" method="POST">
                        @csrf
                        @if($isCurrent)
                            <button type="button" disabled
                                class="w-full py-3 px-4 bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-lg font-medium cursor-not-allowed">
                                Текущий тариф
                            </button>
                        @else
                            <button type="submit"
                                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                Выбрать тариф
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Сравнительная таблица -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Сравнение тарифов</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Функция</th>
                        @foreach($plans as $plan)
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                {{ $plan->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($metrics as $metric)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $metric->label }}</td>
                            @foreach($plans as $plan)
                                @php
                                    $value = $plan->getFeatureValue($metric->key);
                                    $displayValue = match(true) {
                                        $value === -1 => 'Безлимит',
                                        $value === true => '<i class="fa-solid fa-check text-green-600"></i>',
                                        $value === false => '<i class="fa-solid fa-times text-red-600"></i>',
                                        is_numeric($value) => number_format($value, 0, ',', ' '),
                                        default => '-'
                                    };
                                @endphp
                                <td class="px-6 py-4 text-sm text-center text-slate-700 dark:text-slate-300">
                                    {!! $displayValue !!}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
