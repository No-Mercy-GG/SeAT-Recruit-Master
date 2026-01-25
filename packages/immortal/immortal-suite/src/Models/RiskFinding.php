<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class RiskFinding extends Model
{
    protected $table = 'immortal_risk_findings';

    protected $fillable = [
        'application_id',
        'rule_key',
        'severity',
        'summary',
        'score',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}
