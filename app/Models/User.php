<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, HasSubscription;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phone',
        'email',
        'password',
        'name',
        'avatar',
        'dashboard_settings',
        'must_change_password',
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
            'must_change_password' => 'boolean',
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

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
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
                            'stat_today' => true,
                            'stat_week' => true,
                            'stat_new_clients' => true,
                            'stat_total_clients' => true,
                            'stat_pending' => true,
                            'stat_completed' => true,
                            'stat_cancelled' => true,
                            'stat_avg_per_day' => true,
                            'quick_actions' => true,
                            'appointments_chart' => true,
                            'clients_chart' => true,
                            'next_appointment' => true,
                            'today_appointments' => true,
                            'pending_appointments' => true,
                            'recent_clients' => true,
                            'weekly_chart' => false,
                        ],
                        'widget_order' => [
                            'stats_header',
                            'quick_actions',
                            'appointments_chart',
                            'clients_chart',
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
