<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'immortal_applications';

    protected $fillable = [
        'user_id',
        'status',
        'assigned_to',
        'discord_user_id',
        'ticket_id',
        'guild_id',
        'application_data',
        'notes',
    ];

    protected $casts = [
        'application_data' => 'array',
    ];
}
