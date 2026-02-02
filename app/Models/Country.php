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
     * Find country by code.
     *
     * @param  string  $code  Country code
     */
    public static function findByCodeCached(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
