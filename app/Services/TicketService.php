<?php

namespace App\Services;

use App\Models\Ticket;

class TicketService
{
    /**
     * Создать тикет с автоматическим назначением, если настроено
     */
    public function createTicket(array $data, int $businessId): Ticket
    {
        $settings = config('tickets');

        // Автоматическое назначение, если включено
        if ($settings['auto_assign']['enabled'] && $settings['auto_assign']['user_id']) {
            $data['assigned_to'] = $settings['auto_assign']['user_id'];
            $data['status'] = 'open';
        }

        return Ticket::create($data);
    }

    /**
     * Назначить тикет пользователю
     */
    public function assignTicket(Ticket $ticket, ?int $userId): void
    {
        $ticket->update([
            'assigned_to' => $userId,
            'status' => $userId ? 'open' : $ticket->status,
        ]);
    }

    /**
     * Изменить статус тикета
     */
    public function updateStatus(Ticket $ticket, string $status): void
    {
        $oldStatus = $ticket->status;
        $ticket->update(['status' => $status]);

        // Автоматически устанавливаем даты при изменении статуса
        if ($status === 'resolved' && ! $ticket->resolved_at) {
            $ticket->update(['resolved_at' => now()]);
        }

        if ($status === 'closed' && ! $ticket->closed_at) {
            $ticket->update(['closed_at' => now()]);
        }
    }

    /**
     * Получить статистику тикетов для бизнеса
     */
    public function getStatsForBusiness(int $businessId): array
    {
        $tickets = Ticket::where('business_id', $businessId)->get();

        return [
            'total' => $tickets->count(),
            'new' => $tickets->where('status', 'new')->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'resolved' => $tickets->where('status', 'resolved')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
            'high_priority' => $tickets->whereIn('priority', ['high', 'critical'])->count(),
        ];
    }
}
