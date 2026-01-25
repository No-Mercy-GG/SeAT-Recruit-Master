<?php

namespace Immortal\Suite\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationSession extends Model
{
    protected $table = 'immortal_application_sessions';

    protected $fillable = [
        'user_id',
        'discord_user_id',
        'ticket_id',
        'guild_id',
    ];
}
