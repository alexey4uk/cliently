<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSchedule extends Model
{
    protected $fillable = [
        "master_id",
        "day_of_week",
        "start_time",
        "end_time",
        "is_working",
    ];

    protected $casts = [
        "day_of_week" => "integer",
        "is_working" => "boolean",
        "start_time" => "datetime:H:i",
        "end_time" => "datetime:H:i",
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(MasterBreak::class);
    }
}
