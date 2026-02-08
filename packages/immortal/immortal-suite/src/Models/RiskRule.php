<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class RiskRule extends Model
{
    protected $table = 'immortal_risk_rules';

    protected $fillable = [
        'key',
        'name',
        'enabled',
        'weight',
        'lookback_days',
        'thresholds',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'thresholds' => 'array',
    ];
}
