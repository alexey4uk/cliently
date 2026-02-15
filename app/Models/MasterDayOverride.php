<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDayOverride extends Model
{
    protected $fillable = [
        'master_id',
        'date',
        'is_working',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date' => 'date',
        'is_working' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }
}
