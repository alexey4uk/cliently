<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class CancelExpiredPendingAppointments extends Command
{
    protected $signature = 'appointment:cancel-expired-pending
                            {--dry-run : Показать, какие записи будут отменены, без изменений}';

    protected $description = 'Автоматически отменить записи в статусе «ожидает», у которых дата/время уже прошли';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('Режим dry-run: статусы не меняются.');
        }

        $query = Appointment::query()
            ->where('status', 'pending')
            ->whereDate('date', '<=', today());

        $cancelled = 0;
        $query->chunk(100, function ($appointments) use ($dryRun, &$cancelled) {
            foreach ($appointments as $appointment) {
                if (! $appointment->dateTime->isPast()) {
                    continue;
                }
                if ($dryRun) {
                    $this->line("  — Запись #{$appointment->id}: {$appointment->date->format('d.m.Y')} {$appointment->time}, клиент ID {$appointment->client_id}");
                    $cancelled++;
                } else {
                    $appointment->update(['status' => 'cancelled']);
                    $cancelled++;
                }
            }
        });

        $this->info($dryRun
            ? "Бы бы отменено записей: {$cancelled}."
            : "Отменено неподтверждённых записей с прошедшим временем: {$cancelled}.");

        return self::SUCCESS;
    }
}
