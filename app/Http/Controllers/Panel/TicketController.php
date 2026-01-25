<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketCommentRequest;
use App\Models\Business;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\User;
use App\Services\TicketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);
        $statusFilter = request('status', '');
        $businessFilter = request('business_id', '');
        $categoryFilter = request('category_id', '');
        $assignedFilter = request('assigned_to', '');

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

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Фильтр по категории
        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        // Фильтр по назначенному пользователю
        if ($assignedFilter) {
            if ($assignedFilter === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $assignedFilter);
            }
        }

        // Сортировка
        $allowedSorts = ['created_at', 'title', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate($perPage)->withQueryString();

        // Получаем данные для фильтров
        $businesses = Business::orderBy('name')->get();
        $categories = TicketCategory::where('is_active', true)->orderBy('sort_order')->get();
        // Только системные пользователи (с доступом к админ-панели) для назначения на тикеты
        $users = User::permission('panel.access')->orderBy('name')->get();

        return view('panel.tickets.index', compact(
            'tickets',
            'search',
            'sort',
            'direction',
            'perPage',
            'statusFilter',
            'businessFilter',
            'categoryFilter',
            'assignedFilter',
            'businesses',
            'categories',
            'users'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'business',
            'category',
            'assignedUser',
            'client',
            'attachments',
            'comments.user',
            'comments.attachments',
        ]);

        // Только системные пользователи (с доступом к админ-панели) для назначения на тикеты
        $users = User::permission('panel.access')->orderBy('name')->get();
        $categories = TicketCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('panel.tickets.show', compact('ticket', 'users', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $ticket->load(['business', 'category', 'assignedUser', 'client']);

        $categories = TicketCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        // Только системные пользователи (с доступом к админ-панели) для назначения на тикеты
        $users = User::permission('panel.access')->orderBy('name')->get();
        $clients = \App\Models\Client::where('business_id', $ticket->business_id)
            ->orderBy('first_name')
            ->get();

        return view('panel.tickets.edit', compact('ticket', 'categories', 'users', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:ticket_categories,id'],
            'status' => ['required', 'in:new,in_progress,resolved,closed'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ]);

        // Если у пользователя нет права на назначение, не обновляем assigned_to
        if (! Auth::user()->can('panel.tickets.assign')) {
            unset($validated['assigned_to']);
        }

        $ticket->update($validated);

        return redirect()->route('panel.tickets.show', $ticket)
            ->with('success', 'Тикет успешно обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('panel.tickets.index')
            ->with('success', 'Тикет успешно удален.');
    }

    /**
     * Assign ticket to user.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $oldAssignedTo = $ticket->assigned_to;

        $ticket->update([
            'assigned_to' => $validated['assigned_to'] ?? null,
            'status' => $validated['assigned_to'] ? 'in_progress' : $ticket->status,
        ]);

        // Отправляем уведомление о назначении
        if ($validated['assigned_to'] && $validated['assigned_to'] !== $oldAssignedTo) {
            $assignedUser = User::find($validated['assigned_to']);
            if ($assignedUser) {
                $notificationService = new TicketNotificationService;
                $notificationService->notifyTicketAssigned($ticket, $assignedUser);
            }
        }

        return redirect()->back()->with('success', 'Тикет успешно назначен.');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,resolved,closed'],
        ]);

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $validated['status']]);

        // Отправляем уведомление об изменении статуса
        if ($oldStatus !== $validated['status']) {
            $notificationService = new TicketNotificationService;
            $notificationService->notifyStatusChanged($ticket, $oldStatus, $validated['status']);
        }

        return redirect()->back()->with('success', 'Статус тикета обновлен.');
    }

    /**
     * Add a comment to the ticket.
     */
    public function addComment(TicketCommentRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        // Обработка загрузки файлов к комментарию
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'comment_id' => $comment->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_type' => 'user',
                    'uploaded_by_id' => Auth::id(),
                ]);
            }
        }

        // Обновляем статус тикета на "в работе", если он был "новый"
        if ($ticket->status === 'new') {
            $ticket->update(['status' => 'in_progress']);
            // Обновляем объект в памяти
            $ticket->refresh();
        }

        // Отправляем уведомление о новом комментарии
        $notificationService = new TicketNotificationService;
        $notificationService->notifyCommentAdded($ticket, $comment);

        return redirect()->route('panel.tickets.show', $ticket)->with('success', 'Комментарий добавлен.');
    }
}
