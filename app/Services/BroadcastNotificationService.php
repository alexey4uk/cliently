<?php

namespace App\Services;

use App\Models\BusinessRole;
use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Notifications\BroadcastNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BroadcastNotificationService
{
    public const TARGET_OWNERS = 'owners';

    public const TARGET_ALL = 'all';

    public const CHANNEL_SYSTEM = 'system';

    public const CHANNEL_EMAIL = 'email';

    public const CHANNEL_TELEGRAM = 'telegram';

    /**
     * Получить получателей рассылки по целевой аудитории.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public static function getRecipients(string $target): \Illuminate\Database\Eloquent\Collection
    {
        if ($target === self::TARGET_OWNERS) {
            $ownerRole = BusinessRole::where('slug', 'owner')->first();
            if (! $ownerRole) {
                return collect();
            }
            $userIds = DB::table('business_user')
                ->where(function ($q) use ($ownerRole) {
                    $q->where('role_id', $ownerRole->id)
                        ->orWhere('role', 'owner');
                })
                ->distinct()
                ->pluck('user_id');

            return User::whereIn('id', $userIds)->get();
        }

        if ($target === self::TARGET_ALL) {
            $userIds = DB::table('business_user')->distinct()->pluck('user_id');

            return User::whereIn('id', $userIds)->get();
        }

        return collect();
    }

    /**
     * Отправить рассылку.
     *
     * @param  array<int, string>  $channels  ['system', 'email', 'telegram']
     */
    public static function send(string $title, string $message, string $target, array $channels, User $sentBy): int
    {
        $validTargets = [self::TARGET_OWNERS, self::TARGET_ALL];
        if (! in_array($target, $validTargets, true)) {
            throw new \InvalidArgumentException("Invalid target: {$target}");
        }

        $validChannels = [self::CHANNEL_SYSTEM, self::CHANNEL_EMAIL, self::CHANNEL_TELEGRAM];
        foreach ($channels as $ch) {
            if (! in_array($ch, $validChannels, true)) {
                throw new \InvalidArgumentException("Invalid channel: {$ch}");
            }
        }
        if (empty($channels)) {
            throw new \InvalidArgumentException('At least one channel required');
        }

        $broadcast = NotificationBroadcast::create([
            'title' => $title,
            'message' => $message,
            'target' => $target,
            'channels' => $channels,
            'sent_by' => $sentBy->id,
            'sent_at' => now(),
            'recipients_count' => 0,
        ]);

        $recipients = self::getRecipients($target);
        $count = $recipients->count();

        foreach ($recipients as $user) {
            if (in_array(self::CHANNEL_SYSTEM, $channels, true)) {
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
                    Log::error('BroadcastNotificationService: system send failed', [
                        'user_id' => $user->id,
                        'broadcast_id' => $broadcast->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (in_array(self::CHANNEL_EMAIL, $channels, true)) {
                if (! empty($user->email) && $user->email_verified_at !== null) {
                    try {
                        $user->notify(new BroadcastNotification($title, $message));
                    } catch (\Throwable $e) {
                        Log::error('BroadcastNotificationService: email send failed', [
                            'user_id' => $user->id,
                            'broadcast_id' => $broadcast->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            if (in_array(self::CHANNEL_TELEGRAM, $channels, true)) {
                if ($user->isTelegramConnected()) {
                    try {
                        TelegramNotificationService::sendBroadcastToUser($user, $title, $message);
                    } catch (\Throwable $e) {
                        Log::error('BroadcastNotificationService: telegram send failed', [
                            'user_id' => $user->id,
                            'broadcast_id' => $broadcast->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        $broadcast->update(['recipients_count' => $count]);

        return $count;
    }
}
