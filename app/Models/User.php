<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'name',
        'avatar',
        'dashboard_settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dashboard_settings' => 'array',
            //            'email_verified_at' => 'datetime',
            //            'password' => 'hashed',
            //            'phone' => E164PhoneNumberCast::class.":BY",
        ];
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)
            ->withPivot('role', 'first_name', 'last_name')
            ->withTimestamps();
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (is_null($user->dashboard_settings)) {
                $user->dashboard_settings = [
                    'dashboard' => [
                        'widgets' => [
                            'stats_header' => true,
                            'quick_actions' => true,
                            'next_appointment' => true,
                            'today_appointments' => true,
                            'pending_appointments' => true,
                            'recent_clients' => true,
                            'weekly_chart' => false,
                        ],
                        'widget_order' => [
                            'next_appointment',
                            'today_appointments',
                            'pending_appointments',
                            'recent_clients',
                            'weekly_chart',
                        ],
                    ],
                ];
            }
        });
    }
}
