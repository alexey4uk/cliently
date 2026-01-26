<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class BusinessRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_system',
        'owner_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get permissions for this role.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(BusinessRolePermission::class, 'role_id');
    }

    /**
     * Get the owner (user) that owns this role.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Scope a query to only include system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include custom (non-system) roles.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope a query to include system roles and roles for a specific owner.
     */
    public function scopeForOwner($query, ?int $ownerId)
    {
        return $query->where(function ($q) use ($ownerId) {
            $q->where('is_system', true)
                ->orWhere('owner_id', $ownerId);
        });
    }

    /**
     * Get cached role by ID.
     *
     * @param  int  $id  Role ID
     */
    public static function getCached(int $id): ?self
    {
        return Cache::remember("business_role_{$id}", 3600, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Get cached role by slug.
     *
     * @param  string  $slug  Role slug
     */
    public static function getCachedBySlug(string $slug): ?self
    {
        return Cache::remember("business_role_slug_{$slug}", 3600, function () use ($slug) {
            return static::where('slug', $slug)->first();
        });
    }

    /**
     * Get owner role with caching.
     */
    public static function getOwnerRole(): ?self
    {
        return static::getCachedBySlug('owner');
    }

    /**
     * Clear cache for this role.
     */
    public function clearCache(): void
    {
        Cache::forget("business_role_{$this->id}");
        Cache::forget("business_role_slug_{$this->slug}");
    }

    /**
     * Boot method to clear cache on model changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($role) {
            $role->clearCache();
        });

        static::deleted(function ($role) {
            $role->clearCache();
        });
    }
}
