<?php

namespace App\Services;

use App\Models\BusinessRole;
use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Jobs\SendBroadcastJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\LazyCollection;
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
     * Базовый запрос получателей по целевой аудитории (owners / all).
     */
    public static function getRecipientsQuery(string $target): Builder
    {
        $subquery = DB::table('business_user')->distinct()->select('user_id');

        if ($target === self::TARGET_OWNERS) {
            $ownerRole = BusinessRole::where('slug', 'owner')->first();
            if (! $ownerRole) {
                $subquery->whereRaw('1 = 0');

                return User::whereIn('id', $subquery);
            }
            $subquery->where(function ($q) use ($ownerRole) {
                $q->where('role_id', $ownerRole->id)
                    ->orWhere('role', 'owner');
            });
        } elseif ($target !== self::TARGET_ALL) {
            $subquery->whereRaw('1 = 0');
        }

        return User::whereIn('id', $subquery);
    }

    /**
     * Получить получателей рассылки по целевой аудитории.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public static function getRecipients(string $target): \Illuminate\Database\Eloquent\Collection
    {
        return self::getRecipientsQuery($target)->get();
    }

    /**
     * Lazy-итерация по получателям (чанк 1000).
     *
     * @return LazyCollection<int, User>
     */
    public static function getRecipientsLazy(string $target): LazyCollection
    {
        return self::getRecipientsQuery($target)->lazy();
    }

    /**
     * Количество получателей по целевой аудитории.
     */
    public static function getRecipientsCount(string $target): int
    {
        return self::getRecipientsQuery($target)->count();
    }

    /**
     * Поставить рассылку в очередь. Отправка выполняется в SendBroadcastJob.
     *
     * @param  array<int, string>  $channels  ['system', 'email', 'telegram']
     */
    public static function send(string $title, string $message, string $target, array $channels, User $sentBy): NotificationBroadcast
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

        SendBroadcastJob::dispatch($broadcast);

        return $broadcast;
    }
}
