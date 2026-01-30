<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the support page with tickets.
     */
    public function index(Request $request)
    {
        $search = request('search', '');
        $statusFilter = request('status', '');

        $query = Ticket::with(['business', 'category', 'assignedUser', 'client'])
            ->withCount('comments');

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтр по статусу
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Сортировка
        $query->orderBy('created_at', 'desc');

        $tickets = $query->paginate(20)->withQueryString();

        return view('panel.support.index', [
            'tickets' => $tickets,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }
}
