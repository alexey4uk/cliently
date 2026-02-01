<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class NotifyTrialEnding extends Command
{
    protected $signature = "subscription:notify-trial-ending";

    protected $description = "Уведомить пользователей о скором окончании пробного периода (за 1-2 дня)";

    public function handle(): int
    {
        $this->info(
            "Поиск подписок с пробным периодом, заканчивающимся через 1-2 дня...",
        );

        $subscriptions = Subscription::where("status", "trial")
            ->whereNotNull("trial_ends_at")
            ->where("trial_ends_at", ">", now())
            ->where("trial_ends_at", "<=", now()->addDays(2))
            ->with(["user", "plan"])
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            SubscriptionNotificationService::notifyTrialEnding($subscription);
        }

        $this->info("Обработано подписок: {$count}");

        return self::SUCCESS;
    }
}
