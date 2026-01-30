<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'email',
        'telegram_user_id',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function primaryPhone(): MorphOne
    {
        return $this->morphOne(Phone::class, 'phoneable')->where('type', 'primary');
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->primaryPhone?->phone;
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
