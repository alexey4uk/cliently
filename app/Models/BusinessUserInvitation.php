<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BusinessUserInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'email',
        'role',
        'role_id',
        'token',
        'created_by',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the business that owns this invitation.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who created this invitation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the role assigned to this invitation.
     */
    public function businessRole(): BelongsTo
    {
        return $this->belongsTo(BusinessRole::class, 'role_id');
    }

    /**
     * Check if invitation is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if invitation is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Check if invitation is valid (not expired and not accepted).
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isAccepted();
    }

    /**
     * Generate a secure token for invitation.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
