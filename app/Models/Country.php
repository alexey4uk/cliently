<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'code_3',
        'name',
        'name_en',
        'calling_code',
        'currency',
        'currency_symbol',
        'ioc',
        'is_active',
        'is_for_phone_select',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_for_phone_select' => 'boolean',
    ];

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Get list of all countries ordered by name.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCached()
    {
        return static::orderBy('name')->get();
    }

    /**
     * Страны для выбора в селекте телефона (только is_for_phone_select = true).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForPhoneSelect()
    {
        return static::where('is_for_phone_select', true)->orderBy('name')->get();
    }

    /**
     * Find country by code.
     *
     * @param  string  $code  Country code
     */
    public static function findByCodeCached(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    /**
     * Find country by phone number prefix (E.164).
     * Matches longest calling_code first (e.g. +375 before +7).
     *
     * @param  string  $e164  Phone in E.164 format, e.g. +375291234567
     * @return static|null
     */
    public static function findByPhonePrefix(string $e164): ?self
    {
        return static::query()
            ->orderByRaw('LENGTH(calling_code) DESC')
            ->get()
            ->first(fn (Country $c) => str_starts_with($e164, $c->calling_code));
    }
}
