<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessRolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'role',
        'permission',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Get the business that owns this permission (nullable for default permissions).
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope a query to only include default permissions (where business_id is NULL).
     */
    public function scopeDefaults($query)
    {
        return $query->whereNull('business_id');
    }

    /**
     * Scope a query to only include permissions for a specific business.
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include granted permissions.
     */
    public function scopeGranted($query)
    {
        return $query->where('granted', true);
    }
}
