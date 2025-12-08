<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'full_description',
        'short_description',
        'slug',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
