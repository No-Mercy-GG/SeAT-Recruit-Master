<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class IntelEvent extends Model
{
    protected $table = 'immortal_intel_events';

    protected $fillable = [
        'title',
        'severity',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}
