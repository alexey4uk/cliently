<?php

namespace App\Console\Commands;

use App\Models\NotificationRecord;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean
                            {--dry-run : Показать, что будет удалено, без удаления}
                            {--read-days= : Удалять прочитанные старше N дней (переопределяет config)}
                            {--any-days= : Удалять любые записи старше N дней (переопределяет config)}
                            {--chunk= : Размер пачки удаления (переопределяет config)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка старых записей из notification_records (колокольчик): прочитанные и очень старые';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $readAfterDays = $this->option('read-days') !== null
            ? (int) $this->option('read-days')
            : config('notifications.cleanup.read_after_days', 30);
        $anyAfterDays = $this->option('any-days') !== null
            ? (int) $this->option('any-days')
            : config('notifications.cleanup.any_after_days', 90);
        $chunkSize = $this->option('chunk') !== null
            ? (int) $this->option('chunk')
            : config('notifications.cleanup.chunk_size', 2000);

        if ($dryRun) {
            $this->info('Режим dry-run: удаление не выполняется.');
        }

        $readBefore = Carbon::now()->subDays($readAfterDays);
        $anyBefore = Carbon::now()->subDays($anyAfterDays);

        // 1) Прочитанные старше read_after_days (пачками — не блокируем таблицу надолго)
        $readCount = NotificationRecord::read()->where('created_at', '<', $readBefore)->count();
        if (! $dryRun && $readCount > 0) {
            $this->deleteInChunks(
                NotificationRecord::read()->where('created_at', '<', $readBefore),
                $chunkSize,
            );
        }

        // 2) Любые записи старше any_after_days (пачками)
        $anyCount = NotificationRecord::where('created_at', '<', $anyBefore)->count();
        if (! $dryRun && $anyCount > 0) {
            $this->deleteInChunks(
                NotificationRecord::where('created_at', '<', $anyBefore),
                $chunkSize,
            );
        }

        $this->info("Прочитанные старше {$readAfterDays} дн.: {$readCount} записей".($dryRun ? ' (бы бы удалено)' : ' удалено'));
        $this->info("Любые старше {$anyAfterDays} дн.: {$anyCount} записей".($dryRun ? ' (бы бы удалено)' : ' удалено'));

        $this->info('Клинер уведомлений завершён.');

        return self::SUCCESS;
    }

    /**
     * Удаление пачками по ID, чтобы не держать долгую блокировку таблицы.
     */
    private function deleteInChunks(\Illuminate\Database\Eloquent\Builder $query, int $chunkSize): void
    {
        do {
            $ids = (clone $query)->orderBy('id')->limit($chunkSize)->pluck('id');
            if ($ids->isEmpty()) {
                break;
            }
            NotificationRecord::whereIn('id', $ids)->delete();
        } while (true);
    }
}
