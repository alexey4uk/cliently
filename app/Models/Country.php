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
    ];

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Get cached list of all countries ordered by name.
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
     * Find country by code with caching.
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
