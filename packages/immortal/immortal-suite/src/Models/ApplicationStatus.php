<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatus extends Model
{
    protected $table = 'immortal_application_statuses';

    protected $fillable = [
        'application_id',
        'user_id',
        'action',
        'status',
        'notes',
    ];
}
