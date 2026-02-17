<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ResetMonthlyUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:reset-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обеспечить записи usage для текущего периода (от даты начала подписки) и удалить старые';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаю сброс месячных метрик...');

        $subscriptionService = app(SubscriptionService::class);

        // Получаем все активные подписки (статусами управляет крон)
        $subscriptions = Subscription::whereIn('status', ['active', 'trial'])
            ->with('user')
            ->get();

        $count = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if ($user) {
                $subscriptionService->resetMonthlyUsage($user);
                $count++;
            }
        }

        $this->info("Обработано подписок: {$count}");
        $this->info('Сброс месячных метрик завершен.');
    }
}
