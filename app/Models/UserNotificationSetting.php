<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'notification_type',
        'enabled',
        'channels',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'channels' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the notification setting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get or create a notification setting for a user and type.
     */
    public static function getForUser(User $user, string $type): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $user->id,
                'notification_type' => $type,
            ],
            [
                'enabled' => true,
                'channels' => config('notifications.default_channels', []),
            ]
        );
    }

    /**
     * Проверить, включён ли данный тип уведомлений для пользователя.
     * Если тип отключён — не отправляются ни in-app, ни email, ни telegram.
     */
    public function isEnabled(): bool
    {
        return $this->enabled ?? true;
    }

    /**
     * Check if a specific channel is enabled for this setting.
     */
    public function isChannelEnabled(string $channel): bool
    {
        $channels = $this->channels ?? [];

        return $channels[$channel] ?? true; // По умолчанию включено
    }

    /**
     * Update channels for this setting.
     */
    public function updateChannels(array $channels): bool
    {
        return $this->update(['channels' => $channels]);
    }
}
