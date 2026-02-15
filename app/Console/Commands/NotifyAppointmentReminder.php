<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\AppointmentNotificationService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyAppointmentReminder extends Command
{
    protected $signature = 'appointment:notify-reminder
                            {--hours= : За сколько часов напоминать (переопределяет config)}
                            {--dry-run : Показать, кому бы отправили, без отправки}';

    protected $description = 'Отправить клиентам напоминание о предстоящей записи (Telegram)';

    public function handle(): int
    {
        $hours = $this->option('hours') !== null
            ? (int) $this->option('hours')
            : config('notifications.reminder_hours_before', 24);

        $from = now();
        $to = now()->addHours($hours);

        $appointments = Appointment::query()
            ->with(['client', 'business', 'service', 'master'])
            ->whereNull('reminder_sent_at')
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereDate('date', '>=', $from->toDateString())
            ->whereDate('date', '<=', $to->toDateString())
            ->get()
            ->filter(function (Appointment $apt) use ($from, $to) {
                $dt = $apt->dateTime;

                return $dt->gte($from) && $dt->lte($to);
            });

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('Режим dry-run: сообщения не отправляются.');
        }

        $sent = 0;
        foreach ($appointments as $appointment) {
            if (! $appointment->client->telegram_user_id) {
                continue;
            }
            if ($dryRun) {
                $this->line("  — Запись #{$appointment->id}: {$appointment->client->full_name}, {$appointment->date->format('d.m.Y')} {$appointment->time}");
                $sent++;
            } elseif (TelegramNotificationService::sendAppointmentReminderToClient($appointment)) {
                $appointment->update(['reminder_sent_at' => Carbon::now()]);
                AppointmentNotificationService::notifyUpcoming($appointment);
                $sent++;
            }
        }

        $this->info($dryRun
            ? "Бы бы отправили напоминаний: {$sent} (записей в окне: {$appointments->count()})."
            : "Напоминаний отправлено: {$sent} из {$appointments->count()} записей в ближайшие {$hours} ч.");

        return self::SUCCESS;
    }
}
