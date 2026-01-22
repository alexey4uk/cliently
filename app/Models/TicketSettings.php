<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'enabled',
        'auto_assign_enabled',
        'auto_assign_to_user_id',
        'sla_response_time',
        'public_form_enabled',
        'public_form_required_fields',
        'email_notifications_enabled',
        'email_notification_recipients',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'auto_assign_enabled' => 'boolean',
        'public_form_enabled' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'public_form_required_fields' => 'array',
        'email_notification_recipients' => 'array',
        'sla_response_time' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function autoAssignUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auto_assign_to_user_id');
    }

    /**
     * Получить или создать настройки для бизнеса
     */
    public static function getForBusiness(int $businessId): self
    {
        return self::firstOrCreate(
            ['business_id' => $businessId],
            [
                'enabled' => true,
                'auto_assign_enabled' => false,
                'public_form_enabled' => true,
                'email_notifications_enabled' => true,
                'public_form_required_fields' => ['name', 'email', 'title', 'description'],
                'email_notification_recipients' => [],
            ]
        );
    }
}
