<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyReengagement extends Command
{
    protected $signature = 'appointment:notify-reengagement
                            {--days= : Напоминать клиентам, у которых последний визит был N дней назад (переопределяет config)}
                            {--cooldown= : Не слать одному клиенту чаще чем раз в N дней (переопределяет config)}
                            {--dry-run : Показать, кому бы отправили, без отправки}';

    protected $description = 'Отправить клиентам приглашение записаться снова (Telegram)';

    public function handle(): int
    {
        $daysSinceLast = $this->option('days') !== null
            ? (int) $this->option('days')
            : config('notifications.reengagement.days_since_last', 30);
        $cooldownDays = $this->option('cooldown') !== null
            ? (int) $this->option('cooldown')
            : config('notifications.reengagement.cooldown_days', 30);

        $lastCompletedBefore = Carbon::now()->subDays($daysSinceLast);
        $cooldownBefore = Carbon::now()->subDays($cooldownDays);

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('Режим dry-run: сообщения не отправляются.');
        }

        $sent = 0;
        $businesses = Business::all();

        foreach ($businesses as $business) {
            $clientIdsWithOldLastVisit = Appointment::query()
                ->where('business_id', $business->id)
                ->where('status', 'completed')
                ->groupBy('client_id')
                ->havingRaw('MAX(date) <= ?', [$lastCompletedBefore->toDateString()])
                ->pluck('client_id');

            if ($clientIdsWithOldLastVisit->isEmpty()) {
                continue;
            }

            $clientsWithUpcoming = Appointment::query()
                ->where('business_id', $business->id)
                ->whereIn('client_id', $clientIdsWithOldLastVisit)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) {
                    $q->whereDate('date', '>', today())
                        ->orWhere(function ($q2) {
                            $q2->whereDate('date', today())
                                ->whereTime('time', '>', now()->format('H:i:s'));
                        });
                })
                ->pluck('client_id')
                ->unique();

            $candidateIds = $clientIdsWithOldLastVisit->diff($clientsWithUpcoming);

            $clients = Client::query()
                ->where('business_id', $business->id)
                ->whereIn('id', $candidateIds)
                ->whereNotNull('telegram_user_id')
                ->where(function ($q) use ($cooldownBefore) {
                    $q->whereNull('last_reengagement_sent_at')
                        ->orWhere('last_reengagement_sent_at', '<', $cooldownBefore);
                })
                ->get();

            foreach ($clients as $client) {
                if ($dryRun) {
                    $this->line("  — {$business->name}: {$client->full_name} (ID {$client->id})");
                    $sent++;
                } elseif (TelegramNotificationService::sendReengagementToClient($client, $business)) {
                    $client->update(['last_reengagement_sent_at' => Carbon::now()]);
                    $sent++;
                }
            }
        }

        $this->info($dryRun
            ? "Бы бы отправили приглашений: {$sent}."
            : "Приглашений «повторная запись» отправлено: {$sent}.");

        return self::SUCCESS;
    }
}
