<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'category_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'token',
        'created_by_type',
        'created_by_id',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->token)) {
                $ticket->token = self::generateToken();
            }
        });

        static::updating(function ($ticket) {
            // Автоматически устанавливаем resolved_at при изменении статуса на resolved
            if ($ticket->isDirty('status') && $ticket->status === 'resolved' && ! $ticket->resolved_at) {
                $ticket->resolved_at = now();
            }

            // Автоматически устанавливаем closed_at при изменении статуса на closed
            if ($ticket->isDirty('status') && $ticket->status === 'closed' && ! $ticket->closed_at) {
                $ticket->closed_at = now();
            }
        });
    }

    /**
     * Генерация уникального токена для публичного доступа
     */
    protected static function generateToken(): string
    {
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $token = Str::random(32);
            $attempts++;

            if ($attempts >= $maxAttempts) {
                $token = Str::random(40);
                break;
            }
        } while (self::where('token', $token)->exists());

        return $token;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Получить создателя тикета
     */
    public function creator(): ?User
    {
        if ($this->created_by_type === 'user' && $this->created_by_id) {
            return User::find($this->created_by_id);
        }

        return null;
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function publicComments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->where('is_internal', false)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->whereNull('comment_id');
    }

    /**
     * Проверить, является ли тикет новым
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Проверить, находится ли тикет в работе
     */
    public function isInProgress(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Проверить, решен ли тикет
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Проверить, закрыт ли тикет
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Получить цвет статуса для UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'blue',
            'open' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Получить цвет приоритета для UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }
}
