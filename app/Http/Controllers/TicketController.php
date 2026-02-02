<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketCommentRequest;
use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Services\BusinessRolePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        $settings = config('tickets');

        if (! $settings['enabled']) {
            return redirect()->route('dashboard')
                ->with('info', 'Система тикетов отключена.');
        }

        // Клиенты видят только свои тикеты (созданные ими)
        // Пользователи с правами tickets.view видят все тикеты через админ-панель
        $query = $business->tickets()
            ->where(function ($q) use ($user) {
                $q->where('created_by_type', 'user')
                    ->where('created_by_id', $user->id);
            })
            ->with(['category', 'assignedUser', 'client'])
            ->withCount('comments');

        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Фильтр по категории
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Количество на страницу
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [15, 30, 50]) ? $perPage : 15;

        $tickets = $query->paginate($perPage)->withQueryString();
        $categories = \App\Models\TicketCategory::where('is_active', true)->orderBy('sort_order')->get();

        $role = $this->getCurrentBusinessRole();
        $permissionService = app(BusinessRolePermissionService::class);
        $canViewTickets = $role && $permissionService->hasPermission($role->id, 'client.tickets.view');
        $canCreateTickets = $role && $permissionService->hasPermission($role->id, 'client.tickets.create');

        return view('tickets.index', [
            'business' => $business,
            'tickets' => $tickets,
            'categories' => $categories,
            'search' => $request->get('search', ''),
            'status' => $request->get('status', ''),
            'category_id' => $request->get('category_id', ''),
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
            'canViewTickets' => $canViewTickets,
            'canCreateTickets' => $canCreateTickets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        $settings = config('tickets');

        if (! $settings['enabled']) {
            return redirect()->route('tickets.index')
                ->with('info', 'Система тикетов отключена.');
        }

        $categories = \App\Models\TicketCategory::where('is_active', true)->orderBy('sort_order')->get();
        $clients = $business->clients()->orderBy('first_name')->get();

        return view('tickets.create', [
            'business' => $business,
            'categories' => $categories,
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        $settings = config('tickets');

        if (! $settings['enabled']) {
            return redirect()->route('tickets.index')
                ->with('error', 'Система тикетов отключена.');
        }

        $validated = $request->validated();

        $ticket = Ticket::create([
            'business_id' => $business->id,
            'user_id' => $user->id, // ID пользователя, создающего тикет
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => 'medium',
            'status' => 'new',
            'created_by_type' => 'user',
            'created_by_id' => $user->id,
            'assigned_to' => null,
        ]);

        // Обработка загрузки файлов
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_type' => 'user',
                    'uploaded_by_id' => $user->id,
                ]);
            }
        }

        // Отправляем уведомление о создании тикета (если тикет назначен)
        if ($ticket->assigned_to) {
            $notificationService = new \App\Services\TicketNotificationService;
            $notificationService->notifyTicketCreated($ticket);
        }

        \App\Services\AdminNotificationService::notifyTicketCreated($ticket);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Тикет успешно создан.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Валидация ID
        if (! is_numeric($id)) {
            return redirect()->route('tickets.index')
                ->with('error', 'Неверный идентификатор тикета.');
        }

        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Ищем тикет с проверкой принадлежности к бизнесу и пользователю
        $ticket = Ticket::where('id', (int) $id)
            ->where('business_id', $business->id)
            ->where('created_by_type', 'user')
            ->where('created_by_id', $user->id)
            ->first();

        if (! $ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Тикет не найден или у вас нет доступа к этому тикету.');
        }

        // Загружаем только публичные комментарии для клиента (не внутренние)
        $ticket->load(['category', 'assignedUser', 'client', 'attachments']);
        $ticket->load(['comments' => function ($query) {
            $query->where('is_internal', false)->with(['user', 'attachments']);
        }]);

        return view('tickets.show', [
            'business' => $business,
            'ticket' => $ticket,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Ищем тикет с проверкой принадлежности к бизнесу и пользователю
        $ticket = Ticket::where('id', $id)
            ->where('business_id', $business->id)
            ->where('created_by_type', 'user')
            ->where('created_by_id', $user->id)
            ->first();

        if (! $ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Тикет не найден или у вас нет доступа к этому тикету.');
        }

        $categories = \App\Models\TicketCategory::where('is_active', true)->orderBy('sort_order')->get();
        $clients = $business->clients()->orderBy('first_name')->get();

        return view('tickets.edit', [
            'business' => $business,
            'ticket' => $ticket,
            'categories' => $categories,
            'clients' => $clients,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketRequest $request, $id)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Ищем тикет с проверкой принадлежности к бизнесу и пользователю
        $ticket = Ticket::where('id', $id)
            ->where('business_id', $business->id)
            ->where('created_by_type', 'user')
            ->where('created_by_id', $user->id)
            ->first();

        if (! $ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Тикет не найден или у вас нет доступа к этому тикету.');
        }

        $validated = $request->validated();

        $ticket->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'] ?? null,
            'user_id' => $validated['user_id'] ?? $ticket->user_id,
        ]);

        // Обработка загрузки новых файлов
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_type' => 'user',
                    'uploaded_by_id' => $user->id,
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Тикет успешно обновлен.');
    }

    /**
     * Add a comment to the ticket.
     */
    public function addComment(TicketCommentRequest $request, $id)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Ищем тикет с проверкой принадлежности к бизнесу и пользователю
        $ticket = Ticket::where('id', $id)
            ->where('business_id', $business->id)
            ->where('created_by_type', 'user')
            ->where('created_by_id', $user->id)
            ->first();

        if (! $ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Тикет не найден или у вас нет доступа к этому тикету.');
        }

        $validated = $request->validated();

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
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
                    'uploaded_by_id' => $user->id,
                ]);
            }
        }

        // Обновляем статус тикета на "в работе", если он был "новый"
        if ($ticket->status === 'new') {
            $ticket->update(['status' => 'open']);
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Комментарий добавлен.');
    }
}
