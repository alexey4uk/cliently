<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    //
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'duration',
        'preparation_time',
        'price',
        'is_active',
    ];
}
