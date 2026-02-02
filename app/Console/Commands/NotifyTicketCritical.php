<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class NotifyTicketCritical extends Command
{
    protected $signature = 'ticket:notify-critical';

    protected $description = 'Уведомить админов о тикетах без ответа более 24 часов';

    public function handle(): int
    {
        $this->info('Поиск тикетов без ответа более 24 часов...');

        $threshold = now()->subHours(24);
        $tickets = Ticket::where('created_by_type', 'user')
            ->where('created_at', '<', $threshold)
            ->whereNotIn('status', ['closed', 'resolved'])
            ->whereDoesntHave('comments')
            ->get();

        $count = 0;
        foreach ($tickets as $ticket) {
            AdminNotificationService::notifyTicketCritical($ticket);
            $count++;
        }

        $this->info("Обработано тикетов: {$count}");

        return self::SUCCESS;
    }
}
