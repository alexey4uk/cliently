<section id="pricing" class="py-16 sm:py-20 md:py-24 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-12">
            <h2 class="landing-heading landing-section-title text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Тарифы</h2>
            <p class="landing-section-lead text-base sm:text-lg text-gray-600 dark:text-gray-400 mx-auto">
                Прозрачные тарифы — от бесплатного старта до полного набора возможностей
            </p>
        </div>

        @php
            $planCount = $plans->count();
            // Сетка под любое количество тарифов: 1 — центр, 2 — две колонки, 3 — три, 4 — 2×2 или 4 в ряд на xl, 5+ — 2/3 колонки
            $containerClass = match ($planCount) {
                1 => 'flex flex-col items-center max-w-2xl mx-auto',
                2 => 'grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 max-w-4xl mx-auto',
                3 => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 max-w-5xl mx-auto',
                4 => 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 md:gap-8',
                5 => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 max-w-6xl mx-auto',
                default => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8',
            };
        @endphp
        <div class="{{ $containerClass }}">
            @foreach($plans as $plan)
                @php
                    $isPopular = $plan->slug === 'basic';
                @endphp
                <div class="relative flex flex-col {{ $planCount === 1 ? 'w-full max-w-md' : '' }} {{ $isPopular && $planCount > 1 ? 'lg:-mt-2 lg:mb-2 z-10' : '' }}">
                    @if($isPopular)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-20">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-full shadow">
                                Популярный
                            </span>
                        </div>
                    @endif

                    <div class="flex flex-col h-full bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden {{ $isPopular ? 'ring-2 ring-indigo-500 dark:ring-indigo-500' : '' }}">
                        <div class="px-5 sm:px-6 pt-6 sm:pt-8 pb-4 sm:pb-5 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $plan->name }}</h3>
                            @if($plan->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $plan->description }}</p>
                            @endif
                        </div>

                        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-baseline gap-2 mb-1">
                                @if($plan->price)
                                    <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                                    <span class="text-base text-gray-600 dark:text-gray-400">BYN</span>
                                @else
                                    <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Бесплатно</span>
                                @endif
                            </div>
                            @if($plan->price)
                                <p class="text-sm text-gray-500 dark:text-gray-400">за {{ $plan->interval === 'monthly' ? 'месяц' : 'год' }}</p>
                            @endif
                            @if($plan->trial_days > 0 && $plan->price)
                                <p class="text-sm text-green-600 dark:text-green-400 font-medium mt-2">
                                    {{ $plan->trial_days }} {{ $plan->trial_days === 1 ? 'день' : ($plan->trial_days < 5 ? 'дня' : 'дней') }} пробного периода
                                </p>
                            @endif
                        </div>

                        <div class="flex-1 px-5 sm:px-6 py-4 sm:py-5 space-y-3">
                            @foreach($basicMetricsList as $metric)
                                @php
                                    $value = $plan->getFeatureValue($metric->key);
                                    if ($value === null) continue;
                                    $displayValue = match (true) {
                                        $value === -1 => 'Безлимит',
                                        $value === 0 => '—',
                                        $value === true => '✓',
                                        $value === false => '✗',
                                        is_numeric($value) => number_format($value, 0, ',', ' '),
                                        default => $value,
                                    };
                                @endphp
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 pr-2">{{ $metric->label }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white shrink-0" @if($value === 0) aria-label="Не доступно" @endif>{{ $displayValue }}</span>
                                </div>
                            @endforeach

                            @if($advancedMetricsList->count() > 0)
                                <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700 space-y-2">
                                    @foreach($advancedMetricsList as $metric)
                                        @php
                                            $value = $plan->getFeatureValue($metric->key);
                                            if ($value === null) continue;
                                            $hasFeature = $value === true;
                                        @endphp
                                        <div class="flex items-center gap-2 text-sm">
                                            @if($hasFeature)
                                                <x-icon name="check-circle" variant="outline" size="sm" class="text-green-500 dark:text-green-400 shrink-0" />
                                            @else
                                                <x-icon name="x-mark" variant="outline" size="sm" class="text-gray-300 dark:text-gray-600 shrink-0" />
                                            @endif
                                            <span class="text-gray-600 dark:text-gray-400">{{ $metric->label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="px-5 sm:px-6 pb-5 sm:pb-6 pt-4">
                            @if(!$plan->price)
                                @auth
                                    <a href="{{ route('dashboard') }}" class="block w-full py-3 text-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                        Перейти в панель
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="block w-full py-3 text-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                        Начать бесплатно
                                    </a>
                                @endauth
                            @else
                                <a href="{{ route('subscription.show', $plan) }}" class="block w-full py-3 text-center text-sm font-semibold text-indigo-600 dark:text-indigo-400 border border-indigo-600 dark:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                                    Выбрать тариф
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
