<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class NotifySubscriptionExpiring extends Command
{
    protected $signature = 'subscription:notify-expiring';

    protected $description = 'Уведомить админов о подписках, истекающих в ближайшие 3 дня';

    public function handle(): int
    {
        $this->info('Поиск подписок, истекающих в ближайшие 3 дня...');

        $subscriptions = Subscription::whereIn('status', ['active', 'trial'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addDays(3))
            ->with(['user', 'plan'])
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            AdminNotificationService::notifySubscriptionExpiring($subscription);
            $count++;
        }

        $this->info("Обработано подписок: {$count}");

        return self::SUCCESS;
    }
}
