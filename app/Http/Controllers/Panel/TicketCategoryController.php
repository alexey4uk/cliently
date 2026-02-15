<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketCategoryRequest;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = request('search', '');
        $sort = request('sort', 'sort_order');
        $direction = request('direction', 'asc');
        $perPage = request('per_page', 20);

        $query = TicketCategory::withCount('tickets');

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $allowedSorts = ['name', 'sort_order', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('sort_order', 'asc');
        }

        $categories = $query->paginate($perPage)->withQueryString();

        return view('panel.ticket-categories.index', compact(
            'categories',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.ticket-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketCategoryRequest $request)
    {
        $validated = $request->validated();

        TicketCategory::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('panel.ticket-categories.index')
            ->with('success', 'Категория успешно создана.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketCategory $ticketCategory)
    {
        return view('panel.ticket-categories.edit', compact('ticketCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketCategoryRequest $request, TicketCategory $ticketCategory)
    {
        $validated = $request->validated();

        $ticketCategory->update($validated);

        return redirect()->route('panel.ticket-categories.index')
            ->with('success', 'Категория успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketCategory $ticketCategory)
    {
        // Проверяем, есть ли тикеты в этой категории
        if ($ticketCategory->tickets()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Невозможно удалить категорию, так как к ней привязаны тикеты.');
        }

        $ticketCategory->delete();

        return redirect()->route('panel.ticket-categories.index')
            ->with('success', 'Категория успешно удалена.');
    }
}
