<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\SubscriptionMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of plans.
     */
    public function index()
    {
        $search = request('search', '');
        $status = request('status', '');
        $price = request('price', '');
        $interval = request('interval', '');
        $sort = request('sort', 'sort_order');
        $direction = request('direction', 'asc');
        $perPage = request('per_page', 20);

        $query = Plan::withCount(['features', 'subscriptions']);

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Фильтр по статусу
        if ($status !== '') {
            $query->where('is_active', $status === 'active');
        }

        // Фильтр по цене
        if ($price === 'free') {
            $query->where(function ($q) {
                $q->whereNull('price')->orWhere('price', 0);
            });
        } elseif ($price === 'paid') {
            $query->where('price', '>', 0);
        }

        // Фильтр по интервалу
        if ($interval) {
            $query->where('interval', $interval);
        }

        // Сортировка
        $allowedSorts = ['name', 'price', 'sort_order', 'created_at'];
        if (in_array($sort, $allowedSorts) && $sort !== 'sort_order') {
            $query->orderBy('sort_order', 'asc')->orderBy($sort, $direction);
        } else {
            $query->orderBy('sort_order', 'asc');
        }

        $plans = $query->with('features')->paginate($perPage)->withQueryString();

        return view('panel.plans.index', compact(
            'plans',
            'search',
            'status',
            'price',
            'interval',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        $availableFeatures = [
            'integer' => SubscriptionMetric::where('type', 'integer')
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('key')
                ->map(fn ($m) => [
                    'label' => $m->label,
                    'description' => $m->description,
                    'icon' => $m->icon,
                ])
                ->toArray(),
            'boolean' => SubscriptionMetric::where('type', 'boolean')
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('key')
                ->map(fn ($m) => [
                    'label' => $m->label,
                    'description' => $m->description,
                    'icon' => $m->icon,
                ])
                ->toArray(),
        ];

        return view('panel.plans.create', compact('availableFeatures'));
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:plans,slug',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'interval' => 'required|in:monthly,yearly',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'nullable|integer',
            'features' => 'nullable|array',
            'features.*.key' => 'required|string',
            'features.*.value' => 'nullable|string', // Разрешаем пустое значение для integer
            'features.*.type' => 'required|in:integer,boolean',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        $plan = Plan::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'interval' => $validated['interval'],
            'trial_days' => $validated['trial_days'] ?? 0,
            // Для чекбоксов: если не передано, значит false (чекбокс не отмечен)
            'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : false,
            'is_default' => isset($validated['is_default']) ? (bool) $validated['is_default'] : false,
            // Автоматически назначаем sort_order (максимальное значение + 1)
            'sort_order' => (Plan::max('sort_order') ?? -1) + 1,
        ]);

        // Если этот тариф помечен как "по умолчанию", снимаем флаг с остальных
        if ($plan->is_default) {
            Plan::where('id', '!=', $plan->id)->update(['is_default' => false]);
        }

        // Создаем метрики
        if (! empty($validated['features'])) {
            foreach ($validated['features'] as $feature) {
                $featureType = $feature['type'] ?? 'integer';
                $featureValue = '';

                if (isset($feature['value']) && $feature['value'] !== null && $feature['value'] !== '') {
                    $featureValue = trim((string) $feature['value']);

                    if ($featureType === 'integer') {
                        // Если это boolean-подобное значение, очищаем
                        if ($featureValue === 'false' || $featureValue === 'true') {
                            $featureValue = '';
                        }
                        // Проверяем, что значение является числом
                        elseif ($featureValue !== '' && ! is_numeric($featureValue)) {
                            $featureValue = '';
                        }
                    } elseif ($featureType === 'boolean') {
                        // Для boolean типа нормализуем значение
                        if ($featureValue === '1' || $featureValue === 1 || $featureValue === 'true' || $featureValue === true) {
                            $featureValue = 'true';
                        } else {
                            $featureValue = 'false';
                        }
                    }
                } elseif ($featureType === 'boolean') {
                    // Для boolean типа, если значение не передано, устанавливаем false
                    $featureValue = 'false';
                }

                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_key' => $feature['key'],
                    'feature_value' => $featureValue,
                    'feature_type' => $featureType,
                ]);
            }
        }

        return redirect()->route('panel.plans.index')
            ->with('success', 'Тариф успешно создан');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        $plan->load('features');

        $availableFeatures = [
            'integer' => SubscriptionMetric::where('type', 'integer')
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('key')
                ->map(fn ($m) => [
                    'label' => $m->label,
                    'description' => $m->description,
                    'icon' => $m->icon,
                ])
                ->toArray(),
            'boolean' => SubscriptionMetric::where('type', 'boolean')
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('key')
                ->map(fn ($m) => [
                    'label' => $m->label,
                    'description' => $m->description,
                    'icon' => $m->icon,
                ])
                ->toArray(),
        ];

        return view('panel.plans.edit', compact('plan', 'availableFeatures'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:plans,slug,'.$plan->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'interval' => 'required|in:monthly,yearly',
            'trial_days' => 'nullable|integer|min:0',
            // Чекбоксы могут быть не переданы, если не отмечены
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'features' => 'nullable|array',
            'features.*.key' => 'required|string',
            'features.*.value' => 'nullable|string', // Разрешаем пустое значение для integer
            'features.*.type' => 'nullable|string|in:integer,boolean',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        $wasDefault = $plan->is_default;
        // Для чекбокса is_default: если не передано, значит false (чекбокс не отмечен)
        $willBeDefault = isset($validated['is_default']) ? (bool) $validated['is_default'] : false;

        $plan->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'interval' => $validated['interval'],
            'trial_days' => $validated['trial_days'] ?? 0,
            // Для чекбоксов: если не передано, значит false (чекбокс не отмечен)
            'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : false,
            'is_default' => $willBeDefault,
            // sort_order не обновляется через форму, только через стрелки в таблице
        ]);

        // Если этот тариф помечен как "по умолчанию", снимаем флаг с остальных
        if ($willBeDefault && ! $wasDefault) {
            Plan::where('id', '!=', $plan->id)->update(['is_default' => false]);
        }

        // Обновляем метрики
        if (isset($validated['features']) && is_array($validated['features']) && ! empty($validated['features'])) {
            // Удаляем старые метрики
            $plan->features()->delete();

            // Создаем новые
            foreach ($validated['features'] as $index => $feature) {
                if (isset($feature['key'])) {
                    // Убеждаемся, что тип валидный
                    $featureType = 'integer'; // По умолчанию

                    // Если тип передан из формы, используем его
                    if (isset($feature['type']) && ! empty($feature['type']) && in_array($feature['type'], ['integer', 'boolean'])) {
                        $featureType = $feature['type'];
                    } else {
                        // Если тип не передан, пытаемся определить из subscription_metrics
                        $metric = \App\Models\SubscriptionMetric::where('key', $feature['key'])->first();
                        if ($metric) {
                            $featureType = $metric->type;
                        }
                    }

                    // Обрабатываем значение в зависимости от типа
                    $featureValue = '';
                    if (isset($feature['value']) && $feature['value'] !== null && $feature['value'] !== '') {
                        $featureValue = trim((string) $feature['value']);

                        if ($featureType === 'integer') {
                            // Если это boolean-подобное значение, очищаем
                            if ($featureValue === 'false' || $featureValue === 'true') {
                                $featureValue = '';
                            }
                            // Проверяем, что значение является числом (включая отрицательные и 0)
                            elseif ($featureValue !== '' && ! is_numeric($featureValue)) {
                                $featureValue = '';
                            }
                            // Если значение валидное число, оставляем как есть
                        } elseif ($featureType === 'boolean') {
                            // Для boolean типа нормализуем значение
                            if ($featureValue === '1' || $featureValue === 1 || $featureValue === 'true' || $featureValue === true) {
                                $featureValue = 'true';
                            } else {
                                $featureValue = 'false';
                            }
                        }
                    } elseif ($featureType === 'boolean') {
                        // Для boolean типа, если значение не передано, устанавливаем false
                        $featureValue = 'false';
                    }

                    PlanFeature::create([
                        'plan_id' => $plan->id,
                        'feature_key' => $feature['key'],
                        'feature_value' => $featureValue,
                        'feature_type' => $featureType,
                    ]);
                }
            }
        } elseif (! isset($validated['features'])) {
            // Если features не переданы в запросе, удаляем все существующие
            $plan->features()->delete();
        }

        return redirect()->route('panel.plans.index')
            ->with('success', 'Тариф успешно обновлен');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Plan $plan)
    {
        // Проверяем, есть ли активные подписки на этот тариф
        if ($plan->subscriptions()->whereIn('status', ['active', 'trial'])->exists()) {
            return redirect()->route('panel.plans.index')
                ->with('error', 'Невозможно удалить тариф, на который есть активные подписки.');
        }

        $plan->delete();

        return redirect()->route('panel.plans.index')
            ->with('success', 'Тариф успешно удален');
    }

    /**
     * Move plan down (increment sort order).
     * Swaps with the element below.
     */
    public function incrementSortOrder(Plan $plan)
    {
        // Находим элемент с sort_order на 1 больше (ниже в списке)
        $nextPlan = Plan::where('sort_order', $plan->sort_order + 1)->first();

        if ($nextPlan) {
            // Меняем местами
            $currentOrder = $plan->sort_order;
            $plan->update(['sort_order' => $nextPlan->sort_order]);
            $nextPlan->update(['sort_order' => $currentOrder]);
        }

        return redirect()->back()
            ->with('success', 'Элемент перемещен вниз.');
    }

    /**
     * Move plan up (decrement sort order).
     * Swaps with the element above.
     */
    public function decrementSortOrder(Plan $plan)
    {
        // Находим элемент с sort_order на 1 меньше (выше в списке)
        $prevPlan = Plan::where('sort_order', $plan->sort_order - 1)->first();

        if ($prevPlan) {
            // Меняем местами
            $currentOrder = $plan->sort_order;
            $plan->update(['sort_order' => $prevPlan->sort_order]);
            $prevPlan->update(['sort_order' => $currentOrder]);
        }

        return redirect()->back()
            ->with('success', 'Элемент перемещен вверх.');
    }

    /**
     * Reorder plans based on drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:plans,id',
            'order.*.sort_order' => 'required|integer|min:1',
        ]);

        foreach ($request->order as $item) {
            Plan::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
