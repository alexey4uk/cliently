<?php

namespace App\Jobs;

use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Notifications\BroadcastNotification;
use App\Services\BroadcastNotificationService;
use App\Services\NotificationService;
use App\Services\TelegramNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public bool $failOnTimeout = false;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public NotificationBroadcast $broadcast
    ) {
        $this->onConnection('broadcasts');
        $this->onQueue('broadcasts');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $broadcast = $this->broadcast->fresh();
        if (! $broadcast) {
            Log::warning('SendBroadcastJob: broadcast not found', ['id' => $this->broadcast->id]);

            return;
        }

        $target = $broadcast->target;
        $channels = $broadcast->channels ?? [];
        $title = $broadcast->title;
        $message = $broadcast->message;

        $count = BroadcastNotificationService::getRecipientsCount($target);
        $broadcast->update(['recipients_count' => $count]);

        $recipients = BroadcastNotificationService::getRecipientsLazy($target);

        foreach ($recipients as $user) {
            if ($this->alreadyProcessed($broadcast->id, $user->id)) {
                continue;
            }

            $this->sendToUser($broadcast, $user, $title, $message, $channels);
            $this->markProcessed($broadcast->id, $user->id);
        }
    }

    private function alreadyProcessed(int $broadcastId, int $userId): bool
    {
        return DB::table('notification_broadcast_recipients')
            ->where('notification_broadcast_id', $broadcastId)
            ->where('user_id', $userId)
            ->exists();
    }

    private function markProcessed(int $broadcastId, int $userId): void
    {
        DB::table('notification_broadcast_recipients')->insertOrIgnore([
            'notification_broadcast_id' => $broadcastId,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function sendToUser(
        NotificationBroadcast $broadcast,
        User $user,
        string $title,
        string $message,
        array $channels
    ): void {
        if (in_array(BroadcastNotificationService::CHANNEL_SYSTEM, $channels, true)) {
            try {
                NotificationService::send([
                    'user_id' => $user->id,
                    'type' => 'admin.broadcast',
                    'title' => $title,
                    'message' => $message,
                    'data' => ['broadcast_id' => $broadcast->id],
                    'required_permission' => null,
                ]);
            } catch (\Throwable $e) {
                Log::error('SendBroadcastJob: system send failed', [
                    'user_id' => $user->id,
                    'broadcast_id' => $broadcast->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (in_array(BroadcastNotificationService::CHANNEL_EMAIL, $channels, true)) {
            if (! empty($user->email) && $user->email_verified_at !== null) {
                try {
                    $user->notify(new BroadcastNotification($title, $message));
                } catch (\Throwable $e) {
                    Log::error('SendBroadcastJob: email send failed', [
                        'user_id' => $user->id,
                        'broadcast_id' => $broadcast->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if (in_array(BroadcastNotificationService::CHANNEL_TELEGRAM, $channels, true)) {
            if ($user->isTelegramConnected()) {
                try {
                    TelegramNotificationService::sendBroadcastToUser($user, $title, $message);
                    usleep(500000); // 0.5 s throttle
                } catch (\Throwable $e) {
                    Log::error('SendBroadcastJob: telegram send failed', [
                        'user_id' => $user->id,
                        'broadcast_id' => $broadcast->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
