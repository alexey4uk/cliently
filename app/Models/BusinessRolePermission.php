<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'permission',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Get the role that owns this permission.
     */
    public function role()
    {
        return $this->belongsTo(BusinessRole::class, 'role_id');
    }

    /**
     * Scope a query to filter by role id.
     */
    public function scopeForRole($query, int $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope a query to only include granted permissions.
     */
    public function scopeGranted($query)
    {
        return $query->where('granted', true);
    }
}
