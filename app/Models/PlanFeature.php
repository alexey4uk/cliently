<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'metric_id',
        'value',
    ];

    // value хранится как строка, кастинг выполняется в модели Plan через getFeatureValue()

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function metric(): BelongsTo
    {
        return $this->belongsTo(SubscriptionMetric::class, 'metric_id');
    }
}
