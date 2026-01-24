<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'description',
        'slug',
        'telegram_chat_id',
        'telegram_token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($business) {
            if (empty($business->telegram_token)) {
                $business->telegram_token = Str::random(32);
            }
        });
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_user')
            ->withPivot('role', 'role_id', 'first_name', 'last_name', 'master_id')
            ->withTimestamps();
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\HasMany|Business
    {
        return $this->hasMany(Location::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function masters(): HasMany
    {
        return $this->hasMany(Master::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function telegramUserStates(): HasMany
    {
        return $this->hasMany(\App\Models\TelegramUserState::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketSettings(): HasOne
    {
        return $this->hasOne(TicketSettings::class);
    }
}
