<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'amount',
        'currency',
        'status',
        'bepaid_transaction_id',
        'bepaid_payment_token',
        'payment_method',
        'paid_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Проверить, оплачен ли инвойс
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Проверить, ожидает ли инвойс оплаты
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Проверить, провалился ли платеж
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Проверить, отменен ли инвойс
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Проверить, возвращен ли платеж
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Проверить, истек ли срок действия инвойса
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Scope для оплаченных инвойсов
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope для ожидающих оплаты инвойсов
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope для провалившихся платежей
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
