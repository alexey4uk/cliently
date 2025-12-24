<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Получить полное имя клиента
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    /**
     * Получить инициалы клиента
     */
    public function getInitialsAttribute(): string
    {
        $first = mb_substr($this->first_name, 0, 1, 'UTF-8');
        $last = $this->last_name ? mb_substr($this->last_name, 0, 1, 'UTF-8') : '';

        return mb_strtoupper($first.$last, 'UTF-8');
    }
}
