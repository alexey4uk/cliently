<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'phone',
        'phone_country_code',
        'email',
        'telegram_user_id',
        'last_reengagement_sent_at',
    ];

    protected $casts = [
        'last_reengagement_sent_at' => 'datetime',
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

    /**
     * Страна по ISO коду (опционально из справочника; для клиентов храним только код).
     */
    public function getPhoneCountryAttribute(): ?Country
    {
        $code = $this->phone_country_code;
        if (! $code) {
            return null;
        }

        return Country::where('code', strtoupper($code))->first();
    }

    /**
     * Телефон: приоритет у колонки clients.phone, иначе — из morph (обратная совместимость).
     */
    public function getPhoneAttribute(): ?string
    {
        $value = $this->attributes['phone'] ?? null;
        if ($value !== null && $value !== '') {
            return $value;
        }

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
