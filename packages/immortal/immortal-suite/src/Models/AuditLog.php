<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'immortal_audit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];
}
