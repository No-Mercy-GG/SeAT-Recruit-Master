<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'immortal_settings';

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];
}
