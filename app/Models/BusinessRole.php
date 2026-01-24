<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
