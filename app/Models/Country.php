<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

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
        return Cache::remember('countries_list', 86400, function () {
            return static::orderBy('name')->get();
        });
    }

    /**
     * Get countries for phone select (is_for_phone_select = true, управляется в админке стран).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForPhoneSelect()
    {
        return static::where('is_active', true)
            ->where('is_for_phone_select', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find country by phone number prefix (calling code).
     * Phone may be in format "+375 29 123-45-67" or "375291234567".
     * Uses all countries with calling_code (not only is_active) so that UA, RU, etc. are detected.
     *
     * @param  string  $phone  Full phone number with or without + and spaces
     */
    public static function findByPhonePrefix(string $phone): ?self
    {
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return null;
        }

        $countries = static::whereNotNull('calling_code')->where('calling_code', '!=', '')->get();
        $best = null;
        $bestPrefixLength = 0;

        foreach ($countries as $country) {
            $prefix = preg_replace('/\D/', '', $country->calling_code ?? '');
            if ($prefix !== '' && str_starts_with($digits, $prefix) && strlen($prefix) > $bestPrefixLength) {
                $best = $country;
                $bestPrefixLength = strlen($prefix);
            }
        }

        return $best;
    }

    /**
     * Find country by code.
     *
     * @param  string  $code  Country code
     */
    public static function findByCodeCached(string $code): ?self
    {
        return Cache::remember("country_code_{$code}", 86400, function () use ($code) {
            return static::where('code', $code)->first();
        });
    }

    /**
     * Clear countries cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('countries_list');
    }

    /**
     * Boot method to clear cache on model changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($country) {
            static::clearCache();
            Cache::forget("country_code_{$country->code}");
        });

        static::deleted(function ($country) {
            static::clearCache();
            Cache::forget("country_code_{$country->code}");
        });
    }
}
