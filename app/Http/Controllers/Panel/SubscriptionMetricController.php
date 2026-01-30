<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\PlanFeature;
use App\Models\SubscriptionMetric;
use Illuminate\Http\Request;

class SubscriptionMetricController extends Controller
{
    /**
     * Display a listing of subscription metrics.
     */
    public function index()
    {
        $search = request('search', '');
        $type = request('type', '');
        $status = request('status', '');
        $sort = request('sort', 'sort_order');
        $direction = request('direction', 'asc');
        $perPage = request('per_page', 20);

        $query = SubscriptionMetric::query();

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтр по типу
        if ($type) {
            $query->where('type', $type);
        }

        // Фильтр по статусу
        if ($status !== '') {
            $query->where('is_active', $status === 'active');
        }

        // Сортировка
        $allowedSorts = ['key', 'label', 'type', 'is_active', 'sort_order', 'created_at'];
        if (in_array($sort, $allowedSorts) && $sort !== 'sort_order') {
            $query->orderBy('sort_order', 'asc')->orderBy($sort, $direction);
        } else {
            $query->orderBy('sort_order', 'asc');
        }

        $metrics = $query->paginate($perPage)->withQueryString();

        return view('panel.plans.metrics.index', compact(
            'metrics',
            'search',
            'type',
            'status',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new metric.
     */
    public function create()
    {
        return view('panel.plans.metrics.create');
    }

    /**
     * Store a newly created metric.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:subscription_metrics,key', 'regex:/^[a-z][a-z0-9_]*$/'],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:integer,boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Обрабатываем чекбокс is_active
        $validated['is_active'] = isset($validated['is_active']) ? (bool) $validated['is_active'] : false;

        // Автоматически назначаем sort_order (максимальное значение + 1)
        $maxSortOrder = SubscriptionMetric::max('sort_order') ?? -1;
        $validated['sort_order'] = $maxSortOrder + 1;

        SubscriptionMetric::create($validated);

        return redirect()->route('panel.plans.properties.index')
            ->with('success', 'Свойство успешно создано.');
    }

    /**
     * Show the form for editing the specified metric.
     */
    public function edit(SubscriptionMetric $metric)
    {
        return view('panel.plans.metrics.edit', compact('metric'));
    }

    /**
     * Update the specified metric.
     */
    public function update(Request $request, SubscriptionMetric $metric)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:subscription_metrics,key,'.$metric->id, 'regex:/^[a-z][a-z0-9_]*$/'],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:integer,boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Обрабатываем чекбокс is_active
        $validated['is_active'] = isset($validated['is_active']) ? (bool) $validated['is_active'] : false;
        // sort_order не обновляется через форму, только через стрелки в таблице
        unset($validated['sort_order']);

        $metric->update($validated);

        return redirect()->route('panel.plans.properties.index')
            ->with('success', 'Свойство успешно обновлено.');
    }

    /**
     * Remove the specified metric.
     */
    public function destroy(SubscriptionMetric $metric)
    {
        // Проверяем, используется ли метрика в тарифах
        $usedInPlans = PlanFeature::where('feature_key', $metric->key)->exists();

        if ($usedInPlans) {
            return redirect()->back()
                ->with('error', 'Невозможно удалить свойство, так как оно используется в тарифах.');
        }

        $metric->delete();

        return redirect()->route('panel.plans.properties.index')
            ->with('success', 'Свойство успешно удалено.');
    }

    /**
     * Move metric down (increment sort order).
     * Swaps with the element below.
     */
    public function incrementSortOrder(SubscriptionMetric $metric)
    {
        // Находим элемент с sort_order на 1 больше (ниже в списке)
        $nextMetric = SubscriptionMetric::where('sort_order', $metric->sort_order + 1)->first();

        if ($nextMetric) {
            // Меняем местами
            $currentOrder = $metric->sort_order;
            $metric->update(['sort_order' => $nextMetric->sort_order]);
            $nextMetric->update(['sort_order' => $currentOrder]);
        }

        return redirect()->back()
            ->with('success', 'Элемент перемещен вниз.');
    }

    /**
     * Move metric up (decrement sort order).
     * Swaps with the element above.
     */
    public function decrementSortOrder(SubscriptionMetric $metric)
    {
        // Находим элемент с sort_order на 1 меньше (выше в списке)
        $prevMetric = SubscriptionMetric::where('sort_order', $metric->sort_order - 1)->first();

        if ($prevMetric) {
            // Меняем местами
            $currentOrder = $metric->sort_order;
            $metric->update(['sort_order' => $prevMetric->sort_order]);
            $prevMetric->update(['sort_order' => $currentOrder]);
        }

        return redirect()->back()
            ->with('success', 'Элемент перемещен вверх.');
    }

    /**
     * Reorder metrics based on drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:subscription_metrics,id',
            'order.*.sort_order' => 'required|integer|min:1',
        ]);

        foreach ($request->order as $item) {
            SubscriptionMetric::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
