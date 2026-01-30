<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class NotifyBusinessInactive extends Command
{
    protected $signature = 'business:notify-inactive {--days=30 : Минимальное количество дней неактивности}';

    protected $description = 'Уведомить админов о бизнесах без активности более N дней';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Поиск бизнесов неактивных более {$days} дней...");

        $threshold = now()->subDays($days);
        $businesses = Business::where('updated_at', '<', $threshold)->get();

        $count = 0;
        foreach ($businesses as $business) {
            $key = 'admin_business_inactive_notify_'.$business->id;
            if (! Cache::add($key, 1, now()->addDays(30))) {
                continue;
            }
            $daysInactive = (int) $business->updated_at->diffInDays(now());
            AdminNotificationService::notifyBusinessInactive($business, $daysInactive);
            $count++;
        }

        $this->info("Обработано бизнесов: {$count}");

        return self::SUCCESS;
    }
}
