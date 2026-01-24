<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable, HasSubscription;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phone',
        'email',
        'email_verified_at',
        'password',
        'name',
        'avatar',
        'dashboard_settings',
        'telegram_chat_id',
        'telegram_token',
        'oauth_provider',
        'oauth_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dashboard_settings' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            //            'phone' => E164PhoneNumberCast::class.":BY",
        ];
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)
            ->withPivot('role', 'role_id', 'first_name', 'last_name')
            ->withTimestamps();
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function notificationRecords(): HasMany
    {
        return $this->hasMany(NotificationRecord::class);
    }

    public function notificationSettings(): HasMany
    {
        return $this->hasMany(UserNotificationSetting::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (is_null($user->dashboard_settings)) {
                $user->dashboard_settings = [
                    'dashboard' => [
                        'widgets' => [
                            'stats_header' => true,
                            'stat_today' => true,
                            'stat_week' => true,
                            'stat_new_clients' => true,
                            'stat_total_clients' => true,
                            'stat_pending' => true,
                            'stat_completed' => true,
                            'stat_cancelled' => true,
                            'stat_avg_per_day' => true,
                            'quick_actions' => true,
                            'appointments_chart' => true,
                            'clients_chart' => true,
                            'next_appointment' => true,
                            'today_appointments' => true,
                            'pending_appointments' => true,
                            'recent_clients' => true,
                            'weekly_chart' => false,
                        ],
                        'widget_order' => [
                            'stats_header',
                            'quick_actions',
                            'appointments_chart',
                            'clients_chart',
                            'next_appointment',
                            'today_appointments',
                            'pending_appointments',
                            'recent_clients',
                            'weekly_chart',
                        ],
                    ],
                ];
            }

            // Генерируем токен для Telegram, если не задан
            if (is_null($user->telegram_token)) {
                $user->telegram_token = Str::random(32);
            }
        });
    }

    /**
     * Проверить, привязан ли Telegram аккаунт
     */
    public function isTelegramConnected(): bool
    {
        return !empty($this->telegram_chat_id);
    }

    /**
     * Получить URL аватара пользователя
     * Поддерживает как локальные файлы, так и внешние URL (OAuth)
     */
    public function getAvatarUrl(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        // Проверяем, является ли аватар внешним URL (начинается с http:// или https://)
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // Иначе это локальный файл в storage
        return asset('storage/' . $this->avatar);
    }

    /**
     * Проверить, является ли аватар внешним URL
     */
    public function hasExternalAvatar(): bool
    {
        return $this->avatar && filter_var($this->avatar, FILTER_VALIDATE_URL);
    }
}
