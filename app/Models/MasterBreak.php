<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterBreak extends Model
{
    protected $fillable = [
        'master_schedule_id',
        'start_time',
        'end_time',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'description' => 'string',
    ];

    public function masterSchedule()
    {
        return $this->belongsTo(MasterSchedule::class);
    }
}
